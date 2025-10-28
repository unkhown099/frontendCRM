<?php
session_start();

// Backend: connect to Database singleton
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

// Points rate (fallback) - 1 point per ₱10
$pointsRate = 10;
$pointsRateDisplay = '₱' . $pointsRate . '/pt';

// Date range helpers
$monthStart = date('Y-m-01 00:00:00');
$monthEnd = date('Y-m-t 23:59:59');

// Pagination for recent activities
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 8; // rows per page in the Recent Activity table
$offset = ($page - 1) * $perPage;

$totalTransactions = 0;
$totalPages = 1;
$showStart = 0;
$showEnd = 0;

// Filters (from UI)
$filterTier = $_GET['tier'] ?? 'All'; // All, Bronze, Silver, Gold, Platinum
$filterPeriod = $_GET['period'] ?? 'All'; // All Time, This Month, Last 3 Months, This Year
$filterPointRange = $_GET['pointrange'] ?? 'All'; // All Ranges, 0-99, 100-499, 500-999, 1000+
$filterSort = $_GET['sort'] ?? 'recent'; // recent, highest_points, lowest_points, name_asc
$searchQ = trim($_GET['q'] ?? '');

// compute period bounds if requested
$periodStart = null; $periodEnd = null;
if ($filterPeriod === 'This Month') { $periodStart = $monthStart; $periodEnd = $monthEnd; }
elseif ($filterPeriod === 'Last 3 Months') { $periodStart = date('Y-m-d H:i:s', strtotime('-3 months')); $periodEnd = date('Y-m-d H:i:s'); }
elseif ($filterPeriod === 'This Year') { $periodStart = date('Y-01-01 00:00:00'); $periodEnd = date('Y-12-31 23:59:59'); }

// Detect if loyalty-specific tables exist; otherwise fallback to deriving from sales
$stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
$stmt->execute(['loyalty_transactions']);
$hasLoyaltyTx = $stmt->fetchColumn() > 0;
$stmt->execute(['loyalty_accounts']);
$hasLoyaltyAccounts = $stmt->fetchColumn() > 0;

$totalPointsIssued = 0;
$pointsRedeemed = 0;
$activePoints = 0;
$pointsThisMonth = 0;
$recentActivities = [];
$customersForSelect = [];

