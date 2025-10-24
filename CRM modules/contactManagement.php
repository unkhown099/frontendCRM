<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management - CRM System</title>
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
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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

        /* View Toggle */
        .view-controls {
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

        /* Grid View */
        .contacts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .contact-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            transition: var(--transition);
            cursor: pointer;
        }

        .contact-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .contact-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }

        .contact-avatar-large {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 24px;
            flex-shrink: 0;
            box-shadow: var(--shadow-md);
        }

        .contact-info {
            flex: 1;
        }

        .contact-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .contact-title {
            font-size: 13px;
            color: var(--gray-600);
            margin-bottom: 2px;
        }

        .contact-company {
            font-size: 13px;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .contact-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
        }

        .contact-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--gray-700);
        }

        .contact-detail-icon {
            width: 32px;
            height: 32px;
            background: var(--gray-100);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .contact-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .contact-tag {
            padding: 4px 10px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .contact-actions {
            display: flex;
            gap: 8px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
        }

        .contact-action-btn {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid var(--gray-300);
            background: white;
            border-radius: var(--radius);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            color: var(--gray-700);
        }

        .contact-action-btn:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        /* List View (Table) */
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

        .contact-id {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 13px;
        }

        .contact-name-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .contact-avatar {
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

        .contact-name-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .contact-name-primary {
            font-weight: 600;
            color: var(--gray-900);
        }

        .contact-name-secondary {
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

        .type-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .type-customer {
            background: #d1fae5;
            color: #065f46;
        }

        .type-partner {
            background: #ddd6fe;
            color: #5b21b6;
        }

        .type-vendor {
            background: #fef3c7;
            color: #92400e;
        }

        .type-prospect {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-active {
            color: var(--success);
            font-weight: 600;
        }

        .status-inactive {
            color: var(--gray-400);
            font-weight: 600;
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

            .contacts-grid {
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

            .view-controls {
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
                <li><a href="#" class="active">Contacts</a></li>
                <li><a href="./dealsManagement.php">Deals</a></li>
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search contacts...">
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
                    <span>Contact Management</span>
                </div>
                <h1 class="page-title">Contact Management</h1>
                <p class="page-subtitle">Organize and manage all your business contacts</p>
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
                    <span>Add Contact</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '👥',
                    'value' => '2,456',
                    'label' => 'Total Contacts',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '✅',
                    'value' => '1,834',
                    'label' => 'Active Contacts',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '🤝',
                    'value' => '892',
                    'label' => 'Customers',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '🎯',
                    'value' => '534',
                    'label' => 'Prospects',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '⭐',
                    'value' => '156',
                    'label' => 'Partners',
                    'color' => '#e9d5ff'
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

        <div class="view-controls">
            <div class="view-toggle">
                <button class="view-btn active" onclick="switchView('grid')">
                    <span>📱</span> Grid View
                </button>
                <button class="view-btn" onclick="switchView('list')">
                    <span>📋</span> List View
                </button>
            </div>
            <div class="filter-group">
                <select class="filter-select">
                    <option>All Types</option>
                    <option>Customer</option>
                    <option>Partner</option>
                    <option>Vendor</option>
                    <option>Prospect</option>
                </select>
                <select class="filter-select">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
                <select class="filter-select">
                    <option>Sort by Name</option>
                    <option>Sort by Date</option>
                    <option>Sort by Company</option>
                </select>
            </div>
        </div>

        <!-- Grid View -->
        <div class="contacts-grid" id="gridView">
            <?php
            $contacts = [
                [
                    'id' => '#CONT-001',
                    'name' => 'John Santos',
                    'initials' => 'JS',
                    'title' => 'Chief Technology Officer',
                    'company' => 'TechCorp Philippines',
                    'email' => 'john.santos@techcorp.ph',
                    'phone' => '+63 917 123 4567',
                    'location' => 'Manila, Philippines',
                    'type' => 'Customer',
                    'status' => 'Active',
                    'tags' => ['VIP', 'Tech']
                ],
                [
                    'id' => '#CONT-002',
                    'name' => 'Maria Garcia',
                    'initials' => 'MG',
                    'title' => 'Marketing Director',
                    'company' => 'Global Solutions Inc',
                    'email' => 'maria.g@globalsol.com',
                    'phone' => '+63 918 234 5678',
                    'location' => 'Makati, Philippines',
                    'type' => 'Partner',
                    'status' => 'Active',
                    'tags' => ['Marketing', 'Strategic']
                ],
                [
                    'id' => '#CONT-003',
                    'name' => 'Robert Chen',
                    'initials' => 'RC',
                    'title' => 'Founder & CEO',
                    'company' => 'Innovation Labs',
                    'email' => 'r.chen@innovationlabs.io',
                    'phone' => '+63 919 345 6789',
                    'location' => 'Taguig, Philippines',
                    'type' => 'Customer',
                    'status' => 'Active',
                    'tags' => ['VIP', 'Innovation']
                ],
                [
                    'id' => '#CONT-004',
                    'name' => 'Ana Reyes',
                    'initials' => 'AR',
                    'title' => 'Business Development Manager',
                    'company' => 'StartUp Innovations',
                    'email' => 'ana@startupinc.ph',
                    'phone' => '+63 920 456 7890',
                    'location' => 'Quezon City, Philippines',
                    'type' => 'Prospect',
                    'status' => 'Active',
                    'tags' => ['Startup']
                ],
                [
                    'id' => '#CONT-005',
                    'name' => 'David Lim',
                    'initials' => 'DL',
                    'title' => 'Operations Manager',
                    'company' => 'Enterprise Solutions Group',
                    'email' => 'david.lim@entgroup.com',
                    'phone' => '+63 921 567 8901',
                    'location' => 'Pasig, Philippines',
                    'type' => 'Vendor',
                    'status' => 'Active',
                    'tags' => ['Operations']
                ],
                [
                    'id' => '#CONT-006',
                    'name' => 'Sarah Tan',
                    'initials' => 'ST',
                    'title' => 'Sales Director',
                    'company' => 'RetailCo Philippines',
                    'email' => 'sarah.tan@retailco.ph',
                    'phone' => '+63 922 678 9012',
                    'location' => 'Cebu City, Philippines',
                    'type' => 'Customer',
                    'status' => 'Active',
                    'tags' => ['Retail', 'Sales']
                ]
            ];

            foreach ($contacts as $contact) {
                echo '<div class="contact-card">';
                echo '<div class="contact-header">';
                echo '<div class="contact-avatar-large">' . $contact['initials'] . '</div>';
                echo '<div class="contact-info">';
                echo '<div class="contact-name">' . $contact['name'] . '</div>';
                echo '<div class="contact-title">' . $contact['title'] . '</div>';
                echo '<div class="contact-company">🏢 ' . $contact['company'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="contact-details">';
                echo '<div class="contact-detail">';
                echo '<div class="contact-detail-icon">📧</div>';
                echo '<span>' . $contact['email'] . '</span>';
                echo '</div>';
                echo '<div class="contact-detail">';
                echo '<div class="contact-detail-icon">📱</div>';
                echo '<span>' . $contact['phone'] . '</span>';
                echo '</div>';
                echo '<div class="contact-detail">';
                echo '<div class="contact-detail-icon">📍</div>';
                echo '<span>' . $contact['location'] . '</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="contact-tags">';
                foreach ($contact['tags'] as $tag) {
                    echo '<span class="contact-tag">' . $tag . '</span>';
                }
                echo '</div>';
                echo '<div class="contact-actions">';
                echo '<button class="contact-action-btn">Message</button>';
                echo '<button class="contact-action-btn">Call</button>';
                echo '<button class="contact-action-btn">View</button>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- List View -->
        <div class="list-view" id="listView">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="section-title">All Contacts (2,456)</h2>
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
                                <th>Contact ID</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($contacts as $contact) {
                                $typeClass = 'type-' . strtolower($contact['type']);
                                $statusClass = 'status-' . strtolower($contact['status']);
                                
                                echo '<tr>';
                                echo '<td><input type="checkbox"></td>';
                                echo '<td><span class="contact-id">' . $contact['id'] . '</span></td>';
                                echo '<td>';
                                echo '<div class="contact-name-cell">';
                                echo '<div class="contact-avatar">' . $contact['initials'] . '</div>';
                                echo '<div class="contact-name-info">';
                                echo '<div class="contact-name-primary">' . $contact['name'] . '</div>';
                                echo '<div class="contact-name-secondary">' . $contact['title'] . '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="company-cell">';
                                echo '<div class="company-icon">🏢</div>';
                                echo '<span>' . $contact['company'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>' . $contact['email'] . '</td>';
                                echo '<td>' . $contact['phone'] . '</td>';
                                echo '<td><span class="type-badge ' . $typeClass . '">' . strtoupper($contact['type']) . '</span></td>';
                                echo '<td><span class="' . $statusClass . '">● ' . $contact['status'] . '</span></td>';
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
                        Showing <strong>1-6</strong> of <strong>2,456</strong> contacts
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

    <!-- Add Contact Modal -->
    <div class="modal-overlay" id="contactModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Contact</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
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
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-input" placeholder="+63 XXX XXX XXXX" required>
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
                            <label class="form-label">Contact Type *</label>
                            <select class="filter-select" required>
                                <option value="">Select type</option>
                                <option>Customer</option>
                                <option>Partner</option>
                                <option>Vendor</option>
                                <option>Prospect</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status *</label>
                            <select class="filter-select" required>
                                <option value="">Select status</option>
                                <option selected>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-input" placeholder="Street address">
                        </div>
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="form-input" placeholder="City">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-input" placeholder="Country">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-input" placeholder="Add tags (comma separated)">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" placeholder="Add any additional notes about this contact..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveContact()">Save Contact</button>
            </div>
        </div>
    </div>

    <script>
        // View switching
        function switchView(view) {
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const viewButtons = document.querySelectorAll('.view-btn');

            viewButtons.forEach(btn => btn.classList.remove('active'));

            if (view === 'grid') {
                gridView.style.display = 'grid';
                listView.classList.remove('active');
                viewButtons[0].classList.add('active');
            } else {
                gridView.style.display = 'none';
                listView.classList.add('active');
                viewButtons[1].classList.add('active');
            }
        }

        // Modal Functions
        function openModal() {
            document.getElementById('contactModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('contactModal').classList.remove('active');
        }

        function saveContact() {
            alert('Contact saved successfully!');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('contactModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
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

        // Contact card clicks
        document.querySelectorAll('.contact-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('button')) {
                    console.log('Contact card clicked');
                }
            });
        });

        // Contact action buttons
        document.querySelectorAll('.contact-action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Contact action:', action);
            });
        });

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Action:', action);
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this contact?')) {
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