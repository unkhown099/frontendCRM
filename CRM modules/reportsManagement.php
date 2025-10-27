<?php
session_start();

// Include DB config and get PDO
require_once(__DIR__ . '/../config/database.php');
try {
    $pdo = Database::getInstance()->getConnection();
} catch (Exception $e) {
    die('Database connection error: ' . $e->getMessage());
}

// Simple auth redirect (adjust to your auth flow)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Helper: parse requested range
function getDateRange($range) {
    $now = new DateTime();
    switch ($range) {
        case 'last_month':
            $start = (new DateTime('first day of last month'))->setTime(0,0,0);
            $end = (new DateTime('last day of last month'))->setTime(23,59,59);
            break;
        case 'this_quarter':
            $quarter = ceil($now->format('n')/3);
            $start = new DateTime(($quarter*3-2) . '/1/' . $now->format('Y'));
            $end = (clone $start)->modify('+2 months')->modify('last day of')->setTime(23,59,59);
            break;
        case 'last_quarter':
            $quarter = ceil($now->format('n')/3) - 1;
            if ($quarter < 1) { $quarter = 4; $year = $now->format('Y') - 1; } else { $year = $now->format('Y'); }
            $start = new DateTime((($quarter*3-2) . '/1/' . $year));
            $end = (clone $start)->modify('+2 months')->modify('last day of')->setTime(23,59,59);
            break;
        case 'this_year':
            $start = new DateTime($now->format('Y') . '-01-01');
            $end = (new DateTime($now->format('Y') . '-12-31'))->setTime(23,59,59);
            break;
        case 'custom':
            // expecting ?start=YYYY-MM-DD&end=YYYY-MM-DD
            if (!empty($_GET['start']) && !empty($_GET['end'])) {
                $start = new DateTime($_GET['start']); $start->setTime(0,0,0);
                $end = new DateTime($_GET['end']); $end->setTime(23,59,59);
                break;
            }
            // fallback to this month
        case 'this_month':
        default:
            $start = (new DateTime('first day of this month'))->setTime(0,0,0);
            $end = (new DateTime('last day of this month'))->setTime(23,59,59);
            break;
    }
    return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
}

$range = $_GET['range'] ?? 'this_month';
list($startDate, $endDate) = getDateRange($range);

// Fetch stats using ERPSCHEMA tables
// Total Revenue
$stmt = $pdo->prepare("SELECT IFNULL(SUM(TotalAmount),0) AS total FROM sales WHERE SaleDate BETWEEN ? AND ?");
$stmt->execute([$startDate, $endDate]);
$totalRevenue = (float)$stmt->fetchColumn();

// Orders Closed (count of sales)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE SaleDate BETWEEN ? AND ?");
$stmt->execute([$startDate, $endDate]);
$ordersClosed = (int)$stmt->fetchColumn();

// Avg Order Value
$avgOrderValue = $ordersClosed > 0 ? ($totalRevenue / $ordersClosed) : 0;

// Avg items per order
$stmt = $pdo->prepare("SELECT AVG(item_count) FROM (SELECT COUNT(*) AS item_count FROM saledetails sd JOIN sales s ON sd.SaleID = s.SaleID WHERE s.SaleDate BETWEEN ? AND ? GROUP BY sd.SaleID) t");
$stmt->execute([$startDate, $endDate]);
$avgItemsPerOrder = $stmt->fetchColumn() ?: 0;

// Avg sales cycle (days): approximate as avg days between customer creation and sale
$stmt = $pdo->prepare("SELECT AVG(DATEDIFF(s.SaleDate, c.CreatedAt)) FROM sales s JOIN customers c ON s.CustomerID = c.CustomerID WHERE s.SaleDate BETWEEN ? AND ? AND c.CreatedAt IS NOT NULL");
$stmt->execute([$startDate, $endDate]);
$avgSalesCycleDays = (int)round($stmt->fetchColumn() ?: 0);

