<?php
// Start session if not already started
session_start();

// Include database configuration
require_once('../config/database.php');

// Initialize database connection
try {
    $db = Database::getInstance()->getConnection();
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Get user information
$userId = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM Dashboard - Enterprise Edition</title>
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
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="./leadsManagement.php">Leads</a></li>
                <li><a href="./contactManagement.php">Contacts</a></li>
                <li><a href="./dealsManagement.php">Deals</a></li>
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search anything...">
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
                    <span>Dashboard</span>
                </div>
                <h1 class="page-title">Crm   Dashboard</h1>
                <p class="page-subtitle">Track and manage your sales pipeline in real-time</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Export</span>
                </button>
                <button class="btn btn-primary">
                    <span>+</span>
                    <span>Add New Lead</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php

            class DashboardStats {
                private $db;

                public function __construct() {
                    global $db;
                    $this->db = $db;
                }

                public function getTotalLeads() {
                    $sql = "SELECT 
                            COUNT(*) as total,
                            COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 END) as last_month
                           FROM leads";
                    $result = $this->db->query($sql)->fetch();
                    $lastMonthPercentage = $result['last_month'] > 0 
                        ? round(($result['last_month'] / $result['total']) * 100, 1) 
                        : 0;
                    return [
                        'value' => number_format($result['total']),
                        'trend' => ($lastMonthPercentage > 0 ? '+' : '') . $lastMonthPercentage . '%'
                    ];
                }

                public function getQualifiedLeads() {
                    $sql = "SELECT 
                            COUNT(*) as total,
                            COUNT(CASE WHEN status_updated_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 END) as last_month
                           FROM leads 
                           WHERE status = 'Qualified'";
                    $result = $this->db->query($sql)->fetch();
                    $lastMonthPercentage = $result['last_month'] > 0 
                        ? round(($result['last_month'] / $result['total']) * 100, 1) 
                        : 0;
                    return [
                        'value' => number_format($result['total']),
                        'trend' => ($lastMonthPercentage > 0 ? '+' : '') . $lastMonthPercentage . '%'
                    ];
                }

                public function getPipelineValue() {
                    $sql = "SELECT 
                            SUM(potential_value) as total,
                            SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN potential_value ELSE 0 END) as last_month
                           FROM deals 
                           WHERE status != 'Lost' AND status != 'Closed'";
                    $result = $this->db->query($sql)->fetch();
                    $lastMonthPercentage = $result['last_month'] > 0 
                        ? round(($result['last_month'] / $result['total']) * 100, 1) 
                        : 0;
                    return [
                        'value' => '₱' . number_format($result['total'], 2),
                        'trend' => ($lastMonthPercentage > 0 ? '+' : '') . $lastMonthPercentage . '%'
                    ];
                }

                public function getConversionRate() {
                    $sql = "SELECT 
                            (COUNT(CASE WHEN status = 'Won' THEN 1 END) * 100.0 / COUNT(*)) as rate,
                            (COUNT(CASE WHEN status = 'Won' AND status_updated_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH) THEN 1 END) * 100.0 / 
                             COUNT(CASE WHEN status_updated_at >= DATE_SUB(NOW(), INTERVAL 3 MONTH) THEN 1 END)) as last_quarter
                           FROM deals 
                           WHERE status IN ('Won', 'Lost')";
                    $result = $this->db->query($sql)->fetch();
                    $trend = $result['last_quarter'] - $result['rate'];
                    return [
                        'value' => round($result['rate'], 1) . '%',
                        'trend' => ($trend > 0 ? '+' : '') . round($trend, 1) . '%'
                    ];
                }

            } // End of DashboardStats class

                $dashboard = new DashboardStats();
                $totalLeads = $dashboard->getTotalLeads();
                $qualifiedLeads = $dashboard->getQualifiedLeads();
                $pipelineValue = $dashboard->getPipelineValue();
                $conversionRate = $dashboard->getConversionRate();

                $stats = [
                    [
                        'icon' => '👥',
                        'value' => $totalLeads['value'],
                        'label' => 'Total Leads',
                        'sublabel' => 'Active in pipeline',
                        'trend' => $totalLeads['trend'],
                        'trend_dir' => strpos($totalLeads['trend'], '+') !== false ? 'up' : 'down',
                        'color' => '#dbeafe'
                    ],
                    [
                        'icon' => '✅',
                        'value' => $qualifiedLeads['value'],
                        'label' => 'Qualified Leads',
                        'sublabel' => 'Ready for conversion',
                        'trend' => $qualifiedLeads['trend'],
                        'trend_dir' => strpos($qualifiedLeads['trend'], '+') !== false ? 'up' : 'down',
                        'color' => '#d1fae5'
                    ],
                    [
                        'icon' => '💰',
                        'value' => $pipelineValue['value'],
                        'label' => 'Pipeline Value',
                        'sublabel' => 'Potential revenue',
                        'trend' => $pipelineValue['trend'],
                        'trend_dir' => strpos($pipelineValue['trend'], '+') !== false ? 'up' : 'down',
                        'color' => '#fef3c7'
                    ],
                    [
                        'icon' => '📈',
                        'value' => $conversionRate['value'],
                        'label' => 'Conversion Rate',
                        'sublabel' => 'This quarter',
                        'trend' => $conversionRate['trend'],
                        'trend_dir' => strpos($conversionRate['trend'], '+') !== false ? 'up' : 'down',
                        'color' => '#ddd6fe'
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
                echo '<div class="stat-body">';
                echo '<div class="stat-value">' . $stat['value'] . '</div>';
                echo '<div class="stat-label">' . $stat['label'] . '</div>';
                echo '<div class="stat-sublabel">' . $stat['sublabel'] . '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <div class="tabs-wrapper">
            <div class="tabs">
                <button class="tab active">Recent Leads</button>
                <button class="tab">Active Deals</button>
                <button class="tab">Tasks & Follow-ups</button>
                <button class="tab">Performance</button>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Recent Leads Overview</h2>
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
                            <th>Lead ID</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th>Deal Value</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        function getRecentLeads($limit = 5, $offset = 0) {
                            global $db;
                            $sql = "SELECT 
                                    l.LeadID,
                                    c.FirstName,
                                    c.LastName,
                                    c.Email,
                                    c.CompanyName,
                                    l.PotentialValue,
                                    l.Priority,
                                    l.Status
                                   FROM leads l
                                   JOIN contacts c ON l.ContactID = c.ContactID
                                   ORDER BY l.CreatedAt DESC
                                   LIMIT ? OFFSET ?";
                            
                            $stmt = $db->prepare($sql);
                            $stmt->execute([$limit, $offset]);
                            return $stmt->fetchAll();
                        }

                        function getTotalLeadsCount() {
                            global $db;
                            $sql = "SELECT COUNT(*) as total FROM leads";
                            return $db->query($sql)->fetch()['total'];
                        }

                        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                        $perPage = 5;
                        $offset = ($page - 1) * $perPage;
                        
                        try {
                            $recentLeads = getRecentLeads($perPage, $offset);
                            $totalLeads = getTotalLeadsCount();
                            $totalPages = ceil($totalLeads / $perPage);

                            foreach ($recentLeads as $lead) {
                                // Get initials from name
                                $names = array_filter(explode(' ', $lead['FirstName'] . ' ' . $lead['LastName']));
                                $initials = '';
                                foreach ($names as $n) {
                                    $initials .= strtoupper(substr($n, 0, 1));
                                }
                                
                                // Format lead data
                                $lead = [
                                    'id' => '#LEAD-' . str_pad($lead['LeadID'], 3, '0', STR_PAD_LEFT),
                                    'name' => $lead['FirstName'] . ' ' . $lead['LastName'],
                                    'initials' => $initials,
                                    'email' => $lead['Email'],
                                    'company' => $lead['CompanyName'],
                                    'company_icon' => '🏢',
                                    'value' => '₱' . number_format($lead['PotentialValue'], 2),
                                    'priority' => $lead['Priority'],
                                    'status' => $lead['Status']
                                ];
                            $statusClass = 'status-' . strtolower($lead['status']);
                            $priorityClass = 'priority-' . strtolower($lead['priority']);
                            
                            echo '<tr>';
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
                            echo '<td><span class="value-cell">' . $lead['value'] . '</span></td>';
                            echo '<td><span class="priority-badge ' . $priorityClass . '">' . $lead['priority'] . '</span></td>';
                            echo '<td><span class="status-badge ' . $statusClass . '">' . strtoupper($lead['status']) . '</span></td>';
                            echo '<td>';
                            echo '<div class="action-buttons">';
                            echo '<button class="view-btn">View</button>';
                            echo '<button class="edit-btn">Edit</button>';
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
                    $end = min($offset + $perPage, $totalLeads);
                ?>
                <div class="showing-text">
                    Showing <strong><?php echo $start ?>-<?php echo $end ?></strong> of <strong><?php echo number_format($totalLeads) ?></strong> leads
                </div>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo ($page - 1) ?>" class="btn">‹</a>
                    <?php endif; ?>
                    
                    <?php
                    $maxPages = 5;
                    $startPage = max(1, min($page - floor($maxPages/2), $totalPages - $maxPages + 1));
                    $endPage = min($startPage + $maxPages - 1, $totalPages);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?page=<?php echo $i ?>" class="btn <?php echo ($i == $page ? 'active' : '') ?>"><?php echo $i ?></a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo ($page + 1) ?>" class="btn">›</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php
                } catch (Exception $e) {
                    echo '<div class="error-message">An error occurred while loading the dashboard data. Please try again later.</div>';
                }
            ?>
        </div>
    </div>
    <script>
        // Tab switching functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Table row interactions
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button')) {
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

        // Notification button
        document.querySelector('.notification-btn').addEventListener('click', function() {
            alert('5 new notifications');
        });
    </script>
</body>
</html>