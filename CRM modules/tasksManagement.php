<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks Management - CRM System</title>
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

        /* Tabs Section */
        .tabs-section {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tabs {
            display: flex;
            gap: 8px;
            background: var(--gray-100);
            padding: 4px;
            border-radius: var(--radius);
        }

        .tab-btn {
            padding: 8px 20px;
            border: none;
            background: transparent;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-600);
        }

        .tab-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        .filter-group {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            background: white;
            font-size: 13px;
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

        /* Task Board (Kanban Style) */
        .task-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .task-column {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .task-column-header {
            padding: 20px;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(to bottom, white, var(--gray-50));
        }

        .task-column-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .task-column-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .task-count {
            background: var(--gray-100);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-700);
        }

        .task-list {
            padding: 16px;
            min-height: 300px;
            max-height: 600px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .task-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            cursor: pointer;
            transition: var(--transition);
        }

        .task-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .task-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .task-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary);
        }

        .task-content {
            flex: 1;
            padding: 0 12px;
        }

        .task-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .task-description {
            font-size: 13px;
            color: var(--gray-600);
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            font-size: 12px;
            color: var(--gray-600);
        }

        .task-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .task-date {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .task-date.overdue {
            color: var(--danger);
            font-weight: 600;
        }

        .task-date.today {
            color: var(--warning);
            font-weight: 600;
        }

        .task-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 10px;
        }

        .priority-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
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

        .task-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .task-tag {
            padding: 4px 8px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        /* List View */
        .list-view {
            display: none;
        }

        .list-view.active {
            display: block;
        }

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

        .task-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 13px;
        }

        .task-name-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .task-name-primary {
            font-weight: 600;
            color: var(--gray-900);
        }

        .task-name-secondary {
            font-size: 12px;
            color: var(--gray-600);
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

        .status-todo {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-in-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .status-review {
            background: #e9d5ff;
            color: #6b21a8;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .assignee-cell {
            display: flex;
            align-items: center;
            gap: 8px;
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
            max-width: 700px;
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

            .task-board {
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

            .form-grid {
                grid-template-columns: 1fr;
            }

            .tabs-section {
                flex-direction: column;
                gap: 16px;
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
                <li><a href="#" class="active">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search tasks...">
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
                    <span>Tasks Management</span>
                </div>
                <h1 class="page-title">Tasks & Activities</h1>
                <p class="page-subtitle">Manage and track all your tasks and follow-ups</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📋</span>
                    <span>My Tasks</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Reports</span>
                </button>
                <button class="btn btn-primary" onclick="openModal()">
                    <span>+</span>
                    <span>Add Task</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '📝',
                    'value' => '84',
                    'label' => 'Total Tasks',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '⏳',
                    'value' => '23',
                    'label' => 'In Progress',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '✅',
                    'value' => '45',
                    'label' => 'Completed',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '🔴',
                    'value' => '8',
                    'label' => 'Overdue',
                    'color' => '#fee2e2'
                ],
                [
                    'icon' => '📅',
                    'value' => '12',
                    'label' => 'Due Today',
                    'color' => '#e9d5ff'
                ],
                [
                    'icon' => '👤',
                    'value' => '28',
                    'label' => 'Assigned to Me',
                    'color' => '#ddd6fe'
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

        <div class="tabs-section">
            <div class="tabs">
                <button class="tab-btn active" onclick="switchView('board')">
                    <span>📊</span> Board View
                </button>
                <button class="tab-btn" onclick="switchView('list')">
                    <span>📋</span> List View
                </button>
            </div>
            <div class="filter-group">
                <select class="filter-select">
                    <option>All Tasks</option>
                    <option>My Tasks</option>
                    <option>Team Tasks</option>
                </select>
                <select class="filter-select">
                    <option>All Priority</option>
                    <option>High</option>
                    <option>Medium</option>
                    <option>Low</option>
                </select>
                <select class="filter-select">
                    <option>All Time</option>
                    <option>Today</option>
                    <option>This Week</option>
                    <option>This Month</option>
                </select>
            </div>
        </div>

        <!-- Board View (Kanban) -->
        <div class="task-board" id="boardView">
            <?php
            $taskColumns = [
                [
                    'status' => 'To Do',
                    'icon' => '📋',
                    'count' => 16,
                    'tasks' => [
                        [
                            'id' => '#TASK-001',
                            'title' => 'Follow up with TechCorp lead',
                            'description' => 'Schedule demo meeting and send proposal',
                            'priority' => 'High',
                            'due_date' => 'Today',
                            'assignee' => 'JS',
                            'tags' => ['Sales', 'Follow-up'],
                            'related' => '🏢 TechCorp'
                        ],
                        [
                            'id' => '#TASK-005',
                            'title' => 'Prepare Q4 sales report',
                            'description' => 'Compile data from all regions',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 15',
                            'assignee' => 'MG',
                            'tags' => ['Report'],
                            'related' => '📊 Analytics'
                        ],
                        [
                            'id' => '#TASK-009',
                            'title' => 'Update CRM database',
                            'description' => 'Add new contacts from trade show',
                            'priority' => 'Low',
                            'due_date' => 'Dec 20',
                            'assignee' => 'AR',
                            'tags' => ['Data Entry'],
                            'related' => '💼 Operations'
                        ]
                    ]
                ],
                [
                    'status' => 'In Progress',
                    'icon' => '⏳',
                    'count' => 23,
                    'tasks' => [
                        [
                            'id' => '#TASK-002',
                            'title' => 'Create proposal for Innovation Labs',
                            'description' => 'Include pricing and timeline details',
                            'priority' => 'High',
                            'due_date' => 'Today',
                            'assignee' => 'RC',
                            'tags' => ['Proposal', 'Urgent'],
                            'related' => '💡 Innovation Labs'
                        ],
                        [
                            'id' => '#TASK-006',
                            'title' => 'Client onboarding - Global Solutions',
                            'description' => 'Set up account and training schedule',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 16',
                            'assignee' => 'DL',
                            'tags' => ['Onboarding'],
                            'related' => '🌐 Global Solutions'
                        ]
                    ]
                ],
                [
                    'status' => 'Review',
                    'icon' => '👀',
                    'count' => 8,
                    'tasks' => [
                        [
                            'id' => '#TASK-003',
                            'title' => 'Review contract terms with Legal',
                            'description' => 'Enterprise agreement for new client',
                            'priority' => 'High',
                            'due_date' => 'Dec 14',
                            'assignee' => 'ST',
                            'tags' => ['Legal', 'Contract'],
                            'related' => '📄 Enterprise Group'
                        ],
                        [
                            'id' => '#TASK-007',
                            'title' => 'Marketing campaign approval',
                            'description' => 'Review Q1 campaign materials',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 18',
                            'assignee' => 'MC',
                            'tags' => ['Marketing'],
                            'related' => '📱 Campaign'
                        ]
                    ]
                ],
                [
                    'status' => 'Completed',
                    'icon' => '✅',
                    'count' => 45,
                    'tasks' => [
                        [
                            'id' => '#TASK-004',
                            'title' => 'Send welcome email to new clients',
                            'description' => 'Include onboarding resources',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 10',
                            'assignee' => 'LW',
                            'tags' => ['Email', 'Completed'],
                            'related' => '📧 Communications'
                        ],
                        [
                            'id' => '#TASK-008',
                            'title' => 'Update product pricing',
                            'description' => 'Reflect new 2025 rates',
                            'priority' => 'Low',
                            'due_date' => 'Dec 08',
                            'assignee' => 'JS',
                            'tags' => ['Pricing'],
                            'related' => '💰 Finance'
                        ]
                    ]
                ]
            ];

            foreach ($taskColumns as $column) {
                echo '<div class="task-column">';
                echo '<div class="task-column-header">';
                echo '<div class="task-column-title">';
                echo '<span class="task-column-name">' . $column['icon'] . ' ' . $column['status'] . '</span>';
                echo '<span class="task-count">' . $column['count'] . '</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="task-list">';
                
                foreach ($column['tasks'] as $task) {
                    $priorityClass = 'priority-' . strtolower($task['priority']);
                    $dateClass = '';
                    if ($task['due_date'] === 'Today') {
                        $dateClass = 'today';
                    } elseif (strtotime($task['due_date']) < time()) {
                        $dateClass = 'overdue';
                    }
                    
                    echo '<div class="task-card">';
                    echo '<div class="task-card-header">';
                    echo '<input type="checkbox" class="task-checkbox" ' . ($column['status'] === 'Completed' ? 'checked' : '') . '>';
                    echo '<div class="task-content">';
                    echo '<div class="task-title">' . $task['title'] . '</div>';
                    echo '<div class="task-description">' . $task['description'] . '</div>';
                    echo '<div class="task-meta">';
                    echo '<div class="task-meta-item">';
                    echo '<div class="task-avatar">' . $task['assignee'] . '</div>';
                    echo '</div>';
                    echo '<div class="task-meta-item">';
                    echo '<span class="priority-badge ' . $priorityClass . '">' . $task['priority'] . '</span>';
                    echo '</div>';
                    echo '<div class="task-meta-item task-date ' . $dateClass . '">';
                    echo '📅 ' . $task['due_date'];
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="task-tags">';
                    foreach ($task['tags'] as $tag) {
                        echo '<span class="task-tag">' . $tag . '</span>';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- List View -->
        <div class="list-view" id="listView">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="section-title">All Tasks (84)</h2>
                    <div class="card-actions">
                        <button class="icon-btn" title="Filter">🔽</button>
                        <button class="icon-btn" title="Sort">⇅</button>
                        <button class="icon-btn" title="Settings">⚙️</button>
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox"></th>
                                <th>Task ID</th>
                                <th>Task Name</th>
                                <th>Related To</th>
                                <th>Assignee</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allTasks = [
                                [
                                    'id' => '#TASK-001',
                                    'name' => 'Follow up with TechCorp lead',
                                    'description' => 'Schedule demo meeting and send proposal',
                                    'related' => 'TechCorp Philippines',
                                    'related_icon' => '🏢',
                                    'assignee' => 'John Santos',
                                    'assignee_initials' => 'JS',
                                    'priority' => 'High',
                                    'status' => 'To Do',
                                    'due_date' => 'Today'
                                ],
                                [
                                    'id' => '#TASK-002',
                                    'name' => 'Create proposal for Innovation Labs',
                                    'description' => 'Include pricing and timeline details',
                                    'related' => 'Innovation Labs',
                                    'related_icon' => '💡',
                                    'assignee' => 'Robert Chen',
                                    'assignee_initials' => 'RC',
                                    'priority' => 'High',
                                    'status' => 'In Progress',
                                    'due_date' => 'Today'
                                ],
                                [
                                    'id' => '#TASK-003',
                                    'name' => 'Review contract terms with Legal',
                                    'description' => 'Enterprise agreement for new client',
                                    'related' => 'Enterprise Group',
                                    'related_icon' => '🏛️',
                                    'assignee' => 'Sarah Tan',
                                    'assignee_initials' => 'ST',
                                    'priority' => 'High',
                                    'status' => 'Review',
                                    'due_date' => 'Dec 14, 2024'
                                ],
                                [
                                    'id' => '#TASK-004',
                                    'name' => 'Send welcome email to new clients',
                                    'description' => 'Include onboarding resources',
                                    'related' => 'Communications',
                                    'related_icon' => '📧',
                                    'assignee' => 'Lisa Wong',
                                    'assignee_initials' => 'LW',
                                    'priority' => 'Medium',
                                    'status' => 'Completed',
                                    'due_date' => 'Dec 10, 2024'
                                ],
                                [
                                    'id' => '#TASK-005',
                                    'name' => 'Prepare Q4 sales report',
                                    'description' => 'Compile data from all regions',
                                    'related' => 'Analytics',
                                    'related_icon' => '📊',
                                    'assignee' => 'Maria Garcia',
                                    'assignee_initials' => 'MG',
                                    'priority' => 'Medium',
                                    'status' => 'To Do',
                                    'due_date' => 'Dec 15, 2024'
                                ],
                                [
                                    'id' => '#TASK-006',
                                    'name' => 'Client onboarding - Global Solutions',
                                    'description' => 'Set up account and training schedule',
                                    'related' => 'Global Solutions',
                                    'related_icon' => '🌐',
                                    'assignee' => 'David Lim',
                                    'assignee_initials' => 'DL',
                                    'priority' => 'Medium',
                                    'status' => 'In Progress',
                                    'due_date' => 'Dec 16, 2024'
                                ],
                                [
                                    'id' => '#TASK-007',
                                    'name' => 'Marketing campaign approval',
                                    'description' => 'Review Q1 campaign materials',
                                    'related' => 'Marketing Campaign',
                                    'related_icon' => '📱',
                                    'assignee' => 'Michael Cruz',
                                    'assignee_initials' => 'MC',
                                    'priority' => 'Medium',
                                    'status' => 'Review',
                                    'due_date' => 'Dec 18, 2024'
                                ],
                                [
                                    'id' => '#TASK-008',
                                    'name' => 'Update product pricing',
                                    'description' => 'Reflect new 2025 rates',
                                    'related' => 'Finance',
                                    'related_icon' => '💰',
                                    'assignee' => 'John Santos',
                                    'assignee_initials' => 'JS',
                                    'priority' => 'Low',
                                    'status' => 'Completed',
                                    'due_date' => 'Dec 08, 2024'
                                ],
                                [
                                    'id' => '#TASK-009',
                                    'name' => 'Update CRM database',
                                    'description' => 'Add new contacts from trade show',
                                    'related' => 'Operations',
                                    'related_icon' => '💼',
                                    'assignee' => 'Ana Reyes',
                                    'assignee_initials' => 'AR',
                                    'priority' => 'Low',
                                    'status' => 'To Do',
                                    'due_date' => 'Dec 20, 2024'
                                ]
                            ];

                            foreach ($allTasks as $task) {
                                $statusClass = 'status-' . strtolower(str_replace(' ', '-', $task['status']));
                                $priorityClass = 'priority-' . strtolower($task['priority']);
                                
                                echo '<tr>';
                                echo '<td><input type="checkbox" ' . ($task['status'] === 'Completed' ? 'checked' : '') . '></td>';
                                echo '<td><span class="task-id">' . $task['id'] . '</span></td>';
                                echo '<td>';
                                echo '<div class="task-name-cell">';
                                echo '<div class="task-name-primary">' . $task['name'] . '</div>';
                                echo '<div class="task-name-secondary">' . $task['description'] . '</div>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="company-cell">';
                                echo '<div class="company-icon">' . $task['related_icon'] . '</div>';
                                echo '<span>' . $task['related'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="assignee-cell">';
                                echo '<div class="task-avatar">' . $task['assignee_initials'] . '</div>';
                                echo '<span>' . $task['assignee'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td><span class="priority-badge ' . $priorityClass . '">' . $task['priority'] . '</span></td>';
                                echo '<td><span class="status-badge ' . $statusClass . '">' . strtoupper($task['status']) . '</span></td>';
                                echo '<td>' . $task['due_date'] . '</td>';
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
                        Showing <strong>1-9</strong> of <strong>84</strong> tasks
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
    </div>

    <!-- Add Task Modal -->
    <div class="modal-overlay" id="taskModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Task</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Task Name *</label>
                            <input type="text" class="form-input" placeholder="Enter task name" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" placeholder="Add task description and details..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Related To</label>
                            <select class="filter-select">
                                <option value="">Select relation</option>
                                <option>Lead</option>
                                <option>Contact</option>
                                <option>Deal</option>
                                <option>Company</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Select Record</label>
                            <input type="text" class="form-input" placeholder="Search...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Task Type *</label>
                            <select class="filter-select" required>
                                <option value="">Select type</option>
                                <option>Call</option>
                                <option>Email</option>
                                <option>Meeting</option>
                                <option>Follow-up</option>
                                <option>Other</option>
                            </select>
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
                                <option selected>To Do</option>
                                <option>In Progress</option>
                                <option>Review</option>
                                <option>Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Due Date *</label>
                            <input type="date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Assign To *</label>
                            <select class="filter-select" required>
                                <option value="">Select assignee</option>
                                <option>John Santos</option>
                                <option>Maria Garcia</option>
                                <option>Robert Chen</option>
                                <option>Ana Reyes</option>
                                <option>David Lim</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reminder</label>
                            <select class="filter-select">
                                <option>No reminder</option>
                                <option>1 hour before</option>
                                <option>1 day before</option>
                                <option>1 week before</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-input" placeholder="Add tags (comma separated)">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveTask()">Create Task</button>
            </div>
        </div>
    </div>

    <script>
        // View switching
        function switchView(view) {
            const boardView = document.getElementById('boardView');
            const listView = document.getElementById('listView');
            const tabButtons = document.querySelectorAll('.tab-btn');

            tabButtons.forEach(btn => btn.classList.remove('active'));

            if (view === 'board') {
                boardView.style.display = 'grid';
                listView.classList.remove('active');
                tabButtons[0].classList.add('active');
            } else {
                boardView.style.display = 'none';
                listView.classList.add('active');
                tabButtons[1].classList.add('active');
            }
        }

        // Modal Functions
        function openModal() {
            document.getElementById('taskModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('taskModal').classList.remove('active');
        }

        function saveTask() {
            alert('Task created successfully!');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('taskModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
        });

        // Task checkbox handling
        document.querySelectorAll('.task-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskCard = this.closest('.task-card');
                if (this.checked) {
                    taskCard.style.opacity = '0.6';
                    taskCard.querySelector('.task-title').style.textDecoration = 'line-through';
                } else {
                    taskCard.style.opacity = '1';
                    taskCard.querySelector('.task-title').style.textDecoration = 'none';
                }
            });
        });

        // Table checkbox handling
        const selectAll = document.querySelector('.table thead input[type="checkbox"]');
        const rowCheckboxes = document.querySelectorAll('.table tbody input[type="checkbox"]');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        // Pagination
        document.querySelectorAll('.pagination button').forEach(btn => {
            btn.addEventListener('click', function() {
                if (this.textContent !== '‹' && this.textContent !== '›') {
                    document.querySelectorAll('.pagination button').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        // Filter change listeners
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                console.log('Filter changed:', this.value);
            });
        });

        // Task card clicks
        document.querySelectorAll('.task-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.task-checkbox')) {
                    console.log('Task card clicked:', this.querySelector('.task-title').textContent);
                }
            });
        });

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Action:', action);
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this task?')) {
                        this.closest('tr').remove();
                    }
                }
            });
        });

        // Table row clicks
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
                    console.log('Row clicked');
                }
            });
        });
    </script>
</body>
</html>