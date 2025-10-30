<?php
session_start();
// Backend: prepare DB-driven data for the Customer Profiles page
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_customer':
                try {
                    // Generate member number following the format in SQL (MEM-XXX)
                    $stmt = $pdo->query("SELECT MAX(CAST(SUBSTRING(MemberNumber, 5) AS UNSIGNED)) as max_num FROM customers WHERE MemberNumber LIKE 'MEM-%'");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $nextNum = ($result['max_num'] ?? 0) + 1;
                    $memberNumber = 'MEM-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
                    
                    $stmt = $pdo->prepare("INSERT INTO customers (MemberNumber, FirstName, LastName, Email, Phone, Address, LoyaltyPoints, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $memberNumber,
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $_POST['address'],
                        intval($_POST['loyalty_points']),
                        'Active'
                    ]);
                    $_SESSION['success_message'] = "Customer added successfully!";
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Error adding customer: " . $e->getMessage();
                }
                break;
                
            case 'delete_customer':
                try {
                    $customerId = $_POST['customer_id'];
                    $stmt = $pdo->prepare("DELETE FROM customers WHERE CustomerID = ?");
                    $stmt->execute([$customerId]);
                    $_SESSION['success_message'] = "Customer deleted successfully!";
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Error deleting customer: " . $e->getMessage();
                }
                break;
                
            case 'update_customer':
                try {
                    $stmt = $pdo->prepare("UPDATE customers SET FirstName = ?, LastName = ?, Email = ?, Phone = ?, Address = ?, LoyaltyPoints = ?, Status = ? WHERE CustomerID = ?");
                    $stmt->execute([
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $_POST['address'],
                        intval($_POST['loyalty_points']),
                        $_POST['status'],
                        $_POST['customer_id']
                    ]);
                    $_SESSION['success_message'] = "Customer updated successfully!";
                } catch (Exception $e) {
                    $_SESSION['error_message'] = "Error updating customer: " . $e->getMessage();
                }
                break;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle export functionality
