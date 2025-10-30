<?php
// Include the unified backend
require_once(__DIR__ . '/../api/crm.php');

// Get initial data for Reports & Analytics
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
$chartsData = getChartsData($pdo, $startDate, $endDate);
$monthsLabels = $chartsData['monthsLabels'];
$monthlyValues = $chartsData['monthlyValues'];
$paidPercentages = $chartsData['paidPercentages'];

// Recent reports
$recentReports = [];
try {
    $stmt = $pdo->prepare("SELECT s.SaleID, s.SaleDate, s.TotalAmount, s.PaymentStatus, s.PaymentMethod,
        u.FirstName, u.LastName, c.FirstName AS cust_first, c.LastName AS cust_last
        FROM sales s
        LEFT JOIN users u ON s.SalespersonID = u.UserID
        LEFT JOIN customers c ON s.CustomerID = c.CustomerID
        WHERE s.SaleDate BETWEEN ? AND ?
        ORDER BY s.SaleDate DESC LIMIT 10");
    $stmt->execute([$startDate, $endDate]);
    $recentReports = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // If sales table doesn't exist or error, create sample data
    $recentReports = [
        ['SaleID' => 1, 'SaleDate' => date('Y-m-d H:i:s'), 'TotalAmount' => 150.00, 'PaymentStatus' => 'Paid', 'PaymentMethod' => 'Cash', 'FirstName' => 'John', 'LastName' => 'Smith', 'cust_first' => 'Alice', 'cust_last' => 'Johnson'],
        ['SaleID' => 2, 'SaleDate' => date('Y-m-d H:i:s', strtotime('-1 day')), 'TotalAmount' => 89.99, 'PaymentStatus' => 'Paid', 'PaymentMethod' => 'Card', 'FirstName' => 'Maria', 'LastName' => 'Garcia', 'cust_first' => 'Bob', 'cust_last' => 'Williams'],
    ];
}

// Prepare stats array for UI
$stats_dynamic = [
    ['icon' => '💰', 'value' => '₱' . number_format($totalRevenue, 2), 'label' => 'Total Revenue', 'sublabel' => 'Period: ' . date('M d', strtotime($startDate)) . ' - ' . date('M d', strtotime($endDate)), 'trend' => '+12.5%', 'trend_dir' => 'up', 'color' => '#d1fae5'],
    ['icon' => '📈', 'value' => number_format($ordersClosed), 'label' => 'Orders Closed', 'sublabel' => 'Completed orders', 'trend' => '+8.2%', 'trend_dir' => 'up', 'color' => '#dbeafe'],
    ['icon' => '🎯', 'value' => '₱' . number_format($avgOrderValue, 2), 'label' => 'Avg Order Value', 'sublabel' => 'Average per order', 'trend' => '+5.1%', 'trend_dir' => 'up', 'color' => '#fef3c7'],
    ['icon' => '⏱️', 'value' => ($avgSalesCycleDays ? $avgSalesCycleDays . ' days' : 'N/A'), 'label' => 'Avg Sales Cycle', 'sublabel' => 'Customer to sale', 'trend' => '-2.3%', 'trend_dir' => 'down', 'color' => '#e9d5ff'],
    ['icon' => '👥', 'value' => number_format($newCustomers), 'label' => 'New Customers', 'sublabel' => 'Acquired in period', 'trend' => '+15.7%', 'trend_dir' => 'up', 'color' => '#fee2e2'],
    ['icon' => '⭐', 'value' => ($satisfactionRate ? $satisfactionRate . '%' : 'N/A'), 'label' => 'Satisfaction Rate', 'sublabel' => 'Customer feedback', 'trend' => '+3.2%', 'trend_dir' => 'up', 'color' => '#ddd6fe']
];
// Assuming you already have the user ID from session
$userId = $_SESSION['user_id'] ?? null;

$userInitials = '';

// Fetch user's first name from the database
if ($userId) {
    $stmt = $db->prepare("SELECT FirstName FROM users WHERE UserID = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && !empty($user['FirstName'])) {
        // Extract initials (first two letters, uppercase)
        $userInitials = strtoupper(substr($user['FirstName'], 0, 2));
    } else {
        $userInitials = 'NA'; // fallback if no name found
    }
}
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
                <span>CRM Shoe Retail</span>
            </div>
            <ul class="nav-menu">
                <li><a href="./CrmDashboard.php">Dashboard</a></li>
                <li><a href="./customerProfile.php">Customer Profiles</a></li>
                <li><a href="./loyaltyProgram.php" class="active">Loyalty Program</a></li>
                <li><a href="./customerSupport.php">Customer Support</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
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
                <a href="./crmProfile.php">
                    <div class="user-avatar"><?php echo htmlspecialchars($userInitials); ?></div>
                </a>
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
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" id="exportDropdown" data-toggle="dropdown">
                        <span>📥</span>
                        <span>Export</span>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="#" onclick="exportReport('sales')">Sales Report</a>
                        <a class="dropdown-item" href="#" onclick="exportReport('revenue')">Revenue Report</a>
                        <a class="dropdown-item" href="#" onclick="exportChartAsImage('revenue')">Revenue Chart</a>
                        <a class="dropdown-item" href="#" onclick="exportChartAsImage('payment')">Payment Chart</a>
                    </div>
                </div>
                <button class="btn btn-primary" onclick="openGenerateReportModal()">
                    <span>📊</span>
                    <span>Generate Report</span>
                </button>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['report_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['report_message'];
                unset($_SESSION['report_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['report_error'])): ?>
            <div class="alert alert-error">
                <?php echo $_SESSION['report_error'];
                unset($_SESSION['report_error']); ?>
            </div>
        <?php endif; ?>

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
                        <option value="this_month" <?php if ($range == 'this_month') echo 'selected'; ?>>This Month</option>
                        <option value="last_month" <?php if ($range == 'last_month') echo 'selected'; ?>>Last Month</option>
                        <option value="this_quarter" <?php if ($range == 'this_quarter') echo 'selected'; ?>>This Quarter</option>
                        <option value="last_quarter" <?php if ($range == 'last_quarter') echo 'selected'; ?>>Last Quarter</option>
                        <option value="this_year" <?php if ($range == 'this_year') echo 'selected'; ?>>This Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                <div class="filter-group" id="customDateRange" style="display: <?php echo $range === 'custom' ? 'block' : 'none'; ?>;">
                    <label class="filter-label">Custom Range</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="date" class="form-input" id="startDate" value="<?php echo $range === 'custom' && isset($_GET['start']) ? $_GET['start'] : ''; ?>" style="flex: 1;">
                        <input type="date" class="form-input" id="endDate" value="<?php echo $range === 'custom' && isset($_GET['end']) ? $_GET['end'] : ''; ?>" style="flex: 1;">
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
                        <button class="icon-btn" title="Export" onclick="exportChartAsImage('revenue')">📥</button>
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
                        <button class="icon-btn" title="Export" onclick="exportChartAsImage('payment')">📥</button>
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
                    <button class="icon-btn" title="Filter" onclick="toggleFilters()">🔽</button>
                    <button class="icon-btn" title="Sort" onclick="toggleSort()">⇅</button>
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
                            $customerInitials = implode('', array_map(function ($p) {
                                return strtoupper(substr($p, 0, 1));
                            }, array_filter(explode(' ', $customer))));

                            // Determine status badge
                            $statusClass = 'status-completed';
                            $statusText = 'COMPLETED';
                            if ($report['PaymentStatus'] === 'Pending') {
                                $statusClass = 'status-pending';
                                $statusText = 'PENDING';
                            } elseif ($report['PaymentStatus'] === 'Partial') {
                                $statusClass = 'status-pending';
                                $statusText = 'PARTIAL';
                            }

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
                                implode('', array_map(function ($p) {
                                    return strtoupper(substr($p, 0, 1));
                                }, array_filter(explode(' ', $person)))) . '</div>';
                            echo '<span>' . htmlspecialchars($person) . '</span>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td><span class="status-badge ' . $statusClass . '">' . $statusText . '</span></td>';
                            echo '<td>';
                            echo '<div class="action-buttons">';
                            echo '<button class="action-btn" onclick="viewReport(' . $report['SaleID'] . ')">View</button>';
                            echo '<button class="action-btn" onclick="exportSaleReport(' . $report['SaleID'] . ')">Export</button>';
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

    <!-- Generate Report Modal -->
    <div class="modal-overlay" id="generateReportModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Generate Custom Report</h3>
                <button class="close-btn" onclick="closeGenerateReportModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="generateReportForm" method="POST" action="crm.php">
                    <input type="hidden" name="action" value="generate_report">

                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Report Type</label>
                            <select class="filter-select" name="report_type" required>
                                <option value="sales_summary">Sales Summary</option>
                                <option value="product_performance">Product Performance</option>
                                <option value="customer_analysis">Customer Analysis</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Date Range</label>
                            <select class="filter-select" name="range" required>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="this_quarter">This Quarter</option>
                                <option value="last_quarter">Last Quarter</option>
                                <option value="this_year">This Year</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">Output Format</label>
                            <select class="filter-select" name="format" required>
                                <option value="html">HTML (View in Browser)</option>
                                <option value="csv">CSV (Download)</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeGenerateReportModal()">Cancel</button>
                <button class="btn btn-primary" onclick="submitGenerateReportForm()">Generate Report</button>
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

        /* Modal styles - Matching loyalty program design */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: white;
            border-radius: 12px;
            width: 500px;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal {
            transform: translateY(0);
        }

        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .close-btn:hover {
            background: #f3f4f6;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .form-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full-width {
            width: 100%;
        }

        .form-label {
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }

        .form-input,
        .filter-select,
        .form-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-input:focus,
        .filter-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 160px;
            padding: 8px 0;
            margin: 2px 0 0;
            font-size: 14px;
            text-align: left;
            list-style: none;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 8px 16px;
            clear: both;
            font-weight: 400;
            color: #212529;
            text-align: inherit;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            text-decoration: none;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        @keyframes pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.7;
            }

            50% {
                transform: scale(1.1);
                opacity: 1;
            }

            100% {
                transform: scale(0.95);
                opacity: 0.7;
            }
        }

        @media (max-width: 768px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }

            .modal {
                width: 95%;
                margin: 20px;
            }

            .modal-footer {
                flex-direction: column;
            }

            .modal-footer .btn {
                width: 100%;
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
                const response = await fetch(`crm.php?action=get_report_updates&range=${encodeURIComponent(range)}`);
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
                revenueChart.data.labels = charts.monthsLabels;
                revenueChart.data.datasets[0].data = charts.monthlyValues;
                revenueChart.update('none');
            }
            if (paymentChart) {
                paymentChart.data.labels = charts.monthsLabels;
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

        // Export functionality
        function exportReport(type) {
            const range = document.getElementById('dateRange').value;
            window.location.href = `crm.php?action=export_report&type=${type}&range=${range}`;
        }

        function exportChartAsImage(chartType) {
            let chart, filename;

            if (chartType === 'revenue' && revenueChart) {
                chart = revenueChart;
                filename = 'revenue_chart_' + new Date().toISOString().slice(0, 10);
            } else if (chartType === 'payment' && paymentChart) {
                chart = paymentChart;
                filename = 'payment_chart_' + new Date().toISOString().slice(0, 10);
            } else {
                alert('Chart not available for export');
                return;
            }

            const image = chart.toBase64Image();
            const link = document.createElement('a');
            link.href = image;
            link.download = filename + '.png';
            link.click();
        }

        function exportSaleReport(saleId) {
            alert(`Export functionality for sale #${saleId} would be implemented here`);
            // In a real implementation, this would generate a PDF or CSV for the specific sale
        }

        // Generate report modal functionality
        function openGenerateReportModal() {
            document.getElementById('generateReportModal').classList.add('active');
        }

        function closeGenerateReportModal() {
            document.getElementById('generateReportModal').classList.remove('active');
        }

        function submitGenerateReportForm() {
            const form = document.getElementById('generateReportForm');
            if (form.checkValidity()) {
                form.submit();
            } else {
                form.reportValidity();
            }
        }

        // Report actions
        function viewReport(saleId) {
            alert(`Viewing report for sale #${saleId}`);
            // window.location.href = `salesView.php?id=${saleId}`;
        }

        function toggleFilters() {
            alert('Filter functionality would be implemented here');
        }

        function toggleSort() {
            alert('Sort functionality would be implemented here');
        }

        // Dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle dropdown
            const exportDropdown = document.getElementById('exportDropdown');
            if (exportDropdown) {
                exportDropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    const menu = this.nextElementSibling;
                    menu.classList.toggle('show');
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.matches('#exportDropdown')) {
                    const dropdowns = document.getElementsByClassName('dropdown-menu');
                    for (let i = 0; i < dropdowns.length; i++) {
                        const openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            });
        });

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

            // Close modals on overlay click
            document.querySelectorAll('.modal-overlay').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('active');
                    }
                });
            });
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