<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deals Management - CRM System</title>
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

        /* Pipeline Board */
        .pipeline-board {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            padding-bottom: 20px;
            margin-bottom: 32px;
        }

        .pipeline-column {
            flex: 0 0 320px;
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow);
        }

        .pipeline-header {
            padding: 20px;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(to bottom, white, var(--gray-50));
        }

        .pipeline-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .pipeline-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .pipeline-count {
            background: var(--gray-100);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-700);
        }

        .pipeline-value {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        .pipeline-deals {
            padding: 16px;
            min-height: 400px;
            max-height: 600px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .deal-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            cursor: grab;
            transition: var(--transition);
        }

        .deal-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .deal-card:active {
            cursor: grabbing;
        }

        .deal-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .deal-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .deal-company {
            font-size: 12px;
            color: var(--gray-600);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .deal-value {
            font-size: 18px;
            font-weight: 700;
            color: var(--success);
        }

        .deal-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
        }

        .deal-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--gray-600);
        }

        .deal-avatar {
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

        .deal-priority {
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

        /* Table View */
        .view-toggle-section {
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

        .view-toggle {
            display: flex;
            gap: 8px;
            background: var(--gray-100);
            padding: 4px;
            border-radius: var(--radius);
        }

        .view-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-600);
        }

        .view-btn.active {
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

        .table-view {
            display: none;
        }

        .table-view.active {
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

        .deal-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 13px;
        }

        .deal-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .deal-name-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .deal-name-primary {
            font-weight: 600;
            color: var(--gray-900);
        }

        .deal-name-secondary {
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
            font-weight: 700;
            color: var(--success);
            font-size: 15px;
        }

        .stage-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .stage-prospecting {
            background: #dbeafe;
            color: #1e40af;
        }

        .stage-qualification {
            background: #fef3c7;
            color: #92400e;
        }

        .stage-proposal {
            background: #e9d5ff;
            color: #6b21a8;
        }

        .stage-negotiation {
            background: #fce7f3;
            color: #9f1239;
        }

        .stage-closed {
            background: #d1fae5;
            color: #065f46;
        }

        .probability-bar {
            width: 100%;
            height: 6px;
            background: var(--gray-200);
            border-radius: 3px;
            overflow: hidden;
        }

        .probability-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: var(--transition);
        }

        .probability-text {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-700);
            margin-top: 4px;
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
            min-height: 80px;
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

            .pipeline-board {
                flex-direction: column;
            }

            .pipeline-column {
                flex: 1;
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

            .view-toggle-section {
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
                <li><a href="#" class="active">Deals</a></li>
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search deals...">
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
                    <span>Deals Management</span>
                </div>
                <h1 class="page-title">Deals Pipeline</h1>
                <p class="page-subtitle">Track and manage your sales opportunities</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Analytics</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📥</span>
                    <span>Export</span>
                </button>
                <button class="btn btn-primary" onclick="openModal()">
                    <span>+</span>
                    <span>Add Deal</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '💼',
                    'value' => '142',
                    'label' => 'Active Deals',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '💰',
                    'value' => '₱8.4M',
                    'label' => 'Total Value',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '📈',
                    'value' => '₱2.1M',
                    'label' => 'This Month',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '✅',
                    'value' => '34',
                    'label' => 'Won This Month',
                    'color' => '#e9d5ff'
                ],
                [
                    'icon' => '🎯',
                    'value' => '68%',
                    'label' => 'Win Rate',
                    'color' => '#fef3c7'
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

        <div class="view-toggle-section">
            <div class="view-toggle">
                <button class="view-btn active" onclick="switchView('pipeline')">
                    <span>📊</span> Pipeline View
                </button>
                <button class="view-btn" onclick="switchView('table')">
                    <span>📋</span> Table View
                </button>
            </div>
            <div class="filter-group">
                <select class="filter-select">
                    <option>All Owners</option>
                    <option>My Deals</option>
                    <option>Team Deals</option>
                </select>
                <select class="filter-select">
                    <option>All Time</option>
                    <option>This Week</option>
                    <option>This Month</option>
                    <option>This Quarter</option>
                </select>
            </div>
        </div>

        <!-- Pipeline View -->
        <div class="pipeline-board" id="pipelineView">
            <?php
            $pipeline = [
                [
                    'stage' => 'Prospecting',
                    'count' => 28,
                    'value' => '₱1.2M',
                    'color' => '#dbeafe',
                    'deals' => [
                        [
                            'id' => '#DEAL-001',
                            'title' => 'Enterprise Software Deal',
                            'company' => 'TechCorp Philippines',
                            'value' => '₱250,000',
                            'owner' => 'JS',
                            'priority' => 'High',
                            'date' => '2 days ago'
                        ],
                        [
                            'id' => '#DEAL-005',
                            'title' => 'Cloud Migration Project',
                            'company' => 'Digital Solutions Inc',
                            'value' => '₱180,000',
                            'owner' => 'MG',
                            'priority' => 'Medium',
                            'date' => '5 days ago'
                        ],
                        [
                            'id' => '#DEAL-009',
                            'title' => 'Website Redesign',
                            'company' => 'Retail Pro',
                            'value' => '₱95,000',
                            'owner' => 'AR',
                            'priority' => 'Low',
                            'date' => '1 week ago'
                        ]
                    ]
                ],
                [
                    'stage' => 'Qualification',
                    'count' => 35,
                    'value' => '₱2.3M',
                    'color' => '#fef3c7',
                    'deals' => [
                        [
                            'id' => '#DEAL-002',
                            'title' => 'CRM Implementation',
                            'company' => 'Global Solutions',
                            'value' => '₱420,000',
                            'owner' => 'RC',
                            'priority' => 'High',
                            'date' => '3 days ago'
                        ],
                        [
                            'id' => '#DEAL-006',
                            'title' => 'Marketing Automation',
                            'company' => 'StartUp Ventures',
                            'value' => '₱175,000',
                            'owner' => 'DL',
                            'priority' => 'Medium',
                            'date' => '4 days ago'
                        ]
                    ]
                ],
                [
                    'stage' => 'Proposal',
                    'count' => 24,
                    'value' => '₱2.8M',
                    'color' => '#e9d5ff',
                    'deals' => [
                        [
                            'id' => '#DEAL-003',
                            'title' => 'Data Analytics Platform',
                            'company' => 'Innovation Labs',
                            'value' => '₱650,000',
                            'owner' => 'ST',
                            'priority' => 'High',
                            'date' => '1 day ago'
                        ],
                        [
                            'id' => '#DEAL-007',
                            'title' => 'Mobile App Development',
                            'company' => 'Fashion Hub',
                            'value' => '₱285,000',
                            'owner' => 'MC',
                            'priority' => 'Medium',
                            'date' => '3 days ago'
                        ]
                    ]
                ],
                [
                    'stage' => 'Negotiation',
                    'count' => 18,
                    'value' => '₱1.6M',
                    'color' => '#fce7f3',
                    'deals' => [
                        [
                            'id' => '#DEAL-004',
                            'title' => 'ERP System Upgrade',
                            'company' => 'Enterprise Group',
                            'value' => '₱890,000',
                            'owner' => 'LW',
                            'priority' => 'High',
                            'date' => 'Today'
                        ]
                    ]
                ],
                [
                    'stage' => 'Closed Won',
                    'count' => 37,
                    'value' => '₱3.5M',
                    'color' => '#d1fae5',
                    'deals' => [
                        [
                            'id' => '#DEAL-008',
                            'title' => 'Security Infrastructure',
                            'company' => 'FinTech Solutions',
                            'value' => '₱725,000',
                            'owner' => 'JS',
                            'priority' => 'High',
                            'date' => 'Yesterday'
                        ]
                    ]
                ]
            ];

            foreach ($pipeline as $column) {
                echo '<div class="pipeline-column">';
                echo '<div class="pipeline-header">';
                echo '<div class="pipeline-title">';
                echo '<span class="pipeline-name">' . $column['stage'] . '</span>';
                echo '<span class="pipeline-count">' . $column['count'] . '</span>';
                echo '</div>';
                echo '<div class="pipeline-value">' . $column['value'] . '</div>';
                echo '</div>';
                echo '<div class="pipeline-deals">';
                
                foreach ($column['deals'] as $deal) {
                    $priorityClass = 'priority-' . strtolower($deal['priority']);
                    echo '<div class="deal-card">';
                    echo '<div class="deal-header">';
                    echo '<div>';
                    echo '<div class="deal-title">' . $deal['title'] . '</div>';
                    echo '<div class="deal-company">🏢 ' . $deal['company'] . '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="deal-value">' . $deal['value'] . '</div>';
                    echo '<div class="deal-meta">';
                    echo '<div class="deal-meta-item">';
                    echo '<div class="deal-avatar">' . $deal['owner'] . '</div>';
                    echo '<span>' . $deal['date'] . '</span>';
                    echo '</div>';
                    echo '<div class="deal-meta-item">';
                    echo '<span class="deal-priority ' . $priorityClass . '">' . $deal['priority'] . '</span>';
                    echo '<span>Priority</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Table View -->
        <div class="table-view" id="tableView">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="section-title">All Deals (142)</h2>
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
                                <th>Deal ID</th>
                                <th>Deal Name</th>
                                <th>Company</th>
                                <th>Value</th>
                                <th>Stage</th>
                                <th>Probability</th>
                                <th>Owner</th>
                                <th>Priority</th>
                                <th>Close Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allDeals = [
                                [
                                    'id' => '#DEAL-001',
                                    'name' => 'Enterprise Software Deal',
                                    'company' => 'TechCorp Philippines',
                                    'company_icon' => '🏢',
                                    'value' => '₱250,000',
                                    'stage' => 'Prospecting',
                                    'probability' => 20,
                                    'owner' => 'John Santos',
                                    'owner_initials' => 'JS',
                                    'priority' => 'High',
                                    'close_date' => 'Dec 15, 2024'
                                ],
                                [
                                    'id' => '#DEAL-002',
                                    'name' => 'CRM Implementation',
                                    'company' => 'Global Solutions',
                                    'company_icon' => '🌐',
                                    'value' => '₱420,000',
                                    'stage' => 'Qualification',
                                    'probability' => 40,
                                    'owner' => 'Maria Garcia',
                                    'owner_initials' => 'MG',
                                    'priority' => 'High',
                                    'close_date' => 'Dec 20, 2024'
                                ],
                                [
                                    'id' => '#DEAL-003',
                                    'name' => 'Data Analytics Platform',
                                    'company' => 'Innovation Labs',
                                    'company_icon' => '💡',
                                    'value' => '₱650,000',
                                    'stage' => 'Proposal',
                                    'probability' => 60,
                                    'owner' => 'Robert Chen',
                                    'owner_initials' => 'RC',
                                    'priority' => 'High',
                                    'close_date' => 'Dec 25, 2024'
                                ],
                                [
                                    'id' => '#DEAL-004',
                                    'name' => 'ERP System Upgrade',
                                    'company' => 'Enterprise Group',
                                    'company_icon' => '🏛️',
                                    'value' => '₱890,000',
                                    'stage' => 'Negotiation',
                                    'probability' => 80,
                                    'owner' => 'David Lim',
                                    'owner_initials' => 'DL',
                                    'priority' => 'High',
                                    'close_date' => 'Jan 05, 2025'
                                ],
                                [
                                    'id' => '#DEAL-005',
                                    'name' => 'Cloud Migration Project',
                                    'company' => 'Digital Solutions Inc',
                                    'company_icon' => '☁️',
                                    'value' => '₱180,000',
                                    'stage' => 'Prospecting',
                                    'probability' => 15,
                                    'owner' => 'Ana Reyes',
                                    'owner_initials' => 'AR',
                                    'priority' => 'Medium',
                                    'close_date' => 'Jan 10, 2025'
                                ],
                                [
                                    'id' => '#DEAL-006',
                                    'name' => 'Marketing Automation',
                                    'company' => 'StartUp Ventures',
                                    'company_icon' => '🚀',
                                    'value' => '₱175,000',
                                    'stage' => 'Qualification',
                                    'probability' => 35,
                                    'owner' => 'Sarah Tan',
                                    'owner_initials' => 'ST',
                                    'priority' => 'Medium',
                                    'close_date' => 'Jan 15, 2025'
                                ],
                                [
                                    'id' => '#DEAL-007',
                                    'name' => 'Mobile App Development',
                                    'company' => 'Fashion Hub',
                                    'company_icon' => '👗',
                                    'value' => '₱285,000',
                                    'stage' => 'Proposal',
                                    'probability' => 55,
                                    'owner' => 'Michael Cruz',
                                    'owner_initials' => 'MC',
                                    'priority' => 'Medium',
                                    'close_date' => 'Jan 20, 2025'
                                ],
                                [
                                    'id' => '#DEAL-008',
                                    'name' => 'Security Infrastructure',
                                    'company' => 'FinTech Solutions',
                                    'company_icon' => '🔐',
                                    'value' => '₱725,000',
                                    'stage' => 'Closed',
                                    'probability' => 100,
                                    'owner' => 'Lisa Wong',
                                    'owner_initials' => 'LW',
                                    'priority' => 'High',
                                    'close_date' => 'Dec 01, 2024'
                                ]
                            ];

                            foreach ($allDeals as $deal) {
                                $stageClass = 'stage-' . strtolower($deal['stage']);
                                $priorityClass = 'priority-' . strtolower($deal['priority']);
                                
                                echo '<tr>';
                                echo '<td><input type="checkbox"></td>';
                                echo '<td><span class="deal-id">' . $deal['id'] . '</span></td>';
                                echo '<td>';
                                echo '<div class="deal-name-cell">';
                                echo '<div class="deal-name-info">';
                                echo '<div class="deal-name-primary">' . $deal['name'] . '</div>';
                                echo '<div class="deal-name-secondary">' . $deal['id'] . '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="company-cell">';
                                echo '<div class="company-icon">' . $deal['company_icon'] . '</div>';
                                echo '<span>' . $deal['company'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td><span class="value-cell">' . $deal['value'] . '</span></td>';
                                echo '<td><span class="stage-badge ' . $stageClass . '">' . strtoupper($deal['stage']) . '</span></td>';
                                echo '<td>';
                                echo '<div class="probability-bar">';
                                echo '<div class="probability-fill" style="width: ' . $deal['probability'] . '%"></div>';
                                echo '</div>';
                                echo '<div class="probability-text">' . $deal['probability'] . '%</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="deal-name-cell">';
                                echo '<div class="deal-avatar">' . $deal['owner_initials'] . '</div>';
                                echo '<span>' . $deal['owner'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td><span class="deal-priority ' . $priorityClass . '">' . $deal['priority'] . '</span></td>';
                                echo '<td>' . $deal['close_date'] . '</td>';
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
                        Showing <strong>1-8</strong> of <strong>142</strong> deals
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

    <!-- Add Deal Modal -->
    <div class="modal-overlay" id="dealModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Deal</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="dealForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Deal Name *</label>
                            <input type="text" class="form-input" placeholder="Enter deal name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Company *</label>
                            <input type="text" class="form-input" placeholder="Select or enter company" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Contact Person *</label>
                            <input type="text" class="form-input" placeholder="Select contact" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deal Value *</label>
                            <input type="text" class="form-input" placeholder="₱0.00" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Expected Close Date *</label>
                            <input type="date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pipeline Stage *</label>
                            <select class="filter-select" required>
                                <option value="">Select stage</option>
                                <option>Prospecting</option>
                                <option>Qualification</option>
                                <option>Proposal</option>
                                <option>Negotiation</option>
                                <option>Closed Won</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Probability %</label>
                            <input type="number" class="form-input" placeholder="0-100" min="0" max="100">
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
                            <label class="form-label">Deal Owner *</label>
                            <select class="filter-select" required>
                                <option value="">Assign to</option>
                                <option>John Santos</option>
                                <option>Maria Garcia</option>
                                <option>Robert Chen</option>
                                <option>Ana Reyes</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" placeholder="Add deal description, notes, and relevant information..."></textarea>
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
                <button class="btn btn-primary" onclick="saveDeal()">Create Deal</button>
            </div>
        </div>
    </div>

    <script>
        // View switching
        function switchView(view) {
            const pipelineView = document.getElementById('pipelineView');
            const tableView = document.getElementById('tableView');
            const viewButtons = document.querySelectorAll('.view-btn');

            viewButtons.forEach(btn => btn.classList.remove('active'));

            if (view === 'pipeline') {
                pipelineView.style.display = 'flex';
                tableView.classList.remove('active');
                viewButtons[0].classList.add('active');
            } else {
                pipelineView.style.display = 'none';
                tableView.classList.add('active');
                viewButtons[1].classList.add('active');
            }
        }

        // Modal Functions
        function openModal() {
            document.getElementById('dealModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('dealModal').classList.remove('active');
        }

        function saveDeal() {
            alert('Deal created successfully!');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('dealModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
        });

        // Deal card drag and drop (basic implementation)
        const dealCards = document.querySelectorAll('.deal-card');
        dealCards.forEach(card => {
            card.addEventListener('dragstart', function(e) {
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.innerHTML);
                this.style.opacity = '0.4';
            });

            card.addEventListener('dragend', function() {
                this.style.opacity = '1';
            });

            card.setAttribute('draggable', 'true');
        });

        const pipelineDeals = document.querySelectorAll('.pipeline-deals');
        pipelineDeals.forEach(column => {
            column.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            });

            column.addEventListener('drop', function(e) {
                e.preventDefault();
                console.log('Deal dropped in:', this);
            });
        });

        // Select all checkbox
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

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Action:', action);
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this deal?')) {
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

        // Deal card clicks
        document.querySelectorAll('.deal-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('button')) {
                    console.log('Deal card clicked:', this.querySelector('.deal-title').textContent);
                }
            });
        });
    </script>
</body>
</html>