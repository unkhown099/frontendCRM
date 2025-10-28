<?php
session_start();

// Backend: connect to Database singleton
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

// Handle AJAX requests for real-time updates
if (isset($_GET['action']) && $_GET['action'] === 'get_ticket_updates') {
    header('Content-Type: application/json');
    
    $lastUpdateTime = $_GET['last_update'] ?? '1970-01-01 00:00:00';
    $response = ['updated' => false, 'tickets' => [], 'stats' => []];
    
    try {
        // Check for updated tickets since last update
        $possibleTicketTables = ['support_tickets','tickets','customer_support','helpdesk_tickets'];
        $ticketTable = null;
        foreach ($possibleTicketTables as $tbl) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $stmt->execute([$tbl]);
            if ($stmt->fetchColumn() > 0) { $ticketTable = $tbl; break; }
        }
        
        if ($ticketTable) {
            $stmt = $pdo->prepare("
                SELECT t.TicketID AS id, t.CustomerID, t.Subject AS subject, t.Description AS description, 
                       t.Category AS category, t.Priority AS priority, t.Status AS status, 
                       t.AssignedTo AS assigned_id, t.CreatedAt AS created, t.UpdatedAt AS updated, 
                       t.SaleRef AS sale_ref, c.FirstName, c.LastName, 
                       u.FirstName AS agent_first, u.LastName AS agent_last
                FROM `{$ticketTable}` t
                LEFT JOIN customers c ON t.CustomerID = c.CustomerID
                LEFT JOIN users u ON t.AssignedTo = u.UserID
                WHERE t.UpdatedAt > ? 
                ORDER BY t.UpdatedAt DESC
            ");
            $stmt->execute([$lastUpdateTime]);
            $updatedTickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($updatedTickets) > 0) {
                $response['updated'] = true;
                foreach ($updatedTickets as $r) {
                    $custName = trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')) ?: 'Customer';
                    $agentName = trim(($r['agent_first'] ?? '') . ' ' . ($r['agent_last'] ?? '')) ?: '—';
                    $response['tickets'][] = [
                        'id' => '#TKT-' . ($r['id'] ?? '0'),
                        'customer' => $custName,
                        'customer_initials' => implode('', array_map(fn($p)=>strtoupper(substr($p,0,1)), array_filter(explode(' ',$custName)))),
                        'subject' => $r['subject'] ?? '',
                        'description' => $r['description'] ?? '',
                        'category' => $r['category'] ?? 'Inquiry',
                        'priority' => $r['priority'] ?? 'Medium',
                        'status' => $r['status'] ?? 'Open',
                        'assigned' => $agentName,
                        'assigned_initials' => implode('', array_map(fn($p)=>strtoupper(substr($p,0,1)), array_filter(explode(' ',$agentName)))),
                        'created' => isset($r['created']) ? date('M d, Y H:i', strtotime($r['created'])) : '',
                        'updated' => isset($r['updated']) ? date('M d, Y H:i', strtotime($r['updated'])) : '',
                        'sale_ref' => $r['sale_ref'] ?? '-'
                    ];
                }
                
                // Get updated stats
                $stats = getTicketStats($pdo, $ticketTable);
                $response['stats'] = $stats;
            }
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}

// Handle filter requests
if (isset($_GET['action']) && $_GET['action'] === 'filter_tickets') {
    header('Content-Type: application/json');
    
    $filters = [
        'status' => $_GET['status'] ?? '',
        'priority' => $_GET['priority'] ?? '',
        'category' => $_GET['category'] ?? '',
        'date_range' => $_GET['date_range'] ?? '',
        'search' => $_GET['search'] ?? ''
    ];
    
    $response = ['tickets' => [], 'total_count' => 0];
    
    try {
        $possibleTicketTables = ['support_tickets','tickets','customer_support','helpdesk_tickets'];
        $ticketTable = null;
        foreach ($possibleTicketTables as $tbl) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $stmt->execute([$tbl]);
            if ($stmt->fetchColumn() > 0) { $ticketTable = $tbl; break; }
        }
        
        if ($ticketTable) {
            $filteredTickets = getFilteredTickets($pdo, $ticketTable, $filters);
            $response['tickets'] = $filteredTickets['tickets'];
            $response['total_count'] = $filteredTickets['total_count'];
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
    }
    
    echo json_encode($response);
    exit();
}

// Function to get ticket statistics
function getTicketStats($pdo, $ticketTable) {
    $stats = [];
    
    // Total tickets
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$ticketTable}`");
    $stats['totalTickets'] = (int)$stmt->fetchColumn();

    // Open tickets
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$ticketTable}` WHERE Status IN ('Open','open')");
    $stats['openTickets'] = (int)$stmt->fetchColumn();

    // In Progress
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$ticketTable}` WHERE Status IN ('In Progress','in progress','Progress')");
    $stats['inProgress'] = (int)$stmt->fetchColumn();

    // Resolved
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$ticketTable}` WHERE Status IN ('Resolved','Closed','closed')");
    $stats['resolved'] = (int)$stmt->fetchColumn();

    // Average response time
    $stmt = $pdo->query("SELECT AVG(TIMESTAMPDIFF(HOUR, CreatedAt, FirstResponseAt)) FROM `{$ticketTable}` WHERE FirstResponseAt IS NOT NULL");
    $stats['avgResponseHours'] = round((float)$stmt->fetchColumn(), 1);

    // Satisfaction rate
    $stmt = $pdo->query("SELECT COUNT(*) FROM `{$ticketTable}` WHERE FeedbackScore IS NOT NULL");
    $fbCount = (int)$stmt->fetchColumn();
    $stats['satisfactionRate'] = 0;
    if ($fbCount > 0) {
        $stmt = $pdo->query("SELECT AVG(FeedbackScore) FROM `{$ticketTable}` WHERE FeedbackScore IS NOT NULL");
        $avgScore = (float)$stmt->fetchColumn();
        $stats['satisfactionRate'] = round(($avgScore / 5) * 100, 1);
    }
    
    return $stats;
}