if (isset($_GET['export'])) {
    try {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['Customer ID', 'Member Number', 'Name', 'Email', 'Phone', 'Loyalty Points', 'Tier', 'Status', 'Total Purchases', 'Last Purchase']);
        
        // Fetch all customers for export
        $stmt = $pdo->query("
            SELECT c.CustomerID, c.MemberNumber, c.FirstName, c.LastName, c.Email, c.Phone, c.LoyaltyPoints, c.Status,
                   COALESCE(SUM(s.TotalAmount), 0) as TotalPurchases,
                   MAX(s.SaleDate) as LastPurchase
            FROM customers c
            LEFT JOIN sales s ON c.CustomerID = s.CustomerID
            GROUP BY c.CustomerID
            ORDER BY c.FirstName
        ");
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Determine tier
            $points = (int)$row['LoyaltyPoints'];
            $tier = 'Bronze';
            if ($points >= 1000) $tier = 'Platinum';
            elseif ($points >= 500) $tier = 'Gold';
            elseif ($points >= 100) $tier = 'Silver';
            
            $name = trim($row['FirstName'] . ' ' . $row['LastName']);
            $lastPurchase = $row['LastPurchase'] ? date('M d, Y', strtotime($row['LastPurchase'])) : 'Never';
            
            fputcsv($output, [
                'CUST-' . $row['CustomerID'],
                $row['MemberNumber'],
                $name,
                $row['Email'],
                $row['Phone'],
                $points,
                $tier,
                $row['Status'],
                '₱' . number_format($row['TotalPurchases'], 2),
                $lastPurchase
            ]);
        }
        
        fclose($output);
        exit;
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error exporting data: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Handle filters
$filters = [
    'type' => $_GET['type'] ?? 'all',
    'tier' => $_GET['tier'] ?? 'all',
    'status' => $_GET['status'] ?? 'all',
    'date_range' => $_GET['date_range'] ?? 'all'
];

$totalCustomers = 0;
$vipCustomers = 0;
$totalLoyaltyPoints = 0;
$newThisMonth = 0;
$customers = [];

try {
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');

    // Build filter conditions
    $whereConditions = [];
    $params = [];
    
    if ($filters['type'] !== 'all') {
        // For type filter, we'll check loyalty points threshold
        if ($filters['type'] === 'VIP') {
            $whereConditions[] = "c.LoyaltyPoints >= 500";
        } else {
            $whereConditions[] = "c.LoyaltyPoints < 500";
        }
    }
    
    if ($filters['status'] !== 'all') {
        $whereConditions[] = "c.Status = ?";
        $params[] = $filters['status'];
    }
    
    if ($filters['date_range'] !== 'all') {
        $dateCondition = "";
        switch ($filters['date_range']) {
            case 'this_month':
                $dateCondition = "c.CreatedAt BETWEEN ? AND ?";
                $params[] = $monthStart . ' 00:00:00';
                $params[] = $monthEnd . ' 23:59:59';
                break;
            case 'last_3_months':
                $dateCondition = "c.CreatedAt >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
                break;
            case 'last_6_months':
                $dateCondition = "c.CreatedAt >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
                break;
            case 'this_year':
                $dateCondition = "YEAR(c.CreatedAt) = YEAR(NOW())";
                break;
        }
        if ($dateCondition) {
            $whereConditions[] = $dateCondition;
        }
    }
    
    // Tier filter
    if ($filters['tier'] !== 'all') {
        $tierCondition = "";
        switch ($filters['tier']) {
            case 'Bronze':
                $tierCondition = "c.LoyaltyPoints < 100";
                break;
            case 'Silver':
                $tierCondition = "c.LoyaltyPoints BETWEEN 100 AND 499";
                break;
            case 'Gold':
                $tierCondition = "c.LoyaltyPoints BETWEEN 500 AND 999";
                break;
            case 'Platinum':
                $tierCondition = "c.LoyaltyPoints >= 1000";
                break;
        }
        if ($tierCondition) {
            $whereConditions[] = $tierCondition;
        }
    }
    
    $whereClause = $whereConditions ? "WHERE " . implode(" AND ", $whereConditions) : "";

    // total customers with filters
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers c $whereClause");
    $stmt->execute($params);
    $totalCustomers = (int)$stmt->fetchColumn();

    // pagination
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $perPage = 10; // rows per page
    $offset = ($page - 1) * $perPage;

    // VIP customers heuristic: customers with high loyalty points
    $vipThreshold = 500; // loyalty points threshold for VIP
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE LoyaltyPoints >= ?");
    $stmt->execute([$vipThreshold]);
    $vipCustomers = (int)$stmt->fetchColumn();

    // total loyalty points
    $stmt = $pdo->query("SELECT IFNULL(SUM(LoyaltyPoints),0) FROM customers");
    $totalLoyaltyPoints = (int)$stmt->fetchColumn();

    // new customers this month
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE CreatedAt BETWEEN ? AND ?");
    $stmt->execute([$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59']);
    $newThisMonth = (int)$stmt->fetchColumn();

    // Fetch customers with filters
    $sql = "SELECT CustomerID, MemberNumber, FirstName, LastName, Email, Phone, LoyaltyPoints, Status, CreatedAt 
            FROM customers c 
            $whereClause 
            ORDER BY FirstName 
            LIMIT ? OFFSET ?";
    
    $stmt = $pdo->prepare($sql);
    $allParams = array_merge($params, [(int)$perPage, (int)$offset]);
    $stmt->execute($allParams);
    $custRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPages = $perPage > 0 ? (int)ceil($totalCustomers / $perPage) : 1;

    foreach ($custRows as $r) {
        $cid = $r['CustomerID'];
        $name = trim((($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')));
        if ($name === '') $name = 'Customer';
        $initials = '';
        foreach (preg_split('/\s+/', $name) as $p) { 
            $initials .= strtoupper(substr($p,0,1)); 
        }

        $points = (int)$r['LoyaltyPoints'];

        // total purchases and last purchase
        $s3 = $pdo->prepare("SELECT IFNULL(SUM(TotalAmount),0) as total, IFNULL(MAX(SaleDate), NULL) as last_sale FROM sales WHERE CustomerID = ?");
        $s3->execute([$cid]);
        $salesRow = $s3->fetch(PDO::FETCH_ASSOC);
        $totalPurchases = $salesRow ? (float)$salesRow['total'] : 0.0;
        $lastPurchase = $salesRow && $salesRow['last_sale'] ? date('M d, Y', strtotime($salesRow['last_sale'])) : '—';

        // determine tier
        $tier = 'Bronze';
        if ($points >= 1000) $tier = 'Platinum';
        elseif ($points >= 500) $tier = 'Gold';
        elseif ($points >= 100) $tier = 'Silver';

        $customers[] = [
            'id' => $cid,
            'member_number' => $r['MemberNumber'],
            'display_id' => '#CUST-' . $cid,
            'name' => $name,
            'first_name' => $r['FirstName'],
            'last_name' => $r['LastName'],
            'initials' => $initials,
            'email' => $r['Email'] ?? '',
            'phone' => $r['Phone'] ?? '',
            'address' => $r['Address'] ?? '',
            'type' => ($points >= $vipThreshold ? 'VIP' : 'Retail'),
            'points' => $points,
            'tier' => $tier,
            'total_purchases' => '₱' . number_format($totalPurchases,2),
            'last_purchase' => $lastPurchase,
            'status' => $r['Status']
        ];
    }

} catch (Exception $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
}

// prepare stats array for UI
$stats_dynamic = [
    ['icon'=>'👥','value'=>number_format($totalCustomers),'label'=>'Total Customers','sublabel'=>'Active accounts','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#dbeafe'],
    ['icon'=>'⭐','value'=>number_format($vipCustomers),'label'=>'VIP Customers','sublabel'=>'Loyalty tier 3+','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#fef3c7'],
    ['icon'=>'🎁','value'=>number_format($totalLoyaltyPoints),'label'=>'Total Loyalty Points','sublabel'=>'Redeemable','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#e9d5ff'],
    ['icon'=>'🆕','value'=>number_format($newThisMonth),'label'=>'New This Month','sublabel'=>'vs last month','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#d1fae5']
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profiles - CRM System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/crmGlobalStyles.css">
    <style>
        /* Additional custom styles for customer-specific elements */
        .contact-info-details {
            display: flex;
            flex-direction: column;
            gap: 4px;
            font-size: 13px;
            color: var(--gray-600);
        }

        .loyalty-points {
            font-weight: 700;
            color: var(--warning);
            font-size: 14px;
        }

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

        .type-vip {
            background: #fce7f3;
            color: #9f1239;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .type-wholesale {
            background: #ddd6fe;
            color: #5b21b6;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .type-retail {
            background: #dbeafe;
            color: #1e40af;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-active {
            color: #10b981;
            font-weight: 600;
        }
        
        .status-inactive {
            color: #ef4444;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            border: none;
            background: #f3f4f6;
            color: #4b5563;
        }
        
        .action-btn.delete {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .action-btn:hover {
            opacity: 0.8;
        }
        
        .notification {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-weight: 500;
        }
        
        .notification.success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .notification.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .filters-form {
            display: contents;
        }
        
        .filter-submit {
            align-self: end;
            padding: 8px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }
        
        .filter-submit:hover {
            background: #2563eb;
        }
        
        /* Modal positioning */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
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
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.active .modal {
            transform: scale(1);
        }
        
        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: between;
        }
        
        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #111827;
            margin: 0;
            flex: 1;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
        }
        
        .close-btn:hover {
            color: #374151;
        }
        
        .modal-body {
            padding: 24px;
        }
        
        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        
        .btn-danger {
            background: #dc2626;
            color: white;
        }
        
        .btn-danger:hover {
            background: #b91c1c;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-label {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }
        
        .form-input, .form-select {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
        }
        
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .customer-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }
        
        .detail-group {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }
        
        .detail-value {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
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
                <li><a href="./customerProfile.php" class="active">Customer Profiles</a></li>
                <li><a href="./loyaltyProgram.php">Loyalty Program</a></li>
                <li><a href="./customerSupport.php">Customer Support</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search customers...">
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notification-badge">5</span>
                </button>
                <a href="./crmProfile.php"><div class="user-avatar">CS</div></a>
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
                    <span>Customer Profiles</span>
                </div>
                <h1 class="page-title">Customer Profiles</h1>
                <p class="page-subtitle">Manage customer data, loyalty points, and purchase history</p>
            </div>
            <div class="header-actions">
                <a href="?export=1" class="btn btn-secondary">
                    <span>📊</span>
                    <span>Export</span>
                </a>
                <button class="btn btn-primary" onclick="openModal('addCustomerModal')">
                    <span>+</span>
                    <span>Add Customer</span>
                </button>
            </div>
        </div>

        <!-- Display notifications -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="notification success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="notification error">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="stats-grid">
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

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                <div class="filters-title">🔍 Filter Customers</div>
                <a href="customerProfile.php" class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset Filters</a>
            </div>
            <form method="GET" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label">Customer Type</label>
                        <select class="filter-select" name="type">
                            <option value="all" <?php echo $filters['type'] === 'all' ? 'selected' : ''; ?>>All Customers</option>
                            <option value="Retail" <?php echo $filters['type'] === 'Retail' ? 'selected' : ''; ?>>Retail</option>
                            <option value="VIP" <?php echo $filters['type'] === 'VIP' ? 'selected' : ''; ?>>VIP</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Loyalty Tier</label>
                        <select class="filter-select" name="tier">
                            <option value="all" <?php echo $filters['tier'] === 'all' ? 'selected' : ''; ?>>All Tiers</option>
                            <option value="Bronze" <?php echo $filters['tier'] === 'Bronze' ? 'selected' : ''; ?>>Bronze (0-99 pts)</option>
                            <option value="Silver" <?php echo $filters['tier'] === 'Silver' ? 'selected' : ''; ?>>Silver (100-499 pts)</option>
                            <option value="Gold" <?php echo $filters['tier'] === 'Gold' ? 'selected' : ''; ?>>Gold (500-999 pts)</option>
                            <option value="Platinum" <?php echo $filters['tier'] === 'Platinum' ? 'selected' : ''; ?>>Platinum (1000+ pts)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Status</label>
                        <select class="filter-select" name="status">
                            <option value="all" <?php echo $filters['status'] === 'all' ? 'selected' : ''; ?>>All Status</option>
                            <option value="Active" <?php echo $filters['status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                            <option value="Inactive" <?php echo $filters['status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Registration Date</label>
                        <select class="filter-select" name="date_range">
                            <option value="all" <?php echo $filters['date_range'] === 'all' ? 'selected' : ''; ?>>All Time</option>
                            <option value="this_month" <?php echo $filters['date_range'] === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                            <option value="last_3_months" <?php echo $filters['date_range'] === 'last_3_months' ? 'selected' : ''; ?>>Last 3 Months</option>
                            <option value="last_6_months" <?php echo $filters['date_range'] === 'last_6_months' ? 'selected' : ''; ?>>Last 6 Months</option>
                            <option value="this_year" <?php echo $filters['date_range'] === 'this_year' ? 'selected' : ''; ?>>This Year</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <button type="submit" class="filter-submit">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Customer Table -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">All Customers (<?php echo number_format($totalCustomers) ?>)</h2>
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
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Customer ID</th>
                            <th>Member Number</th>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Customer Type</th>
                            <th>Loyalty Points</th>
                            <th>Tier</th>
                            <th>Total Purchases</th>
                            <th>Last Purchase</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // $customers array is prepared by backend queries at the top of the file
                        foreach ($customers as $customer) {
                            // Determine tier badge class
                            $tierClass = match($customer['tier']) {
                                'Platinum' => 'tier-platinum',
                                'Gold' => 'tier-gold',
                                'Silver' => 'tier-silver',
                                default => 'tier-bronze'
                            };

                            // Determine type badge class
                            $typeClass = match($customer['type']) {
                                'VIP' => 'type-vip',
                                'Wholesale' => 'type-wholesale',
                                default => 'type-retail'
                            };

                            $statusClass = $customer['status'] === 'Active' ? 'status-active' : 'status-inactive';
                            
                            echo '<tr>';
                            echo '<td><input type="checkbox" class="row-checkbox"></td>';
                            echo '<td><span class="contact-id">' . $customer['display_id'] . '</span></td>';
                            echo '<td>' . $customer['member_number'] . '</td>';
                            echo '<td>';
                            echo '<div class="contact-name-cell">';
                            echo '<div class="contact-avatar">' . $customer['initials'] . '</div>';
                            echo '<div class="contact-name-info">';
                            echo '<div class="contact-name-primary">' . $customer['name'] . '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td>';
                            echo '<div class="contact-info-details">';
                            echo '<div>📧 ' . $customer['email'] . '</div>';
                            echo '<div>📱 ' . $customer['phone'] . '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td><span class="type-badge ' . $typeClass . '">' . strtoupper($customer['type']) . '</span></td>';
                            echo '<td><span class="loyalty-points">' . number_format($customer['points']) . ' pts</span></td>';
                            echo '<td><span class="tier-badge ' . $tierClass . '">' . $customer['tier'] . '</span></td>';
                            echo '<td><span class="value-cell">' . $customer['total_purchases'] . '</span></td>';
                            echo '<td>' . $customer['last_purchase'] . '</td>';
                            echo '<td><span class="' . $statusClass . '">● ' . $customer['status'] . '</span></td>';
                            echo '<td>';
                            echo '<div class="action-buttons">';
                            echo '<button class="action-btn" onclick="viewCustomer(' . $customer['id'] . ')">View</button>';
                            echo '<button class="action-btn" onclick="editCustomer(' . $customer['id'] . ')">Edit</button>';
                            echo '<button class="action-btn delete" onclick="deleteCustomer(' . $customer['id'] . ', \'' . $customer['name'] . '\')">Delete</button>';
                            echo '</div>';
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <?php
                    $start = $offset + 1;
                    $end = min($offset + $perPage, $totalCustomers);
                ?>
                <div class="showing-text">
                    Showing <strong><?php echo $start ?>-<?php echo $end ?></strong> of <strong><?php echo number_format($totalCustomers) ?></strong> customers
                </div>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="btn">‹</a>
                    <?php endif; ?>

                    <?php
                    $maxPagesToShow = 7;
                    $startPage = max(1, min($page - floor($maxPagesToShow/2), max(1, $totalPages - $maxPagesToShow + 1)));
                    $endPage = min($startPage + $maxPagesToShow - 1, $totalPages);
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="btn <?php echo ($i == $page ? 'active' : '') ?>"><?php echo $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="btn">›</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal-overlay" id="addCustomerModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Customer</h3>
                <button class="close-btn" onclick="closeModal('addCustomerModal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="customerForm" method="POST">
                    <input type="hidden" name="action" value="add_customer">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-input" name="first_name" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-input" name="last_name" placeholder="Enter last name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-input" name="email" placeholder="customer@email.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-input" name="phone" placeholder="+63 XXX XXX XXXX" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-input" name="address" placeholder="Street address">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Initial Loyalty Points</label>
                            <input type="number" class="form-input" name="loyalty_points" placeholder="0" value="0" min="0">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('addCustomerModal')">Cancel</button>
                <button class="btn btn-primary" onclick="document.getElementById('customerForm').submit()">Save Customer</button>
            </div>
        </div>
    </div>
    
    <!-- View Customer Modal -->
    <div class="modal-overlay" id="viewCustomerModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Customer Details</h3>
                <button class="close-btn" onclick="closeModal('viewCustomerModal')">✕</button>
            </div>
            <div class="modal-body">
                <div id="customerDetails">
                    <!-- Customer details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('viewCustomerModal')">Close</button>
            </div>
        </div>
    </div>
    
    <!-- Edit Customer Modal -->
    <div class="modal-overlay" id="editCustomerModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Edit Customer</h3>
                <button class="close-btn" onclick="closeModal('editCustomerModal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm" method="POST">
                    <input type="hidden" name="action" value="update_customer">
                    <input type="hidden" name="customer_id" id="editCustomerId">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">First Name *</label>
                            <input type="text" class="form-input" id="editFirstName" name="first_name" placeholder="Enter first name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name *</label>
                            <input type="text" class="form-input" id="editLastName" name="last_name" placeholder="Enter last name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-input" id="editEmail" name="email" placeholder="customer@email.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-input" id="editPhone" name="phone" placeholder="+63 XXX XXX XXXX" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-input" id="editAddress" name="address" placeholder="Street address">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Loyalty Points</label>
                            <input type="number" class="form-input" id="editLoyaltyPoints" name="loyalty_points" placeholder="0" min="0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('editCustomerModal')">Cancel</button>
                <button class="btn btn-primary" onclick="document.getElementById('editCustomerForm').submit()">Update Customer</button>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Deletion</h3>
                <button class="close-btn" onclick="closeModal('deleteModal')">✕</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete customer <span id="deleteCustomerName"></span>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete_customer">
                    <input type="hidden" name="customer_id" id="deleteCustomerId">
                    <button class="btn btn-danger" type="submit">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Customer data from PHP
        const customers = <?php echo json_encode($customers); ?>;
        
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }
        
        function viewCustomer(customerId) {
            const customer = customers.find(c => c.id === customerId);
            if (customer) {
                const detailsHtml = `
                    <div class="customer-details">
                        <div class="detail-group">
                            <div class="detail-label">Customer ID</div>
                            <div class="detail-value">${customer.display_id}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Member Number</div>
                            <div class="detail-value">${customer.member_number}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Name</div>
                            <div class="detail-value">${customer.name}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">${customer.email}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">${customer.phone}</div>
                        </div>
                        <div class="detail-group full-width">
                            <div class="detail-label">Address</div>
                            <div class="detail-value">${customer.address || 'Not provided'}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Customer Type</div>
                            <div class="detail-value">${customer.type}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Loyalty Points</div>
                            <div class="detail-value">${customer.points.toLocaleString()} pts</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Tier</div>
                            <div class="detail-value">${customer.tier}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Total Purchases</div>
                            <div class="detail-value">${customer.total_purchases}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Last Purchase</div>
                            <div class="detail-value">${customer.last_purchase}</div>
                        </div>
                        <div class="detail-group">
                            <div class="detail-label">Status</div>
                            <div class="detail-value">${customer.status}</div>
                        </div>
                    </div>
                `;
                document.getElementById('customerDetails').innerHTML = detailsHtml;
                openModal('viewCustomerModal');
            }
        }
        
        function editCustomer(customerId) {
            const customer = customers.find(c => c.id === customerId);
            if (customer) {
                document.getElementById('editCustomerId').value = customer.id;
                document.getElementById('editFirstName').value = customer.first_name;
                document.getElementById('editLastName').value = customer.last_name;
                document.getElementById('editEmail').value = customer.email;
                document.getElementById('editPhone').value = customer.phone;
                document.getElementById('editAddress').value = customer.address || '';
                document.getElementById('editLoyaltyPoints').value = customer.points;
                document.getElementById('editStatus').value = customer.status;
                openModal('editCustomerModal');
            }
        }
        
        function deleteCustomer(customerId, customerName) {
            document.getElementById('deleteCustomerName').textContent = customerName;
            document.getElementById('deleteCustomerId').value = customerId;
            openModal('deleteModal');
        }

        // Close modal on overlay click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
            // Implement search logic here
        });

        // Select all checkbox
        const selectAll = document.getElementById('select-all');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                rowCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        }

        // Filter change listeners
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                console.log('Filter changed:', this.value);
                // The form will submit when the Apply Filters button is clicked
            });
        });

        // Table row clicks
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
                    const customerId = this.querySelector('.contact-id').textContent.replace('#CUST-', '');
                    viewCustomer(parseInt(customerId));
                }
            });
        });
    </script>
</body>
</html>