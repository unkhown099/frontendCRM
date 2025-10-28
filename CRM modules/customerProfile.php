<?php
session_start();
// Backend: prepare DB-driven data for the Customer Profiles page
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();

$totalCustomers = 0;
$vipCustomers = 0;
$totalLoyaltyPoints = 0;
$newThisMonth = 0;
$customers = [];

try {
    $monthStart = date('Y-m-01');
    $monthEnd = date('Y-m-t');

    // total customers
    $stmt = $pdo->query("SELECT COUNT(*) FROM customers");
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

    // total loyalty points: prefer loyalty_accounts if present
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'loyalty_accounts'");
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $stmt2 = $pdo->query("SELECT IFNULL(SUM(points_balance),0) FROM loyalty_accounts");
        $totalLoyaltyPoints = (int)$stmt2->fetchColumn();
    } else {
        $stmt2 = $pdo->query("SELECT IFNULL(SUM(LoyaltyPoints),0) FROM customers");
        $totalLoyaltyPoints = (int)$stmt2->fetchColumn();
    }

    // new customers this month
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM customers WHERE CreatedAt BETWEEN ? AND ?");
    $stmt->execute([$monthStart . ' 00:00:00', $monthEnd . ' 23:59:59']);
    $newThisMonth = (int)$stmt->fetchColumn();

    // Fetch customers
    $stmt = $pdo->prepare("SELECT CustomerID, FirstName, LastName, Email, Phone, LoyaltyPoints, Status, CreatedAt FROM customers ORDER BY FirstName LIMIT ? OFFSET ?");
    $stmt->execute([(int)$perPage, (int)$offset]);
    $custRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalPages = $perPage > 0 ? (int)ceil($totalCustomers / $perPage) : 1;

    foreach ($custRows as $r) {
        $cid = $r['CustomerID'];
        $name = trim((($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? '')));
        if ($name === '') $name = 'Customer';
        $initials = '';
        foreach (preg_split('/\s+/', $name) as $p) { $initials .= strtoupper(substr($p,0,1)); }

        // points
        $points = 0;
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'loyalty_accounts'");
        $stmtCheck->execute();
        if ($stmtCheck->fetchColumn() > 0) {
            $s2 = $pdo->prepare("SELECT IFNULL(points_balance,0) FROM loyalty_accounts WHERE CustomerID = ? LIMIT 1");
            $s2->execute([$cid]);
            $points = (int)$s2->fetchColumn();
        } else {
            // use LoyaltyPoints column from customers table per ERPSCHEMA.sql
            $s2 = $pdo->prepare("SELECT IFNULL(LoyaltyPoints,0) FROM customers WHERE CustomerID = ? LIMIT 1");
            $s2->execute([$cid]);
            $points = (int)$s2->fetchColumn();
        }

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
            'id' => '#CUST-' . $cid,
            'name' => $name,
            'initials' => $initials,
            'email' => $r['Email'] ?? '',
            'phone' => $r['Phone'] ?? '',
            // no CustomerType column in schema; infer VIP by points otherwise Retail
            'type' => ($points >= $vipThreshold ? 'VIP' : 'Retail'),
            'points' => $points,
            'tier' => $tier,
            'total_purchases' => '₱' . number_format($totalPurchases,2),
            'last_purchase' => $lastPurchase,
            'status' => 'Active'
        ];
    }

} catch (Exception $e) {
    // keep defaults if DB queries fail
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
                <button class="btn btn-secondary">
                    <span>📥</span>
                    <span>Import Customers</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Export</span>
                </button>
                <button class="btn btn-primary" onclick="openModal()">
                    <span>+</span>
                    <span>Add Customer</span>
                </button>
            </div>
        </div>

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
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset Filters</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Customer Type</label>
                    <select class="filter-select">
                        <option>All Customers</option>
                        <option>Retail</option>
                        <option>Wholesale</option>
                        <option>VIP</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Loyalty Tier</label>
                    <select class="filter-select">
                        <option>All Tiers</option>
                        <option>Bronze (0-99 pts)</option>
                        <option>Silver (100-499 pts)</option>
                        <option>Gold (500-999 pts)</option>
                        <option>Platinum (1000+ pts)</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                        <option>Suspended</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Registration Date</label>
                    <select class="filter-select">
                        <option>All Time</option>
                        <option>This Month</option>
                        <option>Last 3 Months</option>
                        <option>Last 6 Months</option>
                        <option>This Year</option>
                    </select>
                </div>
            </div>
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
                            <th><input type="checkbox"></th>
                            <th>Customer ID</th>
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
                            echo '<td><input type="checkbox"></td>';
                            echo '<td><span class="contact-id">' . $customer['id'] . '</span></td>';
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
                            echo '<button class="action-btn" onclick="viewCustomer(\'' . $customer['id'] . '\')">View</button>';
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
                <?php
                    $start = $offset + 1;
                    $end = min($offset + $perPage, $totalCustomers);
                ?>
                <div class="showing-text">
                    Showing <strong><?php echo $start ?>-<?php echo $end ?></strong> of <strong><?php echo number_format($totalCustomers) ?></strong> customers
                </div>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo ($page - 1) ?>" class="btn">‹</a>
                    <?php endif; ?>

                    <?php
                    $maxPagesToShow = 7;
                    $startPage = max(1, min($page - floor($maxPagesToShow/2), max(1, $totalPages - $maxPagesToShow + 1)));
                    $endPage = min($startPage + $maxPagesToShow - 1, $totalPages);
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?page=<?php echo $i ?>" class="btn <?php echo ($i == $page ? 'active' : '') ?>"><?php echo $i ?></a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?php echo ($page + 1) ?>" class="btn">›</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div class="modal-overlay" id="customerModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Customer</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="customerForm">
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
                            <label class="form-label">City</label>
                            <input type="text" class="form-input" name="city" placeholder="City">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Province</label>
                            <input type="text" class="form-input" name="province" placeholder="Province">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-input" name="postal_code" placeholder="0000">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-input" name="country" placeholder="Philippines" value="Philippines">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Customer Type *</label>
                            <select class="filter-select" name="customer_type" required>
                                <option value="">Select type</option>
                                <option value="Retail">Retail</option>
                                <option value="Wholesale">Wholesale</option>
                                <option value="VIP">VIP</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Initial Loyalty Points</label>
                            <input type="number" class="form-input" name="loyalty_points" placeholder="0" value="0">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" name="notes" placeholder="Add any additional notes about this customer..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveCustomer()">Save Customer</button>
            </div>
        </div>
    </div>

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
        }

        .type-wholesale {
            background: #ddd6fe;
            color: #5b21b6;
        }

        .type-retail {
            background: #dbeafe;
            color: #1e40af;
        }
    </style>

    <script>
        // Modal Functions
        function openModal() {
            document.getElementById('customerModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('customerModal').classList.remove('active');
            document.getElementById('customerForm').reset();
        }

        function saveCustomer() {
            const form = document.getElementById('customerForm');
            if (form.checkValidity()) {
                // Here you would normally send the data to PHP backend
                alert('Customer saved successfully!');
                closeModal();
                // Refresh the table or add the new row
            } else {
                form.reportValidity();
            }
        }

        function viewCustomer(customerId) {
            alert('Viewing customer: ' + customerId);
            // Here you would normally redirect to customer detail page
        }

        // Close modal on overlay click
        document.getElementById('customerModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
            // Implement search logic here
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

        // Pagination (anchor links)
        const pagination = document.querySelector('.pagination');
        if (pagination) {
            pagination.addEventListener('click', function(e) {
                const a = e.target.closest('a');
                if (!a) return;
                // Let the browser navigate — optionally update UI immediately
                pagination.querySelectorAll('a').forEach(el => el.classList.remove('active'));
                a.classList.add('active');
            });
        }

        // Filter change listeners
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                console.log('Filter changed:', this.value);
                // Implement filter logic here
            });
        });

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this customer?')) {
                        this.closest('tr').remove();
                        alert('Customer deleted successfully!');
                    }
                } else if (action === 'Edit') {
                    alert('Edit customer functionality');
                }
            });
        });

        // Table row clicks
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
                    const customerId = this.querySelector('.contact-id').textContent;
                    viewCustomer(customerId);
                }
            });
        });
    </script>
</body>
</html>