// Function to get filtered tickets
function getFilteredTickets($pdo, $ticketTable, $filters) {
    $whereConditions = [];
    $params = [];
    
    // Status filter
    if (!empty($filters['status']) && $filters['status'] !== 'All Status') {
        $statusMap = [
            'Open' => ['Open','open'],
            'In Progress' => ['In Progress','in progress','Progress'],
            'Resolved' => ['Resolved'],
            'Closed' => ['Closed','closed']
        ];
        
        if (isset($statusMap[$filters['status']])) {
            $placeholders = implode(',', array_fill(0, count($statusMap[$filters['status']]), '?'));
            $whereConditions[] = "t.Status IN ($placeholders)";
            $params = array_merge($params, $statusMap[$filters['status']]);
        }
    }
    
    // Priority filter
    if (!empty($filters['priority']) && $filters['priority'] !== 'All Priority') {
        $whereConditions[] = "t.Priority = ?";
        $params[] = $filters['priority'];
    }
    
    // Category filter
    if (!empty($filters['category']) && $filters['category'] !== 'All Categories') {
        $whereConditions[] = "t.Category = ?";
        $params[] = $filters['category'];
    }
    
    // Date range filter
    if (!empty($filters['date_range']) && $filters['date_range'] !== 'All Time') {
        $dateConditions = [
            'Today' => "DATE(t.CreatedAt) = CURDATE()",
            'This Week' => "YEARWEEK(t.CreatedAt) = YEARWEEK(CURDATE())",
            'This Month' => "MONTH(t.CreatedAt) = MONTH(CURDATE()) AND YEAR(t.CreatedAt) = YEAR(CURDATE())"
        ];
        
        if (isset($dateConditions[$filters['date_range']])) {
            $whereConditions[] = $dateConditions[$filters['date_range']];
        }
    }
    
    // Search filter
    if (!empty($filters['search'])) {
        $searchTerm = '%' . $filters['search'] . '%';
        $whereConditions[] = "(t.Subject LIKE ? OR t.Description LIKE ? OR c.FirstName LIKE ? OR c.LastName LIKE ?)";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    }
    
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    }
    
    // Get total count for pagination
    $countSql = "SELECT COUNT(*) FROM `{$ticketTable}` t LEFT JOIN customers c ON t.CustomerID = c.CustomerID $whereClause";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $totalCount = (int)$stmt->fetchColumn();
    
    // Get filtered tickets
    $ticketsSql = "
        SELECT t.TicketID AS id, t.CustomerID, t.Subject AS subject, t.Description AS description, 
               t.Category AS category, t.Priority AS priority, t.Status AS status, 
               t.AssignedTo AS assigned_id, t.CreatedAt AS created, t.UpdatedAt AS updated, 
               t.SaleRef AS sale_ref, c.FirstName, c.LastName, 
               u.FirstName AS agent_first, u.LastName AS agent_last
        FROM `{$ticketTable}` t
        LEFT JOIN customers c ON t.CustomerID = c.CustomerID
        LEFT JOIN users u ON t.AssignedTo = u.UserID
        $whereClause
        ORDER BY t.CreatedAt DESC 
        LIMIT 50
    ";
    
    $stmt = $pdo->prepare($ticketsSql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $tickets = [];
    foreach ($rows as $r) {
        $custName = trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')) ?: 'Customer';
        $agentName = trim(($r['agent_first'] ?? '') . ' ' . ($r['agent_last'] ?? '')) ?: '—';
        $tickets[] = [
            'id' => '#TKT-' . ($r['id'] ?? '0'),
            'customer' => $custName,
            'customer_initials' => implode('', array_map(fn($p)=>strtoupper(substr($p,0,1)), array_filter(explode(' ',$custName)))),
            'subject' => $r['subject'] ?? '',
            'description' => $r['description'] ?? '',
            'category' => $r['category'] ?? 'Inquiry',
            'priority' => $r['priority'] ?? 'Medium',
            'status' => $r['status'] ?? 'Open',
            'assigned' => $agentName,
            'assigned_initials' => implode('', array_map(fn($p)=>strtoupper(substr($p,0,1)), array_filter(explode(' ',$agentName)))),
            'created' => isset($r['created']) ? date('M d, Y H:i', strtotime($r['created'])) : '',
            'updated' => isset($r['updated']) ? date('M d, Y H:i', strtotime($r['updated'])) : '',
            'sale_ref' => $r['sale_ref'] ?? '-'
        ];
    }
    
    return ['tickets' => $tickets, 'total_count' => $totalCount];
}

