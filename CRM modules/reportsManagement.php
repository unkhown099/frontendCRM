<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - CRM System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-hover: #4f46e5;
            --primary-light: #eef2ff;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #fafafa;
            --gray-100: #f4f4f5;
            --gray-200: #e4e4e7;
            --gray-300: #d4d4d8;
            --gray-400: #a1a1aa;
            --gray-500: #71717a;
            --gray-600: #52525b;
            --gray-700: #3f3f46;
            --gray-800: #27272a;
            --gray-900: #18181b;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
            --radius: 8px;
            --radius-lg: 12px;
            --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(to bottom, #fafafa 0%, #f4f4f5 100%);
            color: var(--gray-900);
            line-height: 1.5;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Navigation */
        .navbar {
            background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
            backdrop-filter: blur(10px);
            padding: 0;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--gray-200);
        }

        .navbar-inner {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 64px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 18px;
            color: var(--gray-900);
            letter-spacing: -0.02em;
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .nav-menu {
            display: flex;
            gap: 4px;
            list-style: none;
            align-items: center;
        }

        .nav-menu a {
            text-decoration: none;
            color: var(--gray-600);
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: var(--radius);
            transition: var(--transition);
            position: relative;
        }

        .nav-menu a:hover {
            color: var(--gray-900);
            background: var(--gray-100);
        }

        .nav-menu a.active {
            color: var(--primary);
            background: var(--primary-light);
        }

        .nav-menu a.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: var(--primary);
            border-radius: 50%;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-wrapper {
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 16px;
        }

        .search-box {
            padding: 10px 16px 10px 40px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: white;
            font-size: 14px;
            width: 280px;
            transition: var(--transition);
            font-family: inherit;
        }

        .search-box:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .notification-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-600);
            box-shadow: var(--shadow-sm);
        }

        .notification-btn:hover {
            background: var(--gray-100);
            color: var(--gray-900);
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 18px;
            height: 18px;
            background: var(--danger);
            border-radius: 50%;
            font-size: 10px;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .user-avatar:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Container */
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 32px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 32px;
        }

        .header-content {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-500);
            font-size: 13px;
            font-weight: 500;
        }

        .breadcrumb-separator {
            color: var(--gray-400);
        }

        .page-title {
            font-size: 36px;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 15px;
            margin-top: 4px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: var(--radius);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            font-family: inherit;
            letter-spacing: -0.01em;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            box-shadow: var(--shadow-sm);
        }

        .btn-secondary:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        /* Filter Section */
        .filter-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 32px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filter-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
        }

        .filter-select {
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: white;
            font-size: 14px;
            font-family: inherit;
            color: var(--gray-700);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
            border: 1px solid var(--gray-200);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
        }

        .stat-trend.up {
            background: #dcfce7;
            color: #16a34a;
        }

        .stat-trend.down {
            background: #fee2e2;
            color: #dc2626;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.02em;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-600);
            font-weight: 500;
        }

        .stat-sublabel {
            font-size: 12px;
            color: var(--gray-500);
            margin-top: 4px;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .chart-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .chart-actions {
            display: flex;
            gap: 8px;
        }

        .icon-btn {
            width: 32px;
            height: 32px;
            border: 1px solid var(--gray-300);
            background: white;
            border-radius: var(--radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            color: var(--gray-600);
            font-size: 14px;
        }

        .icon-btn:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            color: var(--gray-900);
        }

        .chart-placeholder {
            height: 300px;
            background: linear-gradient(to bottom, var(--gray-50), white);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed var(--gray-300);
        }

        .chart-placeholder-text {
            color: var(--gray-500);
            font-size: 14px;
            font-weight: 600;
        }

        /* Bar Chart Visual */
        .bar-chart {
            height: 300px;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            gap: 12px;
            padding: 20px 0;
        }

        .bar {
            flex: 1;
            background: linear-gradient(to top, var(--primary), var(--secondary));
            border-radius: 8px 8px 0 0;
            position: relative;
            transition: var(--transition);
            cursor: pointer;
            min-height: 40px;
        }

        .bar:hover {
            opacity: 0.8;
            transform: translateY(-4px);
        }

        .bar-label {
            position: absolute;
            bottom: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: 600;
            color: var(--gray-600);
            white-space: nowrap;
        }

        .bar-value {
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-900);
        }

        /* Line Chart Visual */
        .line-chart {
            height: 300px;
            display: flex;
            align-items: flex-end;
            position: relative;
            padding: 20px 0;
        }

        .line-chart-grid {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .grid-line {
            width: 100%;
            height: 1px;
            background: var(--gray-200);
        }

        .line-chart-content {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            position: relative;
            z-index: 1;
        }

        .line-point {
            width: 12px;
            height: 12px;
            background: var(--primary);
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: var(--shadow-md);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .line-point:hover {
            transform: scale(1.5);
        }

        .line-point-label {
            position: absolute;
            top: -25px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: 700;
            color: var(--primary);
            white-space: nowrap;
        }

        /* Reports Table */
        .reports-table-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 0;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--gray-200);
            margin-bottom: 32px;
        }

        .table-header {
            padding: 24px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(to bottom, white 0%, var(--gray-50) 100%);
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.01em;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead {
            background: var(--gray-50);
        }

        .table th {
            text-align: left;
            padding: 16px 24px;
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--gray-200);
        }

        .table td {
            padding: 20px 24px;
            font-size: 14px;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background: var(--gray-50);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .report-name {
            font-weight: 600;
            color: var(--gray-900);
        }

        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-sales {
            background: #dbeafe;
            color: #1e40af;
        }

        .category-leads {
            background: #fef3c7;
            color: #92400e;
        }

        .category-performance {
            background: #d1fae5;
            color: #065f46;
        }

        .category-activity {
            background: #ddd6fe;
            color: #5b21b6;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 8px 16px;
            border: 1px solid var(--gray-300);
            background: white;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-700);
        }

        .action-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .charts-grid {
                grid-template-columns: 1fr;
            }

            .nav-menu {
                display: none;
            }

            .search-box {
                width: 180px;
            }

            .page-title {
                font-size: 28px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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
                <li><a href="./leadsManagement.php">Leads</a></li>
                <li><a href="./contactManagement.php">Contacts</a></li>
                <li><a href="./dealsManagement.php">Deals</a></li>
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="#" class="active">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search reports...">
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notification-badge">5</span>
                </button>
                <div class="user-avatar">JD</div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <div class="header-content">
                <div class="breadcrumb">
                    <span>Home</span>
                    <span class="breadcrumb-separator">/</span>
                    <span>Reports & Analytics</span>
                </div>
                <h1 class="page-title">Reports & Analytics</h1>
                <p class="page-subtitle">Comprehensive insights and performance metrics</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Custom Report</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📥</span>
                    <span>Export All</span>
                </button>
                <button class="btn btn-primary">
                    <span>📧</span>
                    <span>Schedule Report</span>
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <div class="filter-title">📅 Report Filters</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset Filters</button>
            </div>
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">Date Range</label>
                    <select class="filter-select">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Quarter</option>
                        <option>Last Quarter</option>
                        <option>This Year</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Report Type</label>
                    <select class="filter-select">
                        <option>All Reports</option>
                        <option>Sales Reports</option>
                        <option>Lead Reports</option>
                        <option>Performance Reports</option>
                        <option>Activity Reports</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Team Member</label>
                    <select class="filter-select">
                        <option>All Team Members</option>
                        <option>John Santos</option>
                        <option>Maria Garcia</option>
                        <option>Robert Chen</option>
                        <option>Ana Reyes</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Region</label>
                    <select class="filter-select">
                        <option>All Regions</option>
                        <option>Metro Manila</option>
                        <option>Visayas</option>
                        <option>Mindanao</option>
                        <option>North Luzon</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '💰',
                    'value' => '₱12.4M',
                    'label' => 'Total Revenue',
                    'sublabel' => 'vs ₱10.2M last month',
                    'trend' => '+21.6%',
                    'trend_dir' => 'up',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '📈',
                    'value' => '156',
                    'label' => 'Deals Closed',
                    'sublabel' => 'vs 128 last month',
                    'trend' => '+21.9%',
                    'trend_dir' => 'up',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '🎯',
                    'value' => '68%',
                    'label' => 'Win Rate',
                    'sublabel' => 'vs 62% last month',
                    'trend' => '+9.7%',
                    'trend_dir' => 'up',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '⏱️',
                    'value' => '18 days',
                    'label' => 'Avg. Sales Cycle',
                    'sublabel' => 'vs 22 days last month',
                    'trend' => '-18.2%',
                    'trend_dir' => 'up',
                    'color' => '#fef3c7'
                ]
            ];

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
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">Monthly Revenue</div>
                    <div class="chart-actions">
                        <button class="icon-btn">⟳</button>
                        <button class="icon-btn">⬇</button>
                    </div>
                </div>
                <div class="bar-chart">
                    <?php
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct'];
                    $values = [50,65,70,80,90,110,105,115,100,120];
                    foreach ($months as $i => $month) {
                        $height = $values[$i] * 2; // simple scaling
                        echo '<div class="bar" style="height:'.$height.'px;">
                                <span class="bar-value">₱'.$values[$i].'k</span>
                                <span class="bar-label">'.$month.'</span>
                              </div>';
                    }
                    ?>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">Lead Conversion Trend</div>
                    <div class="chart-actions">
                        <button class="icon-btn">⟳</button>
                        <button class="icon-btn">⬇</button>
                    </div>
                </div>
                <div class="line-chart">
                    <div class="line-chart-grid">
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                    </div>
                    <div class="line-chart-content">
                        <?php
                        $points = [10,30,45,40,60,70,65,85];
                        foreach ($points as $p) {
                            echo '<div class="line-point" style="margin-bottom:'.$p.'%;"><span class="line-point-label">'.$p.'%</span></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table Section -->
        <div class="reports-table-section">
            <div class="table-header">
                <h2 class="section-title">Recent Reports</h2>
                <button class="btn btn-secondary">View All</button>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Category</th>
                            <th>Date Generated</th>
                            <th>Generated By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $reports = [
                            ['name'=>'Q3 Sales Overview','category'=>'Sales','date'=>'Oct 10, 2025','by'=>'Maria Garcia'],
                            ['name'=>'Leads Performance Report','category'=>'Leads','date'=>'Oct 8, 2025','by'=>'John Santos'],
                            ['name'=>'Team Productivity Metrics','category'=>'Performance','date'=>'Oct 5, 2025','by'=>'Ana Reyes'],
                            ['name'=>'Daily Activity Summary','category'=>'Activity','date'=>'Oct 3, 2025','by'=>'Robert Chen']
                        ];
                        foreach ($reports as $r) {
                            $badgeClass = match($r['category']) {
                                'Sales' => 'category-sales',
                                'Leads' => 'category-leads',
                                'Performance' => 'category-performance',
                                'Activity' => 'category-activity',
                                default => ''
                            };
                            echo "<tr>
                                    <td class='report-name'>{$r['name']}</td>
                                    <td><span class='category-badge {$badgeClass}'>{$r['category']}</span></td>
                                    <td>{$r['date']}</td>
                                    <td>{$r['by']}</td>
                                    <td class='action-buttons'>
                                        <button class='action-btn'>View</button>
                                        <button class='action-btn'>Export</button>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>