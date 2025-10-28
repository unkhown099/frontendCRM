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
            $stats = [
                [
                    'icon' => '👥',
                    'value' => '2,847',
                    'label' => 'Total Customers',
                    'sublabel' => 'Active accounts',
                    'trend' => '+12.5%',
                    'trend_dir' => 'up',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '⭐',
                    'value' => '892',
                    'label' => 'VIP Customers',
                    'sublabel' => 'Loyalty tier 3+',
                    'trend' => '+8.2%',
                    'trend_dir' => 'up',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '🎁',
                    'value' => '45,678',
                    'label' => 'Total Loyalty Points',
                    'sublabel' => 'Redeemable',
                    'trend' => '+15.3%',
                    'trend_dir' => 'up',
                    'color' => '#e9d5ff'
                ],
                [
                    'icon' => '🆕',
                    'value' => '156',
                    'label' => 'New This Month',
                    'sublabel' => 'vs 128 last month',
                    'trend' => '+21.9%',
                    'trend_dir' => 'up',
                    'color' => '#d1fae5'
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
                <h2 class="section-title">All Customers (2,847)</h2>
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
                        $customers = [
                            [
                                'id' => '#CUST-001',
                                'name' => 'John Santos',
                                'initials' => 'JS',
                                'email' => 'john.santos@email.com',
                                'phone' => '+63 917 123 4567',
                                'type' => 'VIP',
                                'points' => 1250,
                                'tier' => 'Platinum',
                                'total_purchases' => '₱125,450',
                                'last_purchase' => '2 days ago',
                                'status' => 'Active'
                            ],
                            [
                                'id' => '#CUST-002',
                                'name' => 'Maria Garcia',
                                'initials' => 'MG',
                                'email' => 'maria.garcia@email.com',
                                'phone' => '+63 918 234 5678',
                                'type' => 'Retail',
                                'points' => 680,
                                'tier' => 'Gold',
                                'total_purchases' => '₱68,200',
                                'last_purchase' => '5 days ago',
                                'status' => 'Active'
                            ],
                            [
                                'id' => '#CUST-003',
                                'name' => 'Robert Chen',
                                'initials' => 'RC',
                                'email' => 'robert.chen@email.com',
                                'phone' => '+63 919 345 6789',
                                'type' => 'Wholesale',
                                'points' => 2450,
                                'tier' => 'Platinum',
                                'total_purchases' => '₱245,800',
                                'last_purchase' => '1 day ago',
                                'status' => 'Active'
                            ],
                            [
                                'id' => '#CUST-004',
                                'name' => 'Ana Reyes',
                                'initials' => 'AR',
                                'email' => 'ana.reyes@email.com',
                                'phone' => '+63 920 456 7890',
                                'type' => 'Retail',
                                'points' => 320,
                                'tier' => 'Silver',
                                'total_purchases' => '₱32,150',
                                'last_purchase' => '1 week ago',
                                'status' => 'Active'
                            ],
                            [
                                'id' => '#CUST-005',
                                'name' => 'David Lim',
                                'initials' => 'DL',
                                'email' => 'david.lim@email.com',
                                'phone' => '+63 921 567 8901',
                                'type' => 'VIP',
                                'points' => 1580,
                                'tier' => 'Platinum',
                                'total_purchases' => '₱158,900',
                                'last_purchase' => '3 days ago',
                                'status' => 'Active'
                            ],
                            [
                                'id' => '#CUST-006',
                                'name' => 'Sarah Tan',
                                'initials' => 'ST',
                                'email' => 'sarah.tan@email.com',
                                'phone' => '+63 922 678 9012',
                                'type' => 'Retail',
                                'points' => 85,
                                'tier' => 'Bronze',
                                'total_purchases' => '₱8,450',
                                'last_purchase' => '2 weeks ago',
                                'status' => 'Active'
                            ],
                            [
                                'id' => '#CUST-007',
                                'name' => 'Michael Cruz',
                                'initials' => 'MC',
                                'email' => 'michael.cruz@email.com',
                                'phone' => '+63 923 789 0123',
                                'type' => 'Wholesale',
                                'points' => 890,
                                'tier' => 'Gold',
                                'total_purchases' => '₱89,600',
                                'last_purchase' => '4 days ago',
                                'status' => 'Active'
                            ],
                            [
                                'id' => '#CUST-008',
                                'name' => 'Lisa Wong',
                                'initials' => 'LW',
                                'email' => 'lisa.wong@email.com',
                                'phone' => '+63 924 890 1234',
                                'type' => 'Retail',
                                'points' => 45,
                                'tier' => 'Bronze',
                                'total_purchases' => '₱4,280',
                                'last_purchase' => '1 month ago',
                                'status' => 'Inactive'
                            ]
                        ];

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
                <div class="showing-text">
                    Showing <strong>1-8</strong> of <strong>2,847</strong> customers
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