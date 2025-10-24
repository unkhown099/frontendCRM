<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Management - CRM System</title>
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

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: white;
            padding: 20px;
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
            margin-bottom: 12px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--gray-900);
            letter-spacing: -0.02em;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 13px;
            color: var(--gray-600);
            font-weight: 500;
        }

        /* Filters Section */
        .filters-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .filters-grid {
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

        /* Content Card */
        .content-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 0;
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .card-header {
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

        .card-actions {
            display: flex;
            gap: 8px;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            border: 1px solid var(--gray-300);
            background: white;
            border-radius: var(--radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            color: var(--gray-600);
        }

        .icon-btn:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
            color: var(--gray-900);
        }

        /* Table */
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

        .table th input[type="checkbox"] {
            cursor: pointer;
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

        .lead-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 13px;
        }

        .lead-name {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .lead-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .lead-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .lead-info-name {
            font-weight: 600;
            color: var(--gray-900);
        }

        .lead-info-email {
            font-size: 12px;
            color: var(--gray-500);
        }

        .company-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .company-icon {
            width: 32px;
            height: 32px;
            background: var(--gray-100);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .value-cell {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 15px;
        }

        .source-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: var(--gray-100);
            color: var(--gray-700);
        }

        .priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .priority-high {
            background: #fee2e2;
            color: #dc2626;
        }

        .priority-medium {
            background: #fef3c7;
            color: #d97706;
        }

        .priority-low {
            background: #dcfce7;
            color: #16a34a;
        }

        .priority-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .status-new {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-contacted {
            background: #fef3c7;
            color: #92400e;
        }

        .status-qualified {
            background: #d1fae5;
            color: #065f46;
        }

        .status-negotiating {
            background: #fce7f3;
            color: #9f1239;
        }

        .status-converted {
            background: #ddd6fe;
            color: #5b21b6;
        }

        .status-lost {
            background: #fee2e2;
            color: #991b1b;
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

        .action-btn.delete:hover {
            background: var(--danger);
            border-color: var(--danger);
        }

        /* Table Footer */
        .table-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--gray-50);
        }

        .pagination {
            display: flex;
            gap: 4px;
        }

        .pagination button {
            width: 36px;
            height: 36px;
            border: 1px solid var(--gray-300);
            background: white;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            color: var(--gray-700);
        }

        .pagination button:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .pagination button.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .showing-text {
            font-size: 13px;
            color: var(--gray-600);
            font-weight: 500;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .close-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--gray-100);
            border-radius: var(--radius);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .close-btn:hover {
            background: var(--gray-200);
        }

        .modal-body {
            padding: 24px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-700);
        }

        .form-input, .form-textarea {
            padding: 10px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: inherit;
            color: var(--gray-900);
            transition: var(--transition);
        }

        .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
        }

        .form-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            background: var(--gray-50);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
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

            .filters-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
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
                <li><a href="#" class="active">Leads</a></li>
                <li><a href="./contactManagement.php">Contacts</a></li>
                <li><a href="./dealsManagement.php">Deals</a></li>
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports and Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search leads...">
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
                    <span>Lead Management</span>
                </div>
                <h1 class="page-title">Lead Management</h1>
                <p class="page-subtitle">Manage and track all your leads in one place</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📥</span>
                    <span>Import</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Export</span>
                </button>
                <button class="btn btn-primary" onclick="openModal()">
                    <span>+</span>
                    <span>Add New Lead</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '📋',
                    'value' => '1,847',
                    'label' => 'Total Leads',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '🆕',
                    'value' => '234',
                    'label' => 'New This Week',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '✅',
                    'value' => '342',
                    'label' => 'Qualified',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '🔄',
                    'value' => '128',
                    'label' => 'In Progress',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '🎯',
                    'value' => '89',
                    'label' => 'Converted',
                    'color' => '#e9d5ff'
                ],
                [
                    'icon' => '❌',
                    'value' => '56',
                    'label' => 'Lost',
                    'color' => '#fee2e2'
                ]
            ];

            foreach ($stats as $stat) {
                echo '<div class="stat-card">';
                echo '<div class="stat-header">';
                echo '<div class="stat-icon" style="background: ' . $stat['color'] . ';">' . $stat['icon'] . '</div>';
                echo '</div>';
                echo '<div class="stat-value">' . $stat['value'] . '</div>';
                echo '<div class="stat-label">' . $stat['label'] . '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="filters-section">
            <div class="filters-header">
                <div class="filters-title">🔍 Filter Leads</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select">
                        <option>All Status</option>
                        <option>New</option>
                        <option>Contacted</option>
                        <option>Qualified</option>
                        <option>Negotiating</option>
                        <option>Converted</option>
                        <option>Lost</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Priority</label>
                    <select class="filter-select">
                        <option>All Priority</option>
                        <option>High</option>
                        <option>Medium</option>
                        <option>Low</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Source</label>
                    <select class="filter-select">
                        <option>All Sources</option>
                        <option>Website</option>
                        <option>Referral</option>
                        <option>Social Media</option>
                        <option>Cold Call</option>
                        <option>Email Campaign</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date Range</label>
                    <select class="filter-select">
                        <option>All Time</option>
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>This Quarter</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">All Leads (1,847)</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Column Settings">⚙️</button>
                    <button class="icon-btn" title="Download">⬇️</button>
                    <button class="icon-btn" title="Refresh">🔄</button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
                            <th>Lead ID</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Value</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $leads = [
                            [
                                'id' => '#LEAD-001',
                                'name' => 'John Santos',
                                'initials' => 'JS',
                                'email' => 'john.santos@techcorp.ph',
                                'company' => 'TechCorp PH',
                                'company_icon' => '🏢',
                                'phone' => '+63 917 123 4567',
                                'source' => 'Website',
                                'value' => '₱125,000',
                                'priority' => 'High',
                                'status' => 'Qualified',
                                'created' => '2 days ago'
                            ],
                            [
                                'id' => '#LEAD-002',
                                'name' => 'Maria Garcia',
                                'initials' => 'MG',
                                'email' => 'maria.g@globalsol.com',
                                'company' => 'Global Solutions',
                                'company_icon' => '🌐',
                                'phone' => '+63 918 234 5678',
                                'source' => 'Referral',
                                'value' => '₱85,000',
                                'priority' => 'Medium',
                                'status' => 'Contacted',
                                'created' => '3 days ago'
                            ],
                            [
                                'id' => '#LEAD-003',
                                'name' => 'Robert Chen',
                                'initials' => 'RC',
                                'email' => 'r.chen@innovationlabs.io',
                                'company' => 'Innovation Labs',
                                'company_icon' => '💡',
                                'phone' => '+63 919 345 6789',
                                'source' => 'Social Media',
                                'value' => '₱200,000',
                                'priority' => 'High',
                                'status' => 'Converted',
                                'created' => '5 days ago'
                            ],
                            [
                                'id' => '#LEAD-004',
                                'name' => 'Ana Reyes',
                                'initials' => 'AR',
                                'email' => 'ana@startupinc.ph',
                                'company' => 'StartUp Inc',
                                'company_icon' => '🚀',
                                'phone' => '+63 920 456 7890',
                                'source' => 'Cold Call',
                                'value' => '₱45,000',
                                'priority' => 'Low',
                                'status' => 'New',
                                'created' => '1 week ago'
                            ],
                            [
                                'id' => '#LEAD-005',
                                'name' => 'David Lim',
                                'initials' => 'DL',
                                'email' => 'david.lim@entgroup.com',
                                'company' => 'Enterprise Group',
                                'company_icon' => '🏛️',
                                'phone' => '+63 921 567 8901',
                                'source' => 'Email Campaign',
                                'value' => '₱150,000',
                                'priority' => 'High',
                                'status' => 'Negotiating',
                                'created' => '1 week ago'
                            ],
                            [
                                'id' => '#LEAD-006',
                                'name' => 'Sarah Tan',
                                'initials' => 'ST',
                                'email' => 'sarah.tan@retailco.ph',
                                'company' => 'RetailCo',
                                'company_icon' => '🛍️',
                                'phone' => '+63 922 678 9012',
                                'source' => 'Website',
                                'value' => '₱95,000',
                                'priority' => 'Medium',
                                'status' => 'Qualified',
                                'created' => '2 weeks ago'
                            ],
                            [
                                'id' => '#LEAD-007',
                                'name' => 'Michael Cruz',
                                'initials' => 'MC',
                                'email' => 'mcruz@digitalagency.com',
                                'company' => 'Digital Agency Pro',
                                'company_icon' => '📱',
                                'phone' => '+63 923 789 0123',
                                'source' => 'Referral',
                                'value' => '₱175,000',
                                'priority' => 'High',
                                'status' => 'Contacted',
                                'created' => '3 weeks ago'
                            ],
                            [
                                'id' => '#LEAD-008',
                                'name' => 'Lisa Wong',
                                'initials' => 'LW',
                                'email' => 'lisa@fashionhub.ph',
                                'company' => 'Fashion Hub',
                                'company_icon' => '👗',
                                'phone' => '+63 924 890 1234',
                                'source' => 'Social Media',
                                'value' => '₱65,000',
                                'priority' => 'Low',
                                'status' => 'Lost',
                                'created' => '1 month ago'
                            ]
                        ];

                        foreach ($leads as $lead) {
                            $statusClass = 'status-' . strtolower($lead['status']);
                            $priorityClass = 'priority-' . strtolower($lead['priority']);
                            
                            echo '<tr>';
                            echo '<td><input type="checkbox"></td>';
                            echo '<td><span class="lead-id">' . $lead['id'] . '</span></td>';
                            echo '<td>';
                            echo '<div class="lead-name">';
                            echo '<div class="lead-avatar">' . $lead['initials'] . '</div>';
                            echo '<div class="lead-info">';
                            echo '<div class="lead-info-name">' . $lead['name'] . '</div>';
                            echo '<div class="lead-info-email">' . $lead['email'] . '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td>';
                            echo '<div class="company-cell">';
                            echo '<div class="company-icon">' . $lead['company_icon'] . '</div>';
                            echo '<span>' . $lead['company'] . '</span>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td>' . $lead['phone'] . '</td>';
                            echo '<td><span class="source-badge">' . $lead['source'] . '</span></td>';
                            echo '<td><span class="value-cell">' . $lead['value'] . '</span></td>';
                            echo '<td><span class="priority-badge ' . $priorityClass . '">' . $lead['priority'] . '</span></td>';
                            echo '<td><span class="status-badge ' . $statusClass . '">' . strtoupper($lead['status']) . '</span></td>';
                            echo '<td>' . $lead['created'] . '</td>';
                            echo '<td>';
                            echo '<div class="action-buttons">';
                            echo '<button class="action-btn">View</button>';
                            echo '<button class="action-btn">Edit</button>';
                            echo '<button class="action-btn delete">Delete</button>';
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
                    Showing <strong>1-8</strong> of <strong>1,847</strong> leads
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

    <!-- Add Lead Modal -->
    <div class="modal-overlay" id="leadModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Lead</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="leadForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-input" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-input" placeholder="Enter last name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-input" placeholder="email@example.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-input" placeholder="+63 XXX XXX XXXX">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Company *</label>
                            <input type="text" class="form-input" placeholder="Company name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Job Title</label>
                            <input type="text" class="form-input" placeholder="Position">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lead Source *</label>
                            <select class="filter-select" required>
                                <option value="">Select source</option>
                                <option>Website</option>
                                <option>Referral</option>
                                <option>Social Media</option>
                                <option>Cold Call</option>
                                <option>Email Campaign</option>
                                <option>Trade Show</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deal Value</label>
                            <input type="text" class="form-input" placeholder="₱0.00">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Priority *</label>
                            <select class="filter-select" required>
                                <option value="">Select priority</option>
                                <option>High</option>
                                <option>Medium</option>
                                <option>Low</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status *</label>
                            <select class="filter-select" required>
                                <option value="">Select status</option>
                                <option selected>New</option>
                                <option>Contacted</option>
                                <option>Qualified</option>
                                <option>Negotiating</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" placeholder="Add any additional notes about this lead..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveLead()">Save Lead</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Functions
        function openModal() {
            document.getElementById('leadModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('leadModal').classList.remove('active');
        }

        function saveLead() {
            alert('Lead saved successfully!');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('leadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Table interactions
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
                    console.log('Row clicked:', this);
                }
            });
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

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
        });

        // Select all checkbox
        const selectAll = document.querySelector('.table thead input[type="checkbox"]');
        const rowCheckboxes = document.querySelectorAll('.table tbody input[type="checkbox"]');

        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Filter change listeners
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                console.log('Filter changed:', this.value);
            });
        });

        // Reset filters
        document.querySelector('.filters-section .btn-secondary').addEventListener('click', function() {
            document.querySelectorAll('.filter-select').forEach(select => {
                select.selectedIndex = 0;
            });
        });

        // Action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Action:', action);
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this lead?')) {
                        this.closest('tr').remove();
                    }
                }
            });
        });
    </script>
</body>
</html>