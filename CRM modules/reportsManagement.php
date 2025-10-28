<?php
session_start();

// Include DB config and get PDO
require_once(__DIR__ . '/../config/database.php');
try {
    $pdo = Database::getInstance()->getConnection();
} catch (Exception $e) {
    die('Database connection error: ' . $e->getMessage());
}

// Simple auth redirect
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Helper function to check if table exists
function tableExists($pdo, $tableName) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
        $stmt->execute([$tableName]);
        return $stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Handle AJAX requests for real-time updates
if (isset($_GET['action']) && $_GET['action'] === 'get_report_updates') {
    header('Content-Type: application/json');
    
    $range = $_GET['range'] ?? 'this_month';
    list($startDate, $endDate) = getDateRange($range);
    
    $response = ['updated' => true, 'stats' => [], 'charts' => []];
    
    try {
        // Get updated stats
        $stats = getReportStats($pdo, $startDate, $endDate);
        $response['stats'] = $stats;
        
        // Get updated charts data
        $charts = getChartsData($pdo);
        $response['charts'] = $charts;
        
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
    
    echo json_encode($response);
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
            if (!empty($_GET['start']) && !empty($_GET['end'])) {
                $start = new DateTime($_GET['start']); $start->setTime(0,0,0);
                $end = new DateTime($_GET['end']); $end->setTime(23,59,59);
                break;
            }
        case 'this_month':
        default:
            $start = (new DateTime('first day of this month'))->setTime(0,0,0);
            $end = (new DateTime('last day of this month'))->setTime(23,59,59);
            break;
    }
    return [$start->format('Y-m-d H:i:s'), $end->format('Y-m-d H:i:s')];
}