// Prepare data holders
$totalTickets = 0;
$openTickets = 0;
$inProgress = 0;
$resolved = 0;
$avgResponseHours = 0;
$satisfactionRate = 0;
$tickets = [];
$customersForSelect = [];
$agentsForSelect = [];
$storesForSelect = [];

// Detect likely ticket tables; fallback if none
$possibleTicketTables = ['support_tickets','tickets','customer_support','helpdesk_tickets'];
$ticketTable = null;
foreach ($possibleTicketTables as $tbl) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$tbl]);
    if ($stmt->fetchColumn() > 0) { $ticketTable = $tbl; break; }
}

try {
    if ($ticketTable) {
        // Get initial stats
        $stats = getTicketStats($pdo, $ticketTable);
        $totalTickets = $stats['totalTickets'];
        $openTickets = $stats['openTickets'];
        $inProgress = $stats['inProgress'];
        $resolved = $stats['resolved'];
        $avgResponseHours = $stats['avgResponseHours'];
        $satisfactionRate = $stats['satisfactionRate'];

        // Get initial tickets
        $ticketsData = getFilteredTickets($pdo, $ticketTable, []);
        $tickets = $ticketsData['tickets'];
    } else {
        // fallback: derive tickets from recent sales/inquiries
        $stmt = $pdo->prepare("SELECT s.SaleID, s.SaleDate, c.FirstName, c.LastName FROM sales s LEFT JOIN customers c ON s.CustomerID = c.CustomerID ORDER BY s.SaleDate DESC LIMIT 50");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $i => $r) {
            $custName = trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')) ?: 'Customer';
            $tickets[] = [
                'id' => '#TKT-F' . ($r['SaleID'] ?? $i),
                'customer' => $custName,
                'customer_initials' => implode('', array_map(fn($p)=>strtoupper(substr($p,0,1)), array_filter(explode(' ',$custName)))),
                'subject' => 'Order inquiry',
                'description' => 'Customer inquiry related to sale',
                'category' => 'Order Issue',
                'priority' => 'Medium',
                'status' => 'Open',
                'assigned' => 'Unassigned',
                'assigned_initials' => '—',
                'created' => isset($r['SaleDate']) ? date('M d, Y H:i', strtotime($r['SaleDate'])) : '',
                'updated' => isset($r['SaleDate']) ? date('M d, Y H:i', strtotime($r['SaleDate'])) : '',
                'sale_ref' => isset($r['SaleID']) ? ('#SALE-' . $r['SaleID']) : '-'
            ];
        }

        $totalTickets = count($tickets);
        $openTickets = $totalTickets;
    }

    // agents list
    $stmt = $pdo->query("SELECT UserID, FirstName, LastName FROM users WHERE Role IN ('Support','Agent','Admin') ORDER BY FirstName LIMIT 200");
    $agentsForSelect = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // customers for modal
    $stmt = $pdo->query("SELECT CustomerID, FirstName, LastName, Email FROM customers ORDER BY FirstName LIMIT 200");
    $customersForSelect = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // stores for modal (if exists)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute(['stores']);
    if ($stmt->fetchColumn() > 0) {
        $stmt2 = $pdo->query("SELECT StoreID, StoreName FROM stores ORDER BY StoreName LIMIT 100");
        $storesForSelect = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Exception $e) {
    // keep defaults on error
}