try {
    if ($hasLoyaltyTx) {
        // totals from loyalty_transactions
        $stmt = $pdo->query("SELECT IFNULL(SUM(CASE WHEN `Change`>0 THEN `Change` ELSE 0 END),0) FROM loyalty_transactions");
        $totalPointsIssued = (int)$stmt->fetchColumn();

        $stmt = $pdo->query("SELECT IFNULL(SUM(CASE WHEN `Change`<0 THEN -`Change` ELSE 0 END),0) FROM loyalty_transactions");
        $pointsRedeemed = (int)$stmt->fetchColumn();

        if ($hasLoyaltyAccounts) {
            $stmt = $pdo->query("SELECT IFNULL(SUM(points_balance),0) FROM loyalty_accounts");
            $activePoints = (int)$stmt->fetchColumn();
        } else {
            $activePoints = $totalPointsIssued - $pointsRedeemed;
        }

        $stmt = $pdo->prepare("SELECT IFNULL(SUM(CASE WHEN `Change`>0 THEN `Change` ELSE 0 END),0) FROM loyalty_transactions WHERE CreatedAt BETWEEN ? AND ?");
        $stmt->execute([$monthStart, $monthEnd]);
        $pointsThisMonth = (int)$stmt->fetchColumn();

        // total loyalty transactions (for pagination/footer)
        $stmt = $pdo->query("SELECT COUNT(*) FROM loyalty_transactions");
        $totalTransactions = (int)$stmt->fetchColumn();

        // Build WHERE clauses for filtering loyalty transactions
        $where = [];
        $params = [];

        if ($periodStart && $periodEnd) {
            $where[] = 'lt.CreatedAt BETWEEN ? AND ?';
            $params[] = $periodStart;
            $params[] = $periodEnd;
        }

        if ($searchQ !== '') {
            $where[] = '(c.FirstName LIKE ? OR c.LastName LIKE ? OR lt.TransactionID LIKE ?)';
            $params[] = "%$searchQ%";
            $params[] = "%$searchQ%";
            $params[] = "%$searchQ%";
        }

        // Tier filter uses NewBalance if available
        if (in_array($filterTier, ['Bronze','Silver','Gold','Platinum'], true)) {
            switch ($filterTier) {
                case 'Bronze': $where[] = 'lt.NewBalance < ?'; $params[] = 100; break;
                case 'Silver': $where[] = 'lt.NewBalance BETWEEN ? AND ?'; $params[] = 100; $params[] = 499; break;
                case 'Gold': $where[] = 'lt.NewBalance BETWEEN ? AND ?'; $params[] = 500; $params[] = 999; break;
                case 'Platinum': $where[] = 'lt.NewBalance >= ?'; $params[] = 1000; break;
            }
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // total matching loyalty transactions for pagination
        $countSql = "SELECT COUNT(*) FROM loyalty_transactions lt LEFT JOIN customers c ON lt.CustomerID = c.CustomerID $whereSql";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalTransactions = (int)$countStmt->fetchColumn();

        // ordering
        $orderSql = 'lt.CreatedAt DESC';
        if ($filterSort === 'highest_points') $orderSql = 'lt.`Change` DESC';
        elseif ($filterSort === 'lowest_points') $orderSql = 'lt.`Change` ASC';

        $sql = "SELECT lt.TransactionID, lt.CustomerID, lt.`Change` AS points_change, lt.PreviousBalance, lt.NewBalance, lt.Type AS action, lt.SaleAmount, lt.CreatedAt, c.FirstName, c.LastName
            FROM loyalty_transactions lt
            LEFT JOIN customers c ON lt.CustomerID = c.CustomerID
            $whereSql
            ORDER BY $orderSql LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $idx = 1;
        // bind positional params
        foreach ($params as $p) { $stmt->bindValue($idx++, $p); }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // customers for modal select
        if ($hasLoyaltyAccounts) {
            $stmt = $pdo->prepare("SELECT la.CustomerID, c.FirstName, c.LastName, la.points_balance AS PointsBalance FROM loyalty_accounts la JOIN customers c ON la.CustomerID = c.CustomerID ORDER BY c.FirstName LIMIT 200");
            $stmt->execute();
            $customersForSelect = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->prepare("SELECT CustomerID, FirstName, LastName, 0 AS PointsBalance FROM customers ORDER BY FirstName LIMIT 200");
            $stmt->execute();
            $customersForSelect = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else {
        // Fallback: derive points from sales table (1 point per $pointsRate)
        $stmt = $pdo->prepare("SELECT IFNULL(SUM(FLOOR(IFNULL(TotalAmount,0)/?)),0) FROM sales");
        $stmt->execute([$pointsRate]);
        $totalPointsIssued = (int)$stmt->fetchColumn();

        $pointsRedeemed = 0;
        $activePoints = $totalPointsIssued;

        $stmt = $pdo->prepare("SELECT IFNULL(SUM(FLOOR(TotalAmount/?)),0) FROM sales WHERE SaleDate BETWEEN ? AND ?");
        $stmt->execute([$pointsRate, $monthStart, $monthEnd]);
        $pointsThisMonth = (int)$stmt->fetchColumn();

        // Build WHERE clauses for sales-derived activities
        $where = [];
        $params = [];

        if ($periodStart && $periodEnd) {
            $where[] = 's.SaleDate BETWEEN ? AND ?';
            $params[] = $periodStart;
            $params[] = $periodEnd;
        }

        if ($searchQ !== '') {
            $where[] = '(c.FirstName LIKE ? OR c.LastName LIKE ? OR s.SaleID LIKE ?)';
            $params[] = "%$searchQ%";
            $params[] = "%$searchQ%";
            $params[] = "%$searchQ%";
        }

        // tier filter operates on computed points per sale (NewBalance equals points for that single sale)
        if (in_array($filterTier, ['Bronze','Silver','Gold','Platinum'], true)) {
            switch ($filterTier) {
                case 'Bronze': $where[] = 'FLOOR(IFNULL(s.TotalAmount,0)/?) < ?'; $params[] = $pointsRate; $params[] = 100; break;
                case 'Silver': $where[] = 'FLOOR(IFNULL(s.TotalAmount,0)/?) BETWEEN ? AND ?'; $params[] = $pointsRate; $params[] = 100; $params[] = 499; break;
                case 'Gold': $where[] = 'FLOOR(IFNULL(s.TotalAmount,0)/?) BETWEEN ? AND ?'; $params[] = $pointsRate; $params[] = 500; $params[] = 999; break;
                case 'Platinum': $where[] = 'FLOOR(IFNULL(s.TotalAmount,0)/?) >= ?'; $params[] = $pointsRate; $params[] = 1000; break;
            }
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // total matching sales rows
        $countSql = "SELECT COUNT(*) FROM sales s LEFT JOIN customers c ON s.CustomerID = c.CustomerID $whereSql";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalTransactions = (int)$countStmt->fetchColumn();

        // ordering
        $orderSql = 's.SaleDate DESC';
        if ($filterSort === 'highest_points') $orderSql = 'FLOOR(IFNULL(s.TotalAmount,0)/' . intval($pointsRate) . ') DESC';
        elseif ($filterSort === 'lowest_points') $orderSql = 'FLOOR(IFNULL(s.TotalAmount,0)/' . intval($pointsRate) . ') ASC';

        $sql = "SELECT s.SaleID AS TransactionID, s.CustomerID, FLOOR(IFNULL(s.TotalAmount,0)/?) AS points_change, 0 AS PreviousBalance, FLOOR(IFNULL(s.TotalAmount,0)/?) AS NewBalance, 'Earned' AS action, s.TotalAmount AS SaleAmount, s.SaleDate AS CreatedAt, c.FirstName, c.LastName
            FROM sales s
            LEFT JOIN customers c ON s.CustomerID = c.CustomerID
            $whereSql
            ORDER BY $orderSql LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $idx = 1;
        // bind the two pointsRate params used in SELECT
        $stmt->bindValue($idx++, $pointsRate, PDO::PARAM_INT);
        $stmt->bindValue($idx++, $pointsRate, PDO::PARAM_INT);
        // bind remaining where params (if any)
        foreach ($params as $p) { $stmt->bindValue($idx++, $p); }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT CustomerID, FirstName, LastName FROM customers ORDER BY FirstName LIMIT 200");
        $stmt->execute();
        $customersForSelect = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // On error, keep defaults and allow page to render with zeros
}

// Normalize recent activities to UI shape expected below
$activities = [];
foreach ($recentActivities as $r) {
    $customerName = trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')) ?: 'Customer';
    $initials = '';
    $parts = preg_split('/\s+/', $customerName);
    foreach ($parts as $p) { $initials .= strtoupper(substr($p,0,1)); }
    $pointsChange = (int)($r['points_change'] ?? 0);
    $prev = isset($r['PreviousBalance']) ? (int)$r['PreviousBalance'] : 0;
    $new = isset($r['NewBalance']) ? (int)$r['NewBalance'] : $prev + $pointsChange;
    $saleAmount = isset($r['SaleAmount']) ? (float)$r['SaleAmount'] : 0.0;
    $dateLabel = isset($r['CreatedAt']) ? date('M d, Y', strtotime($r['CreatedAt'])) : '';
    // Determine tier from new balance (fallback)
    $tier = 'Bronze';
    if ($new >= 1000) $tier = 'Platinum';
    elseif ($new >= 500) $tier = 'Gold';
    elseif ($new >= 100) $tier = 'Silver';

    $activities[] = [
        'id' => isset($r['TransactionID']) ? ('#TXN-' . $r['TransactionID']) : '#TXN-0',
        'customer' => $customerName,
        'initials' => $initials,
        'action' => $r['action'] ?? 'Earned',
        'points_change' => $pointsChange,
        'prev_balance' => $prev,
        'new_balance' => $new,
        'tier' => $tier,
        'sale_amount' => $saleAmount ? ('₱' . number_format($saleAmount,2)) : '-',
        'date' => $dateLabel
    ];
}

// Prepare pagination display vars
$totalPages = $perPage > 0 ? max(1, (int)ceil($totalTransactions / $perPage)) : 1;
$showStart = ($totalTransactions > 0) ? ($offset + 1) : 0;
$showEnd = $offset + count($activities);
if ($showEnd > $totalTransactions) $showEnd = $totalTransactions;

// Prepare stats array used by UI
$stats_dynamic = [
    ['icon'=>'🎁','value'=>number_format($totalPointsIssued),'label'=>'Total Points Issued','sublabel'=>'All time','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#ddd6fe'],
    ['icon'=>'💰','value'=>number_format($pointsRedeemed),'label'=>'Points Redeemed','sublabel'=>'This year','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#d1fae5'],
    ['icon'=>'🏆','value'=>number_format($activePoints),'label'=>'Active Points','sublabel'=>'Available for redemption','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#fef3c7'],
    ['icon'=>'⭐','value'=>$pointsRateDisplay,'label'=>'Points Rate','sublabel'=>"1 point per ₱{$pointsRate} spent",'trend'=>'Standard','trend_dir'=>'up','color'=>'#dbeafe'],
    ['icon'=>'📈','value'=>number_format($pointsThisMonth),'label'=>'Points This Month','sublabel'=>'From recent sales','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#e9d5ff']
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loyalty Program - CRM System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/crmGlobalStyles.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <div class="brand">
                <div class="brand-icon">C</div>
                <span>CRM Enterprise</span>
            </div>
            <ul class="nav-menu">
                <li><a href="#">Dashboard</a></li>
                <li><a href="./customerProfile.php">Customer Profiles</a></li>
                <li><a href="./loyaltyProgram.php" class="active">Loyalty Program</a></li>
                <li><a href="./customerSupport.php">Customer Support</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" class="search-box" placeholder="Search customers...">
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notification-badge">5</span>
                </button>
                <a href="./crmProfile.php"><div class="user-avatar">SM</div></a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="breadcrumb">
                    <span>Home</span>
                    <span class="breadcrumb-separator">/</span>
                    <span>Customer Management</span>
                    <span class="breadcrumb-separator">/</span>
                    <span>Loyalty Program</span>
                </div>
                <h1 class="page-title">Loyalty Program</h1>
                <p class="page-subtitle">Manage customer loyalty points and rewards program</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>⚙️</span>
                    <span>Program Settings</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Export Report</span>
                </button>
                <button class="btn btn-primary" onclick="openAdjustModal()">
                    <span>✏️</span>
                    <span>Adjust Points</span>
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <?php
            // Use computed stats from backend
            $stats = $stats_dynamic;

            foreach ($stats as $stat) {
                echo '<div class="stat-card">';
                echo '<div class="stat-header">';
                echo '<div class="stat-icon" style="background: ' . $stat['color'] . ';">' . $stat['icon'] . '</div>';
                echo '<div class="stat-trend ' . $stat['trend_dir'] . '">';
                echo '<span>' . ($stat['trend_dir'] === 'up' && $stat['trend'] !== 'Standard' ? '↑' : '') . '</span>';
                echo '<span>' . $stat['trend'] . '</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="stat-value">' . $stat['value'] . '</div>';
                echo '<div class="stat-label">' . $stat['label'] . '</div>';
                echo '<div class="stat-sublabel">' . $stat['sublabel'] . '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Loyalty Tiers Section -->
        <div class="content-card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h2 class="section-title">Loyalty Tier Structure</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Edit Tiers">✏️</button>
                </div>
            </div>
            <div style="padding: 24px;">
                <div class="tier-structure">
                    <?php
                    // Compute current members per tier from DB (loyalty_accounts if available, otherwise derive from sales totals)
                    $tierCounts = ['Bronze' => 0, 'Silver' => 0, 'Gold' => 0, 'Platinum' => 0];
                    try {
                        if ($hasLoyaltyAccounts) {
                            // Use loyalty_accounts.points_balance
                            $q = $pdo->query("SELECT
                                SUM(CASE WHEN points_balance < 100 THEN 1 ELSE 0 END) AS bronze,
                                SUM(CASE WHEN points_balance BETWEEN 100 AND 499 THEN 1 ELSE 0 END) AS silver,
                                SUM(CASE WHEN points_balance BETWEEN 500 AND 999 THEN 1 ELSE 0 END) AS gold,
                                SUM(CASE WHEN points_balance >= 1000 THEN 1 ELSE 0 END) AS platinum
                                FROM loyalty_accounts");
                            $r = $q->fetch(PDO::FETCH_ASSOC);
                            if ($r) {
                                $tierCounts['Bronze'] = (int)$r['bronze'];
                                $tierCounts['Silver'] = (int)$r['silver'];
                                $tierCounts['Gold'] = (int)$r['gold'];
                                $tierCounts['Platinum'] = (int)$r['platinum'];
                            }
                        } else {
                            // derive points per customer from sales
                            $stmt = $pdo->prepare("SELECT
                                SUM(CASE WHEN pts < 100 THEN 1 ELSE 0 END) AS bronze,
                                SUM(CASE WHEN pts BETWEEN 100 AND 499 THEN 1 ELSE 0 END) AS silver,
                                SUM(CASE WHEN pts BETWEEN 500 AND 999 THEN 1 ELSE 0 END) AS gold,
                                SUM(CASE WHEN pts >= 1000 THEN 1 ELSE 0 END) AS platinum
                                FROM (
                                    SELECT s.CustomerID, SUM(FLOOR(IFNULL(s.TotalAmount,0)/?)) AS pts
                                    FROM sales s
                                    GROUP BY s.CustomerID
                                ) x");
                            $stmt->execute([$pointsRate]);
                            $r = $stmt->fetch(PDO::FETCH_ASSOC);
                            if ($r) {
                                $tierCounts['Bronze'] = (int)$r['bronze'];
                                $tierCounts['Silver'] = (int)$r['silver'];
                                $tierCounts['Gold'] = (int)$r['gold'];
                                $tierCounts['Platinum'] = (int)$r['platinum'];
                            }
                        }
                    } catch (Exception $e) {
                        // keep zeros on error
                    }

                    $tiers = [
                        ['name'=>'Bronze','icon'=>'🥉','range'=>'0 - 99 points','benefits'=>['1 point per ₱10','Basic rewards','Birthday discount'],'members'=>$tierCounts['Bronze'],'color'=>'#fed7aa'],
                        ['name'=>'Silver','icon'=>'🥈','range'=>'100 - 499 points','benefits'=>['1.2 points per ₱10','Priority support','Exclusive offers','Free shipping'],'members'=>$tierCounts['Silver'],'color'=>'#e5e7eb'],
                        ['name'=>'Gold','icon'=>'🥇','range'=>'500 - 999 points','benefits'=>['1.5 points per ₱10','VIP support','Early access','Special events','Gift wrapping'],'members'=>$tierCounts['Gold'],'color'=>'#fde68a'],
                        ['name'=>'Platinum','icon'=>'💎','range'=>'1000+ points','benefits'=>['2 points per ₱10','Dedicated manager','Premium gifts','Exclusive launches','Personal shopping'],'members'=>$tierCounts['Platinum'],'color'=>'#c7d2fe']
                    ];

                    echo '<div class="tiers-grid">';
                    foreach ($tiers as $tier) {
                        echo '<div class="tier-card" style="border-top: 4px solid ' . $tier['color'] . ';">';
                        echo '<div class="tier-card-header">';
                        echo '<span class="tier-icon">' . $tier['icon'] . '</span>';
                        echo '<div>';
                        echo '<h3 class="tier-name">' . $tier['name'] . '</h3>';
                        echo '<p class="tier-range">' . $tier['range'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="tier-benefits">';
                        foreach ($tier['benefits'] as $benefit) {
                            echo '<div class="tier-benefit">✓ ' . $benefit . '</div>';
                        }
                        echo '</div>';
                        echo '<div class="tier-members">' . number_format($tier['members']) . ' members</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                    ?>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
                <div class="filters-header">
                <div class="filters-title">🔍 Filter Loyalty Data</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;" id="resetFiltersBtn">Reset Filters</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Loyalty Tier</label>
                    <select class="filter-select" name="tier">
                        <option value="All" <?= ($filterTier === 'All' ? 'selected' : '') ?>>All Tiers</option>
                        <option value="Bronze" <?= ($filterTier === 'Bronze' ? 'selected' : '') ?>>Bronze</option>
                        <option value="Silver" <?= ($filterTier === 'Silver' ? 'selected' : '') ?>>Silver</option>
                        <option value="Gold" <?= ($filterTier === 'Gold' ? 'selected' : '') ?>>Gold</option>
                        <option value="Platinum" <?= ($filterTier === 'Platinum' ? 'selected' : '') ?>>Platinum</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Activity Period</label>
                    <select class="filter-select" name="period">
                        <option value="All" <?= ($filterPeriod === 'All' ? 'selected' : '') ?>>All Time</option>
                        <option value="This Month" <?= ($filterPeriod === 'This Month' ? 'selected' : '') ?>>This Month</option>
                        <option value="Last 3 Months" <?= ($filterPeriod === 'Last 3 Months' ? 'selected' : '') ?>>Last 3 Months</option>
                        <option value="This Year" <?= ($filterPeriod === 'This Year' ? 'selected' : '') ?>>This Year</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Point Range</label>
                    <select class="filter-select" name="pointrange">
                        <option value="All" <?= ($filterPointRange === 'All' ? 'selected' : '') ?>>All Ranges</option>
                        <option value="0-99" <?= ($filterPointRange === '0-99' ? 'selected' : '') ?>>0-99 pts</option>
                        <option value="100-499" <?= ($filterPointRange === '100-499' ? 'selected' : '') ?>>100-499 pts</option>
                        <option value="500-999" <?= ($filterPointRange === '500-999' ? 'selected' : '') ?>>500-999 pts</option>
                        <option value="1000+" <?= ($filterPointRange === '1000+' ? 'selected' : '') ?>>1000+ pts</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Sort By</label>
                    <select class="filter-select" name="sort">
                        <option value="highest_points" <?= ($filterSort === 'highest_points' ? 'selected' : '') ?>>Highest Points</option>
                        <option value="lowest_points" <?= ($filterSort === 'lowest_points' ? 'selected' : '') ?>>Lowest Points</option>
                        <option value="recent" <?= ($filterSort === 'recent' ? 'selected' : '') ?>>Recent Activity</option>
                        <option value="name_asc" <?= ($filterSort === 'name_asc' ? 'selected' : '') ?>>Name A-Z</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Recent Points Activity Table -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Recent Points Activity</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Filter">🔽</button>
                    <button class="icon-btn" title="Refresh">🔄</button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Customer</th>
                            <th>Action</th>
                            <th>Points Change</th>
                            <th>Previous Balance</th>
                            <th>New Balance</th>
                            <th>Tier</th>
                            <th>Sale Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Activities prepared by backend (from loyalty_transactions or derived from sales)
                        // $activities was built at the top of the file

                        foreach ($activities as $activity) {
                            // determine CSS classes
                            $tierClass = match($activity['tier']) {
                                'Platinum' => 'tier-platinum',
                                'Gold' => 'tier-gold',
                                'Silver' => 'tier-silver',
                                default => 'tier-bronze'
                            };

                            $actionClass = match($activity['action']) {
                                'Earned' => 'action-earned',
                                'Redeemed' => 'action-redeemed',
                                default => 'action-adjusted'
                            };

                            $pointsClass = ($activity['points_change'] ?? 0) > 0 ? 'points-positive' : 'points-negative';
                            ?>
                            <tr>
                                <td><span class="contact-id"><?= htmlspecialchars($activity['id']) ?></span></td>
                                <td>
                                    <div class="contact-name-cell">
                                        <div class="contact-avatar"><?= htmlspecialchars($activity['initials']) ?></div>
                                        <div class="contact-name-info">
                                            <div class="contact-name-primary"><?= htmlspecialchars($activity['customer']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="action-badge <?= $actionClass ?>"><?= htmlspecialchars($activity['action']) ?></span></td>
                                <td><span class="<?= $pointsClass ?>"><?= ($activity['points_change'] > 0 ? '+' : '') . number_format($activity['points_change']) ?> pts</span></td>
                                <td><?= number_format($activity['prev_balance']) ?> pts</td>
                                <td><strong><?= number_format($activity['new_balance']) ?> pts</strong></td>
                                <td><span class="tier-badge <?= $tierClass ?>"><?= htmlspecialchars($activity['tier']) ?></span></td>
                                <td><?= htmlspecialchars($activity['sale_amount']) ?></td>
                                <td><?= htmlspecialchars($activity['date']) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn">View</button>
                                        <button class="action-btn">Receipt</button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <div class="showing-text">
                    <?php
                    // Render showing range and total transactions
                    $start = $showStart;
                    $end = $showEnd;
                    echo 'Showing <strong>' . number_format($start) . '-' . number_format($end) . '</strong> of <strong>' . number_format($totalTransactions) . '</strong> transactions';
                    ?>
                </div>
                <div class="pagination">
                    <?php
                    // preserve other GET params when generating pagination links
                    $baseParams = $_GET;
                    unset($baseParams['page']);
                    $baseQs = http_build_query($baseParams);
                    $hrefPrefix = $baseQs ? ('?' . $baseQs . '&') : '?';
                    if ($page > 1): ?>
                        <a href="<?= $hrefPrefix ?>page=<?= $page - 1 ?>"><button>‹</button></a>
                    <?php else: ?>
                        <button disabled>‹</button>
                    <?php endif; ?>

                    <?php
                    // Show up to $maxPagesToShow page links
                    $maxPagesToShow = 9;
                    $startPage = 1;
                    $endPage = min($totalPages, $maxPagesToShow);
                    for ($p = $startPage; $p <= $endPage; $p++):
                    ?>
                        <a href="<?= $hrefPrefix ?>page=<?= $p ?>"><button class="<?= $p === $page ? 'active' : '' ?>"><?= $p ?></button></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= $hrefPrefix ?>page=<?= $page + 1 ?>"><button>›</button></a>
                    <?php else: ?>
                        <button disabled>›</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Adjust Points Modal -->
    <div class="modal-overlay" id="adjustModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Adjust Loyalty Points</h3>
                <button class="close-btn" onclick="closeAdjustModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="adjustForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Select Customer *</label>
                            <select class="filter-select" name="customer_id" required>
                                <option value="">Choose customer</option>
                                <?php
                                // Populate customers from backend
                                foreach ($customersForSelect as $c) {
                                    $cid = $c['CustomerID'] ?? $c['CustomerID'];
                                    $name = trim(($c['FirstName'] ?? '') . ' ' . ($c['LastName'] ?? ''));
                                    $points = isset($c['PointsBalance']) ? number_format($c['PointsBalance']) . ' pts' : '';
                                    echo '<option value="' . htmlspecialchars($cid) . '">' . htmlspecialchars($name) . ($points ? ' - Current: ' . $points : '') . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adjustment Type *</label>
                            <select class="filter-select" name="adjustment_type" required>
                                <option value="">Select type</option>
                                <option value="add">Add Points</option>
                                <option value="subtract">Subtract Points</option>
                                <option value="set">Set Points</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Points Amount *</label>
                            <input type="number" class="form-input" name="points" placeholder="0" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Reason *</label>
                            <select class="filter-select" name="reason" required>
                                <option value="">Select reason</option>
                                <option value="promotion">Promotional Bonus</option>
                                <option value="correction">Correction</option>
                                <option value="compensation">Customer Compensation</option>
                                <option value="birthday">Birthday Gift</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" name="notes" placeholder="Add notes about this adjustment..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAdjustModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveAdjustment()">Apply Adjustment</button>
            </div>
        </div>
    </div>

    <style>
        /* Tier Structure Styling */
        .tiers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .tier-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .tier-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .tier-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .tier-icon {
            font-size: 32px;
        }

        .tier-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 2px;
        }

        .tier-range {
            font-size: 13px;
            color: var(--gray-600);
        }

        .tier-benefits {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
        }

        .tier-benefit {
            font-size: 13px;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tier-members {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
        }

        /* Action Badges */
        .action-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .action-earned {
            background: #d1fae5;
            color: #065f46;
        }

        .action-redeemed {
            background: #fef3c7;
            color: #92400e;
        }

        .action-adjusted {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Points Display */
        .points-positive {
            color: var(--success);
            font-weight: 700;
            font-size: 14px;
        }

        .points-negative {
            color: var(--danger);
            font-weight: 700;
            font-size: 14px;
        }

        /* Tier Badges */
        .tier-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .tier-platinum {
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #4338ca;
        }

        .tier-gold {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        .tier-silver {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #4b5563;
        }

        .tier-bronze {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #9a3412;
        }
    </style>

    <script>
        // Modal Functions
        function openAdjustModal() {
            document.getElementById('adjustModal').classList.add('active');
        }

        function closeAdjustModal() {
            document.getElementById('adjustModal').classList.remove('active');
            document.getElementById('adjustForm').reset();
        }

        function saveAdjustment() {
            const form = document.getElementById('adjustForm');
            if (form.checkValidity()) {
                alert('Points adjustment applied successfully!');
                closeAdjustModal();
                // Here you would send data to PHP backend
            } else {
                form.reportValidity();
            }
        }

        // Close modal on overlay click
        document.getElementById('adjustModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAdjustModal();
            }
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
        });

        // Pagination
        document.querySelectorAll('.pagination button').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.textContent !== '‹' && this.textContent !== '›') {
                    document.querySelectorAll('.pagination button').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Filter change listeners - build query and reload page
        function applyFilters() {
            const params = new URLSearchParams(window.location.search);
            const tier = document.querySelector('select[name="tier"]')?.value || '';
            const period = document.querySelector('select[name="period"]')?.value || '';
            const pointrange = document.querySelector('select[name="pointrange"]')?.value || '';
            const sort = document.querySelector('select[name="sort"]')?.value || '';
            const q = document.querySelector('.search-box')?.value || '';

            if (tier) params.set('tier', tier); else params.delete('tier');
            if (period) params.set('period', period); else params.delete('period');
            if (pointrange) params.set('pointrange', pointrange); else params.delete('pointrange');
            if (sort) params.set('sort', sort); else params.delete('sort');
            if (q) params.set('q', q); else params.delete('q');
            params.delete('page'); // reset to first page when filters change
            window.location.search = params.toString();
        }

        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', applyFilters);
        });

        // If search box is used (Enter), apply filters
        const searchBox2 = document.querySelector('.search-box');
        searchBox2.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters();
            }
        });

        // Reset filters button
        const resetBtn = document.getElementById('resetFiltersBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                // Navigate to base page without any query string
                window.location.href = window.location.pathname;
            });
        }

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                
                if (action === 'View') {
                    alert('View transaction details');
                } else if (action === 'Receipt') {
                    alert('Generate receipt');
                }
            });
        });
    </script>
</body>
</html>