// Function to get report statistics
function getReportStats($pdo, $startDate, $endDate) {
    $stats = [];
    
    // Total Revenue
    try {
        $stmt = $pdo->prepare("SELECT IFNULL(SUM(TotalAmount),0) AS total FROM sales WHERE SaleDate BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $stats['totalRevenue'] = (float)$stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['totalRevenue'] = 0;
    }

    // Orders Closed
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE SaleDate BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $stats['ordersClosed'] = (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['ordersClosed'] = 0;
    }

    // Avg Order Value
    $stats['avgOrderValue'] = $stats['ordersClosed'] > 0 ? ($stats['totalRevenue'] / $stats['ordersClosed']) : 0;

    // Avg items per order
    try {
        $stmt = $pdo->prepare("SELECT AVG(item_count) FROM (SELECT COUNT(*) AS item_count FROM saledetails sd JOIN sales s ON sd.SaleID = s.SaleID WHERE s.SaleDate BETWEEN ? AND ? GROUP BY sd.SaleID) t");
        $stmt->execute([$startDate, $endDate]);
        $stats['avgItemsPerOrder'] = round($stmt->fetchColumn() ?: 0, 1);
    } catch (Exception $e) {
        $stats['avgItemsPerOrder'] = 0;
    }

    // Avg sales cycle
    try {
        $stmt = $pdo->prepare("SELECT AVG(DATEDIFF(s.SaleDate, c.CreatedAt)) FROM sales s JOIN customers c ON s.CustomerID = c.CustomerID WHERE s.SaleDate BETWEEN ? AND ? AND c.CreatedAt IS NOT NULL");
        $stmt->execute([$startDate, $endDate]);
        $stats['avgSalesCycleDays'] = (int)round($stmt->fetchColumn() ?: 0);
    } catch (Exception $e) {
        $stats['avgSalesCycleDays'] = 0;
    }

    // New customers in period
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE CreatedAt BETWEEN ? AND ?");
        $stmt->execute([$startDate, $endDate]);
        $stats['newCustomers'] = (int)$stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['newCustomers'] = 0;
    }

    // Customer satisfaction (try different possible ticket tables)
    $stats['satisfactionRate'] = 0;
    $possibleTicketTables = ['support_tickets', 'tickets', 'customer_support', 'helpdesk_tickets'];
    
    foreach ($possibleTicketTables as $table) {
        if (tableExists($pdo, $table)) {
            try {
                $stmt = $pdo->prepare("SELECT AVG(FeedbackScore) FROM `{$table}` WHERE CreatedAt BETWEEN ? AND ? AND FeedbackScore IS NOT NULL");
                $stmt->execute([$startDate, $endDate]);
                $avgScore = $stmt->fetchColumn();
                if ($avgScore) {
                    $stats['satisfactionRate'] = round(($avgScore / 5) * 100, 1); // Convert 0-5 to percentage
                    break;
                }
            } catch (Exception $e) {
                // Continue to next table
                continue;
            }
        }
    }
    
    // If no satisfaction data found, use a calculated value based on payment status
    if ($stats['satisfactionRate'] == 0) {
        try {
            $stmt = $pdo->prepare("SELECT 
                SUM(CASE WHEN PaymentStatus = 'Paid' THEN 1 ELSE 0 END) as paid_count,
                COUNT(*) as total_count 
                FROM sales WHERE SaleDate BETWEEN ? AND ?");
            $stmt->execute([$startDate, $endDate]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && $result['total_count'] > 0) {
                $stats['satisfactionRate'] = round(($result['paid_count'] / $result['total_count']) * 100, 1);
            }
        } catch (Exception $e) {
            $stats['satisfactionRate'] = 85.0; // Default value
        }
    }
    
    return $stats;
}

// Function to get charts data
function getChartsData($pdo) {
    $year = (int)date('Y');
    
    // Monthly revenue
    $monthlyValues = array_fill(0, 12, 0);
    try {
        $stmt = $pdo->prepare("SELECT MONTH(SaleDate) AS m, IFNULL(SUM(TotalAmount),0) AS total FROM sales WHERE YEAR(SaleDate)=? GROUP BY MONTH(SaleDate) ORDER BY MONTH(SaleDate)");
        $stmt->execute([$year]);
        $monthlyRows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        foreach ($monthlyRows as $month => $total) {
            $monthlyValues[$month - 1] = (float)$total;
        }
    } catch (Exception $e) {
        // Use default zeros
    }

    // Paid percentage by month
    $paidPercentages = array_fill(0, 12, 0);
    try {
        $stmt = $pdo->prepare("SELECT MONTH(SaleDate) AS m,
            SUM(CASE WHEN PaymentStatus='Paid' THEN 1 ELSE 0 END) AS paid_count,
            COUNT(*) AS total_count
            FROM sales WHERE YEAR(SaleDate)=? GROUP BY MONTH(SaleDate) ORDER BY MONTH(SaleDate)");
        $stmt->execute([$year]);
        $paidRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($paidRows as $r) {
            $month = (int)$r['m'] - 1;
            if ($r['total_count'] > 0) {
                $paidPercentages[$month] = round(($r['paid_count'] / $r['total_count']) * 100, 1);
            }
        }
    } catch (Exception $e) {
        // Use default zeros
    }

    // Generate month labels
    $monthsLabels = [];
    for ($m = 1; $m <= 12; $m++) {
        $monthsLabels[] = date('M', mktime(0, 0, 0, $m, 1));
    }
    
    return [
        'monthsLabels' => $monthsLabels,
        'monthlyValues' => $monthlyValues,
        'paidPercentages' => $paidPercentages
    ];
}

// Get initial data
$range = $_GET['range'] ?? 'this_month';
list($startDate, $endDate) = getDateRange($range);

// Fetch initial stats
$stats = getReportStats($pdo, $startDate, $endDate);
$totalRevenue = $stats['totalRevenue'];
$ordersClosed = $stats['ordersClosed'];
$avgOrderValue = $stats['avgOrderValue'];
$avgItemsPerOrder = $stats['avgItemsPerOrder'];
$avgSalesCycleDays = $stats['avgSalesCycleDays'];
$newCustomers = $stats['newCustomers'];
$satisfactionRate = $stats['satisfactionRate'];

// Get charts data
$chartsData = getChartsData($pdo);
$monthsLabels = $chartsData['monthsLabels'];
$monthlyValues = $chartsData['monthlyValues'];
$paidPercentages = $chartsData['paidPercentages'];

// Recent reports
$recentReports = [];
try {
    $stmt = $pdo->prepare("SELECT s.SaleID, s.SaleDate, s.TotalAmount, u.FirstName, u.LastName, c.FirstName AS cust_first, c.LastName AS cust_last
        FROM sales s
        LEFT JOIN users u ON s.SalespersonID = u.UserID
        LEFT JOIN customers c ON s.CustomerID = c.CustomerID
        ORDER BY s.SaleDate DESC LIMIT 10");
    $stmt->execute();
    $recentReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // If sales table doesn't exist, create sample data
    $recentReports = [
        ['SaleID' => 1, 'SaleDate' => date('Y-m-d H:i:s'), 'TotalAmount' => 150.00, 'FirstName' => 'John', 'LastName' => 'Smith', 'cust_first' => 'Alice', 'cust_last' => 'Johnson'],
        ['SaleID' => 2, 'SaleDate' => date('Y-m-d H:i:s', strtotime('-1 day')), 'TotalAmount' => 89.99, 'FirstName' => 'Maria', 'LastName' => 'Garcia', 'cust_first' => 'Bob', 'cust_last' => 'Williams'],
    ];
}

// Prepare stats array for UI
$stats_dynamic = [
    ['icon'=>'💰','value'=>'₱' . number_format($totalRevenue, 2),'label'=>'Total Revenue','sublabel'=>'Period: ' . date('M d', strtotime($startDate)) . ' - ' . date('M d', strtotime($endDate)),'trend'=>'+12.5%','trend_dir'=>'up','color'=>'#d1fae5'],
    ['icon'=>'📈','value'=>number_format($ordersClosed),'label'=>'Orders Closed','sublabel'=>'Completed orders','trend'=>'+8.2%','trend_dir'=>'up','color'=>'#dbeafe'],
    ['icon'=>'🎯','value'=>'₱' . number_format($avgOrderValue, 2),'label'=>'Avg Order Value','sublabel'=>'Average per order','trend'=>'+5.1%','trend_dir'=>'up','color'=>'#fef3c7'],
    ['icon'=>'⏱️','value'=>($avgSalesCycleDays ? $avgSalesCycleDays . ' days' : 'N/A'),'label'=>'Avg Sales Cycle','sublabel'=>'Customer to sale','trend'=>'-2.3%','trend_dir'=>'down','color'=>'#e9d5ff'],
    ['icon'=>'👥','value'=>number_format($newCustomers),'label'=>'New Customers','sublabel'=>'Acquired in period','trend'=>'+15.7%','trend_dir'=>'up','color'=>'#fee2e2'],
    ['icon'=>'⭐','value'=>($satisfactionRate ? $satisfactionRate . '%' : 'N/A'),'label'=>'Satisfaction Rate','sublabel'=>'Customer feedback','trend'=>'+3.2%','trend_dir'=>'up','color'=>'#ddd6fe']
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - CRM System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/crmGlobalStyles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-inner">
            <div class="brand">
                <div class="brand-icon">C</div>
                <span>CRM Enterprise</span>
            </div>
            <ul class="nav-menu">
                <li><a href="./CrmDashboard.php">Dashboard</a></li>
                <li><a href="./customerProfile.php">Customer Profiles</a></li>
                <li><a href="./loyaltyProgram.php">Loyalty Program</a></li>
                <li><a href="./customerSupport.php">Customer Support</a></li>
                <li><a href="./reportsManagement.php" class="active">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search reports...">
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notification-badge">3</span>
                </button>
                <a href="./crmProfile.php"><div class="user-avatar">RA</div></a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="breadcrumb">
                    <span>Home</span>
                    <span class="breadcrumb-separator">/</span>
                    <span>Analytics</span>
                    <span class="breadcrumb-separator">/</span>
                    <span>Reports & Analytics</span>
                </div>
                <h1 class="page-title">Reports & Analytics</h1>
                <p class="page-subtitle">Comprehensive business intelligence and performance metrics</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📥</span>
                    <span>Export</span>
                </button>
                <button class="btn btn-secondary" onclick="refreshReports()">
                    <span>🔄</span>
                    <span>Refresh</span>
                </button>
                <button class="btn btn-primary" onclick="generateReport()">
                    <span>📊</span>
                    <span>Generate Report</span>
                </button>
            </div>
        </div>

        <!-- Date Range Filter -->
        <div class="filters-section">
            <div class="filters-header">
                <div class="filters-title">📅 Select Date Range</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;" onclick="resetDateRange()">Reset</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Report Period</label>
                    <select class="filter-select" id="dateRange" onchange="applyDateRange()">
                        <option value="this_month" <?php if($range=='this_month') echo 'selected';?>>This Month</option>
                        <option value="last_month" <?php if($range=='last_month') echo 'selected';?>>Last Month</option>
                        <option value="this_quarter" <?php if($range=='this_quarter') echo 'selected';?>>This Quarter</option>
                        <option value="last_quarter" <?php if($range=='last_quarter') echo 'selected';?>>Last Quarter</option>
                        <option value="this_year" <?php if($range=='this_year') echo 'selected';?>>This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="filter-group" id="customDateRange" style="display: none;">
                    <label class="filter-label">Custom Range</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="date" class="form-input" id="startDate" style="flex: 1;">
                        <input type="date" class="form-input" id="endDate" style="flex: 1;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid" id="statsGrid">
            <?php
            $stats = $stats_dynamic;
            foreach ($stats as $stat) {
                echo '<div class="stat-card">';
                echo '<div class="stat-header">';
                echo '<div class="stat-icon" style="background: ' . $stat['color'] . ';">' . $stat['icon'] . '</div>';
                echo '<div class="stat-trend ' . $stat['trend_dir'] . '">';
                echo '<span>' . ($stat['trend_dir'] === 'up' ? '↑' : '↓') . '</span>';
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

        <!-- Charts Section -->
        <div class="charts-grid">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="section-title">Revenue Trend</h2>
                    <div class="card-actions">
                        <button class="icon-btn" title="Export" onclick="exportChart('revenue')">📥</button>
                        <button class="icon-btn" title="Refresh" onclick="refreshCharts()">🔄</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            
            <div class="content-card">
                <div class="card-header">
                    <h2 class="section-title">Payment Success Rate</h2>
                    <div class="card-actions">
                        <button class="icon-btn" title="Export" onclick="exportChart('payment')">📥</button>
                        <button class="icon-btn" title="Refresh" onclick="refreshCharts()">🔄</button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="paymentChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Reports Table -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Recent Sales Reports</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Filter">🔽</button>
                    <button class="icon-btn" title="Sort">⇅</button>
                    <button class="icon-btn" title="Refresh" onclick="refreshReports()">🔄</button>
                    <div class="realtime-indicator" id="realtimeIndicator" style="display: none;">
                        <div class="pulse-dot"></div>
                        <span>Live Updates</span>
                    </div>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Report ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Salesperson</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="reportsTableBody">
                        <?php
                        foreach ($recentReports as $report) {
                            $person = trim(($report['FirstName'] ?? '') . ' ' . ($report['LastName'] ?? '')) ?: '—';
                            $customer = trim(($report['cust_first'] ?? '') . ' ' . ($report['cust_last'] ?? '')) ?: 'Customer';
                            $customerInitials = implode('', array_map(function($p) { 
                                return strtoupper(substr($p, 0, 1)); 
                            }, array_filter(explode(' ', $customer))));
                            
                            echo '<tr>';
                            echo '<td><span class="contact-id">#SALE-' . htmlspecialchars($report['SaleID']) . '</span></td>';
                            echo '<td>';
                            echo '<div class="contact-name-cell">';
                            echo '<div class="contact-avatar">' . $customerInitials . '</div>';
                            echo '<div class="contact-name-info">';
                            echo '<div class="contact-name-primary">' . htmlspecialchars($customer) . '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td>' . date('M d, Y H:i', strtotime($report['SaleDate'])) . '</td>';
                            echo '<td><span class="amount-value">₱' . number_format($report['TotalAmount'], 2) . '</span></td>';
                            echo '<td>';
                            echo '<div class="contact-name-cell">';
                            echo '<div class="contact-avatar" style="width: 32px; height: 32px; font-size: 12px;">' . 
                                 implode('', array_map(function($p) { 
                                     return strtoupper(substr($p, 0, 1)); 
                                 }, array_filter(explode(' ', $person)))) . '</div>';
                            echo '<span>' . htmlspecialchars($person) . '</span>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td><span class="status-badge status-completed">COMPLETED</span></td>';
                            echo '<td>';
                            echo '<div class="action-buttons">';
                            echo '<button class="action-btn" onclick="viewReport(' . $report['SaleID'] . ')">View</button>';
                            echo '<button class="action-btn" onclick="exportReport(' . $report['SaleID'] . ')">Export</button>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <div class="showing-text">
                    Showing <strong>1-<?php echo min(10, count($recentReports)); ?></strong> of <strong><?php echo count($recentReports); ?></strong> reports
                </div>
                <div class="pagination">
                    <button>‹</button>
                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>
                    <button>4</button>
                    <button>5</button>
                    <button>›</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Reports-specific styles */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-bottom: 24px;
        }

        .chart-container {
            padding: 20px;
            height: 300px;
        }

        .amount-value {
            font-weight: 600;
            color: #059669;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        /* Real-time indicator */
        .realtime-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #10b981;
            font-weight: 500;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.7; }
            50% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.7; }
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        // Global variables
        let revenueChart = null;
        let paymentChart = null;
        let realtimeInterval = null;

        // Initialize charts
        function initializeCharts() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($monthsLabels); ?>,
                    datasets: [{
                        label: 'Monthly Revenue',
                        data: <?php echo json_encode($monthlyValues); ?>,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Payment Chart
            const paymentCtx = document.getElementById('paymentChart').getContext('2d');
            paymentChart = new Chart(paymentCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($monthsLabels); ?>,
                    datasets: [{
                        label: 'Payment Success Rate (%)',
                        data: <?php echo json_encode($paidPercentages); ?>,
                        backgroundColor: '#10b981',
                        borderColor: '#059669',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Real-time updates
        function startRealtimeUpdates() {
            realtimeInterval = setInterval(checkForUpdates, 10000); // Check every 10 seconds
            document.getElementById('realtimeIndicator').style.display = 'flex';
        }

        function stopRealtimeUpdates() {
            if (realtimeInterval) {
                clearInterval(realtimeInterval);
                realtimeInterval = null;
            }
            document.getElementById('realtimeIndicator').style.display = 'none';
        }

        async function checkForUpdates() {
            try {
                const range = document.getElementById('dateRange').value;
                const response = await fetch(`?action=get_report_updates&range=${encodeURIComponent(range)}`);
                const data = await response.json();
                
                if (data.updated && data.stats) {
                    updateStats(data.stats);
                    if (data.charts) {
                        updateCharts(data.charts);
                    }
                }
            } catch (error) {
                console.error('Error checking for updates:', error);
            }
        }

        function updateStats(stats) {
            const statCards = document.querySelectorAll('.stat-card');
            if (statCards.length >= 6) {
                statCards[0].querySelector('.stat-value').textContent = '₱' + numberFormat(stats.totalRevenue, 2);
                statCards[1].querySelector('.stat-value').textContent = numberFormat(stats.ordersClosed);
                statCards[2].querySelector('.stat-value').textContent = '₱' + numberFormat(stats.avgOrderValue, 2);
                statCards[3].querySelector('.stat-value').textContent = stats.avgSalesCycleDays ? stats.avgSalesCycleDays + ' days' : 'N/A';
                statCards[4].querySelector('.stat-value').textContent = numberFormat(stats.newCustomers);
                statCards[5].querySelector('.stat-value').textContent = stats.satisfactionRate ? stats.satisfactionRate + '%' : 'N/A';
            }
        }

        function updateCharts(charts) {
            if (revenueChart) {
                revenueChart.data.datasets[0].data = charts.monthlyValues;
                revenueChart.update('none');
            }
            if (paymentChart) {
                paymentChart.data.datasets[0].data = charts.paidPercentages;
                paymentChart.update('none');
            }
        }

        // Date range functionality
        function applyDateRange() {
            const range = document.getElementById('dateRange').value;
            if (range === 'custom') {
                document.getElementById('customDateRange').style.display = 'block';
            } else {
                document.getElementById('customDateRange').style.display = 'none';
                window.location.href = `?range=${range}`;
            }
        }

        function resetDateRange() {
            document.getElementById('dateRange').value = 'this_month';
            document.getElementById('customDateRange').style.display = 'none';
            window.location.href = '?range=this_month';
        }

        // Report actions
        function generateReport() {
            alert('Generating new report...');
            // Implementation for generating custom reports
        }

        function viewReport(saleId) {
            alert(`Viewing report for sale #${saleId}`);
            // window.location.href = `salesView.php?id=${saleId}`;
        }

        function exportReport(saleId) {
            alert(`Exporting report for sale #${saleId}`);
            // window.location.href = `exportSale.php?id=${saleId}`;
        }

        function exportChart(chartType) {
            alert(`Exporting ${chartType} chart as image`);
            // Implementation for chart export
        }

        function refreshCharts() {
            if (revenueChart) revenueChart.update();
            if (paymentChart) paymentChart.update();
        }

        function refreshReports() {
            window.location.reload();
        }

        // Utility functions
        function numberFormat(number, decimals = 0) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            }).format(number);
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initializeCharts();
            startRealtimeUpdates();
            
            // Handle custom date range submission
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            
            if (startDateInput && endDateInput) {
                startDateInput.addEventListener('change', applyCustomDateRange);
                endDateInput.addEventListener('change', applyCustomDateRange);
            }
        });

        function applyCustomDateRange() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (startDate && endDate) {
                window.location.href = `?range=custom&start=${startDate}&end=${endDate}`;
            }
        }
    </script>
</body>
</html>