// Prepare stats array for UI
$stats_dynamic = [
    ['icon'=>'🎫','value'=>number_format($totalTickets),'label'=>'Total Tickets','sublabel'=>'All time','trend'=>'+0.0%','trend_dir'=>'up','color'=>'#dbeafe'],
    ['icon'=>'🔓','value'=>number_format($openTickets),'label'=>'Open Tickets','sublabel'=>'Awaiting response','trend'=>'','trend_dir'=>'down','color'=>'#fee2e2'],
    ['icon'=>'⏳','value'=>number_format($inProgress),'label'=>'In Progress','sublabel'=>'Being handled','trend'=>'','trend_dir'=>'up','color'=>'#fef3c7'],
    ['icon'=>'✅','value'=>number_format($resolved),'label'=>'Resolved','sublabel'=>'Closed tickets','trend'=>'','trend_dir'=>'up','color'=>'#d1fae5'],
    ['icon'=>'⏱️','value'=>($avgResponseHours ? $avgResponseHours . ' hrs' : 'N/A'),'label'=>'Avg Response Time','sublabel'=>'First response','trend'=>'','trend_dir'=>'up','color'=>'#e9d5ff'],
    ['icon'=>'📈','value'=>($satisfactionRate ? $satisfactionRate . '%' : 'N/A'),'label'=>'Satisfaction Rate','sublabel'=>'Customer feedback','trend'=>'','trend_dir'=>'up','color'=>'#ddd6fe']
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support - CRM System</title>
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
                <li><a href="./CrmDashboard.php">Dashboard</a></li>
                <li><a href="./customerProfile.php">Customer Profiles</a></li>
                <li><a href="./loyaltyProgram.php">Loyalty Program</a></li>
                <li><a href="./customerSupport.php" class="active">Customer Support</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search tickets..." id="globalSearch">
                </div>
                <button class="notification-btn">
                    🔔
                    <span class="notification-badge">12</span>
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
                    <span>Customer Support</span>
                </div>
                <h1 class="page-title">Customer Support</h1>
                <p class="page-subtitle">Manage support tickets and customer inquiries</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Reports</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📥</span>
                    <span>Export</span>
                </button>
                <button class="btn btn-primary" onclick="openTicketModal()">
                    <span>+</span>
                    <span>Create Ticket</span>
                </button>
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

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                <div class="filters-title">🔍 Filter Support Tickets</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;" onclick="resetFilters()">Reset Filters</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select" id="statusFilter">
                        <option value="All Status">All Status</option>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Priority</label>
                    <select class="filter-select" id="priorityFilter">
                        <option value="All Priority">All Priority</option>
                        <option value="Critical">Critical</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Category</label>
                    <select class="filter-select" id="categoryFilter">
                        <option value="All Categories">All Categories</option>
                        <option value="Product Issue">Product Issue</option>
                        <option value="Order Issue">Order Issue</option>
                        <option value="Refund Request">Refund Request</option>
                        <option value="Complaint">Complaint</option>
                        <option value="Inquiry">Inquiry</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date Range</label>
                    <select class="filter-select" id="dateFilter">
                        <option value="All Time">All Time</option>
                        <option value="Today">Today</option>
                        <option value="This Week">This Week</option>
                        <option value="This Month">This Month</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Support Tickets Table -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Support Tickets (<span id="ticketCount"><?php echo count($tickets); ?></span>)</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Refresh" onclick="refreshTickets()">🔄</button>
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
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Ticket ID</th>
                            <th>Customer</th>
                            <th>Subject</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                            <th>Last Update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ticketsTableBody">
                        <?php
                        // $tickets populated by backend earlier
                        foreach ($tickets as $ticket) {
                            echo renderTicketRow($ticket);
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="table-footer">
                <div class="showing-text">
                    Showing <strong id="showingRange">1-<?php echo min(50, count($tickets)); ?></strong> of <strong id="totalTicketsCount"><?php echo count($tickets); ?></strong> tickets
                </div>
                <div class="pagination">
                    <button onclick="changePage('prev')">‹</button>
                    <button class="active">1</button>
                    <button onclick="changePage(2)">2</button>
                    <button onclick="changePage(3)">3</button>
                    <button onclick="changePage(4)">4</button>
                    <button onclick="changePage(5)">5</button>
                    <button onclick="changePage('next')">›</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Ticket Modal -->
    <div class="modal-overlay" id="ticketModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Create Support Ticket</h3>
                <button class="close-btn" onclick="closeTicketModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="ticketForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Select Customer *</label>
                            <select class="filter-select" name="customer_id" required>
                                <option value="">Choose customer</option>
                                <?php
                                foreach ($customersForSelect as $c) {
                                    $cid = $c['CustomerID'] ?? $c['CustomerID'];
                                    $name = trim(($c['FirstName'] ?? '') . ' ' . ($c['LastName'] ?? ''));
                                    $email = $c['Email'] ?? '';
                                    echo '<option value="' . htmlspecialchars($cid) . '">' . htmlspecialchars($name) . ($email ? ' - ' . htmlspecialchars($email) : '') . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Category *</label>
                            <select class="filter-select" name="category" required>
                                <option value="">Select category</option>
                                <option value="product">Product Issue</option>
                                <option value="order">Order Issue</option>
                                <option value="refund">Refund Request</option>
                                <option value="complaint">Complaint</option>
                                <option value="inquiry">Inquiry</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Priority *</label>
                            <select class="filter-select" name="priority" required>
                                <option value="">Select priority</option>
                                <option value="critical">Critical</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Subject *</label>
                            <input type="text" class="form-input" name="subject" placeholder="Brief description of the issue" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Description *</label>
                            <textarea class="form-textarea" name="description" placeholder="Detailed description of the issue or inquiry..." required style="min-height: 120px;"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Related Sale ID</label>
                            <input type="text" class="form-input" name="sale_id" placeholder="#SALE-0000 (optional)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Store Location</label>
                            <select class="filter-select" name="store_id">
                                <option value="">Select store</option>
                                <?php
                                foreach ($storesForSelect as $s) {
                                    $sid = $s['StoreID'] ?? $s['StoreID'];
                                    $sname = $s['StoreName'] ?? $s['StoreName'];
                                    echo '<option value="' . htmlspecialchars($sid) . '">' . htmlspecialchars($sname) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Assign To *</label>
                            <select class="filter-select" name="assigned_to" required>
                                <option value="">Select agent</option>
                                <?php
                                foreach ($agentsForSelect as $a) {
                                    $aid = $a['UserID'] ?? $a['UserID'];
                                    $aname = trim(($a['FirstName'] ?? '') . ' ' . ($a['LastName'] ?? ''));
                                    echo '<option value="' . htmlspecialchars($aid) . '">' . htmlspecialchars($aname) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Internal Notes</label>
                            <textarea class="form-textarea" name="notes" placeholder="Add internal notes (not visible to customer)..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeTicketModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveTicket()">Create Ticket</button>
            </div>
        </div>
    </div>

    <style>
        /* Ticket-specific styles */
        .ticket-subject {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .ticket-subject-title {
            font-weight: 600;
            color: var(--gray-900);
            font-size: 14px;
        }

        .ticket-subject-desc {
            font-size: 12px;
            color: var(--gray-600);
            line-height: 1.4;
        }

        .ticket-sale-ref {
            font-size: 11px;
            color: var(--primary);
            font-weight: 600;
            margin-top: 4px;
        }

        /* Ticket Status Badges */
        .ticket-status-open {
            background: #dbeafe;
            color: #1e40af;
        }

        .ticket-status-progress {
            background: #fef3c7;
            color: #92400e;
        }

        .ticket-status-resolved {
            background: #d1fae5;
            color: #065f46;
        }

        .ticket-status-closed {
            background: #f3f4f6;
            color: #4b5563;
        }

        /* Priority Badges */
        .ticket-priority-critical {
            background: #fecaca;
            color: #991b1b;
        }

        /* Category Badges */
        .category-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-product {
            background: #fee2e2;
            color: #991b1b;
        }

        .category-order {
            background: #dbeafe;
            color: #1e40af;
        }

        .category-refund {
            background: #fef3c7;
            color: #92400e;
        }

        .category-complaint {
            background: #fecaca;
            color: #7f1d1d;
        }

        .category-inquiry {
            background: #e0e7ff;
            color: #3730a3;
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

        .table-row-updated {
            background: #f0f9ff !important;
            transition: background 0.5s ease;
        }
    </style>

    <script>
        // Global variables
        let currentFilters = {
            status: 'All Status',
            priority: 'All Priority',
            category: 'All Categories',
            date_range: 'All Time',
            search: ''
        };
        let lastUpdateTime = '<?php echo date('Y-m-d H:i:s'); ?>';
        let realtimeInterval = null;
        let currentPage = 1;
        const itemsPerPage = 50;

        // Initialize real-time updates
        function startRealtimeUpdates() {
            realtimeInterval = setInterval(checkForUpdates, 5000); // Check every 5 seconds
            document.getElementById('realtimeIndicator').style.display = 'flex';
        }

        // Stop real-time updates
        function stopRealtimeUpdates() {
            if (realtimeInterval) {
                clearInterval(realtimeInterval);
                realtimeInterval = null;
            }
            document.getElementById('realtimeIndicator').style.display = 'none';
        }

        // Check for ticket updates
        async function checkForUpdates() {
            try {
                const response = await fetch(`?action=get_ticket_updates&last_update=${encodeURIComponent(lastUpdateTime)}`);
                const data = await response.json();
                
                if (data.updated) {
                    // Update last update time
                    lastUpdateTime = '<?php echo date('Y-m-d H:i:s'); ?>';
                    
                    // Update stats
                    if (data.stats) {
                        updateStats(data.stats);
                    }
                    
                    // Show notification for new/updated tickets
                    if (data.tickets.length > 0) {
                        showUpdateNotification(data.tickets.length);
                    }
                }
            } catch (error) {
                console.error('Error checking for updates:', error);
            }
        }

        // Update statistics display
        function updateStats(stats) {
            const statCards = document.querySelectorAll('.stat-card');
            if (statCards.length >= 6) {
                statCards[0].querySelector('.stat-value').textContent = numberFormat(stats.totalTickets);
                statCards[1].querySelector('.stat-value').textContent = numberFormat(stats.openTickets);
                statCards[2].querySelector('.stat-value').textContent = numberFormat(stats.inProgress);
                statCards[3].querySelector('.stat-value').textContent = numberFormat(stats.resolved);
                statCards[4].querySelector('.stat-value').textContent = stats.avgResponseHours ? stats.avgResponseHours + ' hrs' : 'N/A';
                statCards[5].querySelector('.stat-value').textContent = stats.satisfactionRate ? stats.satisfactionRate + '%' : 'N/A';
            }
        }

        // Show update notification
        function showUpdateNotification(count) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'update-notification';
            notification.innerHTML = `
                <div style="padding: 12px; background: #10b981; color: white; border-radius: 6px; margin-bottom: 10px; font-size: 14px;">
                    🔄 ${count} ticket${count > 1 ? 's' : ''} updated. <a href="javascript:void(0)" onclick="refreshTickets()" style="color: white; text-decoration: underline;">Refresh</a>
                </div>
            `;
            
            // Add to page
            const container = document.querySelector('.container');
            container.insertBefore(notification, container.firstChild);
            
            // Remove after 5 seconds
            setTimeout(() => {
                notification.remove();
            }, 5000);
        }

        // Apply filters
        async function applyFilters() {
            const filters = {
                status: document.getElementById('statusFilter').value,
                priority: document.getElementById('priorityFilter').value,
                category: document.getElementById('categoryFilter').value,
                date_range: document.getElementById('dateFilter').value,
                search: document.getElementById('globalSearch').value
            };
            
            currentFilters = filters;
            
            try {
                const params = new URLSearchParams({
                    action: 'filter_tickets',
                    ...filters
                });
                
                const response = await fetch(`?${params}`);
                const data = await response.json();
                
                if (!data.error) {
                    updateTicketsTable(data.tickets, data.total_count);
                } else {
                    console.error('Filter error:', data.error);
                }
            } catch (error) {
                console.error('Error applying filters:', error);
            }
        }

        // Update tickets table
        function updateTicketsTable(tickets, totalCount) {
            const tbody = document.getElementById('ticketsTableBody');
            const ticketCount = document.getElementById('ticketCount');
            const totalTicketsCount = document.getElementById('totalTicketsCount');
            const showingRange = document.getElementById('showingRange');
            
            // Update counts
            ticketCount.textContent = totalCount;
            totalTicketsCount.textContent = totalCount;
            showingRange.innerHTML = `<strong>1-${Math.min(itemsPerPage, tickets.length)}</strong>`;
            
            // Clear existing rows
            tbody.innerHTML = '';
            
            // Add new rows
            tickets.forEach(ticket => {
                tbody.innerHTML += `
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><span class="contact-id">${ticket.id}</span></td>
                        <td>
                            <div class="contact-name-cell">
                                <div class="contact-avatar">${ticket.customer_initials}</div>
                                <div class="contact-name-info">
                                    <div class="contact-name-primary">${ticket.customer}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="ticket-subject">
                                <div class="ticket-subject-title">${ticket.subject}</div>
                                <div class="ticket-subject-desc">${ticket.description}</div>
                                ${ticket.sale_ref !== '-' ? `<div class="ticket-sale-ref">🔗 ${ticket.sale_ref}</div>` : ''}
                            </div>
                        </td>
                        <td><span class="category-badge category-${ticket.category.toLowerCase().replace(' ', '-')}">${ticket.category}</span></td>
                        <td><span class="priority-badge priority-${ticket.priority.toLowerCase()}">${ticket.priority}</span></td>
                        <td><span class="status-badge ticket-status-${ticket.status.toLowerCase().replace(' ', '-')}">${ticket.status.toUpperCase()}</span></td>
                        <td>
                            <div class="contact-name-cell">
                                <div class="contact-avatar" style="width: 32px; height: 32px; font-size: 12px;">${ticket.assigned_initials}</div>
                                <span>${ticket.assigned}</span>
                            </div>
                        </td>
                        <td>${ticket.created}</td>
                        <td>${ticket.updated}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-btn" onclick="viewTicket('${ticket.id}')">View</button>
                                <button class="action-btn" onclick="updateTicket('${ticket.id}')">Update</button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('statusFilter').value = 'All Status';
            document.getElementById('priorityFilter').value = 'All Priority';
            document.getElementById('categoryFilter').value = 'All Categories';
            document.getElementById('dateFilter').value = 'All Time';
            document.getElementById('globalSearch').value = '';
            
            applyFilters();
        }

        // Refresh tickets
        function refreshTickets() {
            applyFilters();
            lastUpdateTime = '<?php echo date('Y-m-d H:i:s'); ?>';
        }

        // Number formatting helper
        function numberFormat(number) {
            return new Intl.NumberFormat().format(number);
        }

        // Modal Functions
        function openTicketModal() {
            document.getElementById('ticketModal').classList.add('active');
        }

        function closeTicketModal() {
            document.getElementById('ticketModal').classList.remove('active');
            document.getElementById('ticketForm').reset();
        }

        function saveTicket() {
            const form = document.getElementById('ticketForm');
            if (form.checkValidity()) {
                alert('Support ticket created successfully!');
                closeTicketModal();
                refreshTickets();
                // Here you would send data to PHP backend
                // PHP will validate user role (can_create_support_tickets)
                // Insert into SupportTickets table
            } else {
                form.reportValidity();
            }
        }

        // Ticket actions
        function viewTicket(ticketId) {
            alert('View ticket: ' + ticketId);
        }

        function updateTicket(ticketId) {
            alert('Update ticket: ' + ticketId);
        }

        // Pagination
        function changePage(direction) {
            // Implementation for pagination
            console.log('Changing page:', direction);
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to filters
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
            document.getElementById('priorityFilter').addEventListener('change', applyFilters);
            document.getElementById('categoryFilter').addEventListener('change', applyFilters);
            document.getElementById('dateFilter').addEventListener('change', applyFilters);
            document.getElementById('globalSearch').addEventListener('input', applyFilters);
            
            // Start real-time updates
            startRealtimeUpdates();
            
            // Close modal on overlay click
            document.getElementById('ticketModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeTicketModal();
                }
            });

            // Select all checkbox
            const selectAll = document.getElementById('selectAll');
            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    const rowCheckboxes = document.querySelectorAll('.table tbody input[type="checkbox"]');
                    rowCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }
        });
    </script>
</body>
</html>

<?php
// Helper function to render ticket row
function renderTicketRow($ticket) {
    $statusClass = match($ticket['status']) {
        'Open' => 'ticket-status-open',
        'In Progress' => 'ticket-status-progress',
        'Resolved' => 'ticket-status-resolved',
        'Closed' => 'ticket-status-closed',
        default => 'ticket-status-open'
    };

    $priorityClass = match($ticket['priority']) {
        'Critical' => 'ticket-priority-critical',
        'High' => 'priority-high',
        'Medium' => 'priority-medium',
        'Low' => 'priority-low',
        default => 'priority-medium'
    };

    $categoryClass = match($ticket['category']) {
        'Product Issue' => 'category-product',
        'Order Issue' => 'category-order',
        'Refund Request' => 'category-refund',
        'Complaint' => 'category-complaint',
        'Inquiry' => 'category-inquiry',
        default => 'category-inquiry'
    };
    
    ob_start();
    ?>
    <tr>
        <td><input type="checkbox"></td>
        <td><span class="contact-id"><?php echo $ticket['id']; ?></span></td>
        <td>
            <div class="contact-name-cell">
                <div class="contact-avatar"><?php echo $ticket['customer_initials']; ?></div>
                <div class="contact-name-info">
                    <div class="contact-name-primary"><?php echo $ticket['customer']; ?></div>
                </div>
            </div>
        </td>
        <td>
            <div class="ticket-subject">
                <div class="ticket-subject-title"><?php echo $ticket['subject']; ?></div>
                <div class="ticket-subject-desc"><?php echo $ticket['description']; ?></div>
                <?php if ($ticket['sale_ref'] !== '-'): ?>
                    <div class="ticket-sale-ref">🔗 <?php echo $ticket['sale_ref']; ?></div>
                <?php endif; ?>
            </div>
        </td>
        <td><span class="category-badge <?php echo $categoryClass; ?>"><?php echo $ticket['category']; ?></span></td>
        <td><span class="priority-badge <?php echo $priorityClass; ?>"><?php echo $ticket['priority']; ?></span></td>
        <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo strtoupper($ticket['status']); ?></span></td>
        <td>
            <div class="contact-name-cell">
                <div class="contact-avatar" style="width: 32px; height: 32px; font-size: 12px;"><?php echo $ticket['assigned_initials']; ?></div>
                <span><?php echo $ticket['assigned']; ?></span>
            </div>
        </td>
        <td><?php echo $ticket['created']; ?></td>
        <td><?php echo $ticket['updated']; ?></td>
        <td>
            <div class="action-buttons">
                <button class="action-btn">View</button>
                <button class="action-btn">Update</button>
            </div>
        </td>
    </tr>
    <?php
    return ob_get_clean();
}
?>