// Monthly revenue for current year (used in chart)
$year = (int)date('Y');
$stmt = $pdo->prepare("SELECT MONTH(SaleDate) AS m, IFNULL(SUM(TotalAmount),0) AS total FROM sales WHERE YEAR(SaleDate)=? GROUP BY MONTH(SaleDate) ORDER BY MONTH(SaleDate)");
$stmt->execute([$year]);
$monthlyRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Paid percentage by month (for conversion-like trend)
$stmt = $pdo->prepare("SELECT MONTH(SaleDate) AS m,
    SUM(CASE WHEN PaymentStatus='Paid' THEN 1 ELSE 0 END) AS paid_count,
    COUNT(*) AS total_count
    FROM sales WHERE YEAR(SaleDate)=? GROUP BY MONTH(SaleDate) ORDER BY MONTH(SaleDate)");
$stmt->execute([$year]);
$paidRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare charts data arrays
$monthsLabels = [];
$monthlyValues = [];
for ($m=1;$m<=12;$m++){
    $monthsLabels[] = date('M', mktime(0,0,0,$m,1));
    $monthlyValues[] = isset($monthlyRows[$m]) ? (float)$monthlyRows[$m] : 0;
}

$paidPercentages = [];
$paidByMonth = [];
foreach ($paidRows as $r) {
    $paidByMonth[(int)$r['m']] = $r['total_count']>0 ? round(($r['paid_count']/$r['total_count'])*100,1) : 0;
}
for ($m=1;$m<=12;$m++) {
    $paidPercentages[] = $paidByMonth[$m] ?? 0;
}

// Recent 'reports' generated from recent sales (map to a report-like list)
$stmt = $pdo->prepare("SELECT s.SaleID, s.SaleDate, s.TotalAmount, u.FirstName, u.LastName
    FROM sales s
    LEFT JOIN users u ON s.SalespersonID = u.UserID
    ORDER BY s.SaleDate DESC LIMIT 10");
$stmt->execute();
$recentReports = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reports Management</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:16px}
        .stats-grid{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px}
        .stat-card{background:#fff;border:1px solid #eee;padding:12px;border-radius:6px;min-width:180px;box-shadow:0 1px 2px rgba(0,0,0,0.03)}
        .stat-icon{width:40px;height:40px;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;margin-right:8px}
        .stat-header{display:flex;align-items:center;justify-content:space-between}
        .stat-value{font-size:18px;font-weight:700;margin-top:8px}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{padding:8px;border:1px solid #eee;text-align:left}
        th{background:#fafafa}
    </style>
</head>
<body>

<h2>Reports & Analytics</h2>

<div>
    <form method="get" style="display:flex;gap:8px;align-items:center">
        <label for="range">Date Range</label>
        <select id="range" name="range">
            <option value="this_month" <?php if($range=='this_month') echo 'selected';?>>This Month</option>
            <option value="last_month" <?php if($range=='last_month') echo 'selected';?>>Last Month</option>
            <option value="this_quarter" <?php if($range=='this_quarter') echo 'selected';?>>This Quarter</option>
            <option value="last_quarter" <?php if($range=='last_quarter') echo 'selected';?>>Last Quarter</option>
            <option value="this_year" <?php if($range=='this_year') echo 'selected';?>>This Year</option>
            <option value="custom" <?php if($range=='custom') echo 'selected';?>>Custom</option>
        </select>
        <button type="submit">Apply</button>
    </form>
</div>

<div class="stats-grid">
    <?php
    $stats = [
        ['icon'=>'💰','value'=>'₱' . number_format($totalRevenue,2),'label'=>'Total Revenue','sublabel'=>'Period: ' . date('M d', strtotime($startDate)) . ' - ' . date('M d', strtotime($endDate))],
        ['icon'=>'📈','value'=>number_format($ordersClosed),'label'=>'Orders','sublabel'=>'Completed orders in range'],
        ['icon'=>'🎯','value'=> '₱' . number_format($avgOrderValue,2),'label'=>'Avg Order Value','sublabel'=>'Avg per order'],
        ['icon'=>'⏱️','value'=>($avgSalesCycleDays ? $avgSalesCycleDays . ' days' : 'N/A'),'label'=>'Avg. Sales Cycle','sublabel'=>'Customer to sale (approx)']
    ];
    foreach($stats as $s) {
        echo '<div class="stat-card">';
        echo '<div style="display:flex;align-items:center">';
        echo '<div class="stat-icon">' . $s['icon'] . '</div>';
        echo '<div>';
        echo '<div class="stat-value">' . $s['value'] . '</div>';
        echo '<div style="color:#666">' . $s['label'] . '</div>';
        echo '<div style="font-size:12px;color:#999">' . $s['sublabel'] . '</div>';
        echo '</div></div></div>';
    }
    ?>
</div>

<h3>Recent Reports (Sales)</h3>
<table>
    <thead>
        <tr><th>Sale ID</th><th>Date</th><th>Amount</th><th>Salesperson</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php if(empty($recentReports)) { echo '<tr><td colspan="5">No recent sales found</td></tr>'; } else {
        foreach($recentReports as $r) {
            $person = trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')) ?: '—';
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['SaleID']) . '</td>';
            echo '<td>' . htmlspecialchars($r['SaleDate']) . '</td>';
            echo '<td>₱' . number_format($r['TotalAmount'],2) . '</td>';
            echo '<td>' . htmlspecialchars($person) . '</td>';
            echo '<td><a href="salesView.php?id=' . urlencode($r['SaleID']) . '">View</a> | <a href="exportSale.php?id=' . urlencode($r['SaleID']) . '">Export</a></td>';
            echo '</tr>';
        }
    }
    ?>
    </tbody>
</table>

</body>
</html>
