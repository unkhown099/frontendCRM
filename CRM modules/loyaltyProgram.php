<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loyalty Program - CRM System</title>
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
                <li><a href="#">Dashboard</a></li>
                <li><a href="./customerProfile.php">Customer Profiles</a></li>
                <li><a href="./loyaltyProgram.php" class="active">Loyalty Program</a></li>
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
                <a href="./crmProfile.php"><div class="user-avatar">SM</div></a>
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
                    <span>Loyalty Program</span>
                </div>
                <h1 class="page-title">Loyalty Program</h1>
                <p class="page-subtitle">Manage customer loyalty points and rewards program</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>⚙️</span>
                    <span>Program Settings</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Export Report</span>
                </button>
                <button class="btn btn-primary" onclick="openAdjustModal()">
                    <span>✏️</span>
                    <span>Adjust Points</span>
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '🎁',
                    'value' => '485,670',
                    'label' => 'Total Points Issued',
                    'sublabel' => 'All time',
                    'trend' => '+18.5%',
                    'trend_dir' => 'up',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '💰',
                    'value' => '142,350',
                    'label' => 'Points Redeemed',
                    'sublabel' => 'This year',
                    'trend' => '+12.3%',
                    'trend_dir' => 'up',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '🏆',
                    'value' => '343,320',
                    'label' => 'Active Points',
                    'sublabel' => 'Available for redemption',
                    'trend' => '+15.8%',
                    'trend_dir' => 'up',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '⭐',
                    'value' => '₱10/pt',
                    'label' => 'Points Rate',
                    'sublabel' => '1 point per ₱10 spent',
                    'trend' => 'Standard',
                    'trend_dir' => 'up',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '📈',
                    'value' => '28,450',
                    'label' => 'Points This Month',
                    'sublabel' => 'From ₱284,500 sales',
                    'trend' => '+22.1%',
                    'trend_dir' => 'up',
                    'color' => '#e9d5ff'
                ]
            ];

            foreach ($stats as $stat) {
                echo '<div class="stat-card">';
                echo '<div class="stat-header">';
                echo '<div class="stat-icon" style="background: ' . $stat['color'] . ';">' . $stat['icon'] . '</div>';
                echo '<div class="stat-trend ' . $stat['trend_dir'] . '">';
                echo '<span>' . ($stat['trend_dir'] === 'up' && $stat['trend'] !== 'Standard' ? '↑' : '') . '</span>';
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

        <!-- Loyalty Tiers Section -->
        <div class="content-card" style="margin-bottom: 24px;">
            <div class="card-header">
                <h2 class="section-title">Loyalty Tier Structure</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Edit Tiers">✏️</button>
                </div>
            </div>
            <div style="padding: 24px;">
                <div class="tier-structure">
                    <?php
                    $tiers = [
                        [
                            'name' => 'Bronze',
                            'icon' => '🥉',
                            'range' => '0 - 99 points',
                            'benefits' => ['1 point per ₱10', 'Basic rewards', 'Birthday discount'],
                            'members' => 1456,
                            'color' => '#fed7aa'
                        ],
                        [
                            'name' => 'Silver',
                            'icon' => '🥈',
                            'range' => '100 - 499 points',
                            'benefits' => ['1.2 points per ₱10', 'Priority support', 'Exclusive offers', 'Free shipping'],
                            'members' => 892,
                            'color' => '#e5e7eb'
                        ],
                        [
                            'name' => 'Gold',
                            'icon' => '🥇',
                            'range' => '500 - 999 points',
                            'benefits' => ['1.5 points per ₱10', 'VIP support', 'Early access', 'Special events', 'Gift wrapping'],
                            'members' => 356,
                            'color' => '#fde68a'
                        ],
                        [
                            'name' => 'Platinum',
                            'icon' => '💎',
                            'range' => '1000+ points',
                            'benefits' => ['2 points per ₱10', 'Dedicated manager', 'Premium gifts', 'Exclusive launches', 'Personal shopping'],
                            'members' => 143,
                            'color' => '#c7d2fe'
                        ]
                    ];

                    echo '<div class="tiers-grid">';
                    foreach ($tiers as $tier) {
                        echo '<div class="tier-card" style="border-top: 4px solid ' . $tier['color'] . ';">';
                        echo '<div class="tier-card-header">';
                        echo '<span class="tier-icon">' . $tier['icon'] . '</span>';
                        echo '<div>';
                        echo '<h3 class="tier-name">' . $tier['name'] . '</h3>';
                        echo '<p class="tier-range">' . $tier['range'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="tier-benefits">';
                        foreach ($tier['benefits'] as $benefit) {
                            echo '<div class="tier-benefit">✓ ' . $benefit . '</div>';
                        }
                        echo '</div>';
                        echo '<div class="tier-members">' . number_format($tier['members']) . ' members</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                    ?>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="filters-section">
            <div class="filters-header">
                <div class="filters-title">🔍 Filter Loyalty Data</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset Filters</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Loyalty Tier</label>
                    <select class="filter-select">
                        <option>All Tiers</option>
                        <option>Bronze</option>
                        <option>Silver</option>
                        <option>Gold</option>
                        <option>Platinum</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Activity Period</label>
                    <select class="filter-select">
                        <option>All Time</option>
                        <option>This Month</option>
                        <option>Last 3 Months</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Point Range</label>
                    <select class="filter-select">
                        <option>All Ranges</option>
                        <option>0-99 pts</option>
                        <option>100-499 pts</option>
                        <option>500-999 pts</option>
                        <option>1000+ pts</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Sort By</label>
                    <select class="filter-select">
                        <option>Highest Points</option>
                        <option>Lowest Points</option>
                        <option>Recent Activity</option>
                        <option>Name A-Z</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Recent Points Activity Table -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Recent Points Activity</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Filter">🔽</button>
                    <button class="icon-btn" title="Refresh">🔄</button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Customer</th>
                            <th>Action</th>
                            <th>Points Change</th>
                            <th>Previous Balance</th>
                            <th>New Balance</th>
                            <th>Tier</th>
                            <th>Sale Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $activities = [
                            [
                                'id' => '#TXN-001',
                                'customer' => 'John Santos',
                                'initials' => 'JS',
                                'action' => 'Earned',
                                'points_change' => 125,
                                'prev_balance' => 1125,
                                'new_balance' => 1250,
                                'tier' => 'Platinum',
                                'sale_amount' => '₱12,500',
                                'date' => '2 hours ago'
                            ],
                            [
                                'id' => '#TXN-002',
                                'customer' => 'Maria Garcia',
                                'initials' => 'MG',
                                'action' => 'Redeemed',
                                'points_change' => -50,
                                'prev_balance' => 730,
                                'new_balance' => 680,
                                'tier' => 'Gold',
                                'sale_amount' => '-',
                                'date' => '5 hours ago'
                            ],
                            [
                                'id' => '#TXN-003',
                                'customer' => 'Robert Chen',
                                'initials' => 'RC',
                                'action' => 'Earned',
                                'points_change' => 245,
                                'prev_balance' => 2205,
                                'new_balance' => 2450,
                                'tier' => 'Platinum',
                                'sale_amount' => '₱24,500',
                                'date' => '1 day ago'
                            ],
                            [
                                'id' => '#TXN-004',
                                'customer' => 'Ana Reyes',
                                'initials' => 'AR',
                                'action' => 'Earned',
                                'points_change' => 32,
                                'prev_balance' => 288,
                                'new_balance' => 320,
                                'tier' => 'Silver',
                                'sale_amount' => '₱3,200',
                                'date' => '1 day ago'
                            ],
                            [
                                'id' => '#TXN-005',
                                'customer' => 'David Lim',
                                'initials' => 'DL',
                                'action' => 'Adjusted',
                                'points_change' => 100,
                                'prev_balance' => 1480,
                                'new_balance' => 1580,
                                'tier' => 'Platinum',
                                'sale_amount' => '-',
                                'date' => '2 days ago'
                            ],
                            [
                                'id' => '#TXN-006',
                                'customer' => 'Sarah Tan',
                                'initials' => 'ST',
                                'action' => 'Earned',
                                'points_change' => 8,
                                'prev_balance' => 77,
                                'new_balance' => 85,
                                'tier' => 'Bronze',
                                'sale_amount' => '₱845',
                                'date' => '3 days ago'
                            ],
                            [
                                'id' => '#TXN-007',
                                'customer' => 'Michael Cruz',
                                'initials' => 'MC',
                                'action' => 'Earned',
                                'points_change' => 89,
                                'prev_balance' => 801,
                                'new_balance' => 890,
                                'tier' => 'Gold',
                                'sale_amount' => '₱8,960',
                                'date' => '3 days ago'
                            ],
                            [
                                'id' => '#TXN-008',
                                'customer' => 'Lisa Wong',
                                'initials' => 'LW',
                                'action' => 'Redeemed',
                                'points_change' => -25,
                                'prev_balance' => 70,
                                'new_balance' => 45,
                                'tier' => 'Bronze',
                                'sale_amount' => '-',
                                'date' => '5 days ago'
                            ]
                        ];

                        foreach ($activities as $activity) {
                            $tierClass = match($activity['tier']) {
                                'Platinum' => 'tier-platinum',
                                'Gold' => 'tier-gold',
                                'Silver' => 'tier-silver',
                                default => 'tier-bronze'
                            };

                            $actionClass = match($activity['action']) {
                                'Earned' => 'action-earned',
                                'Redeemed' => 'action-redeemed',
                                default => 'action-adjusted'
                            };

                            $pointsClass = $activity['points_change'] > 0 ? 'points-positive' : 'points-negative';
                            
                            echo '<tr>';
                            echo '<td><span class="contact-id">' . $activity['id'] . '</span></td>';
                            echo '<td>';
                            echo '<div class="contact-name-cell">';
                            echo '<div class="contact-avatar">' . $activity['initials'] . '</div>';
                            echo '<div class="contact-name-info">';
                            echo '<div class="contact-name-primary">' . $activity['customer'] . '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td><span class="action-badge ' . $actionClass . '">' . $activity['action'] . '</span></td>';
                            echo '<td><span class="' . $pointsClass . '">' . ($activity['points_change'] > 0 ? '+' : '') . number_format($activity['points_change']) . ' pts</span></td>';
                            echo '<td>' . number_format($activity['prev_balance']) . ' pts</td>';
                            echo '<td><strong>' . number_format($activity['new_balance']) . ' pts</strong></td>';
                            echo '<td><span class="tier-badge ' . $tierClass . '">' . $activity['tier'] . '</span></td>';
                            echo '<td>' . $activity['sale_amount'] . '</td>';
                            echo '<td>' . $activity['date'] . '</td>';
                            echo '<td>';
                            echo '<div class="action-buttons">';
                            echo '<button class="action-btn">View</button>';
                            echo '<button class="action-btn">Receipt</button>';
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
                    Showing <strong>1-8</strong> of <strong>1,245</strong> transactions
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

    <!-- Adjust Points Modal -->
    <div class="modal-overlay" id="adjustModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Adjust Loyalty Points</h3>
                <button class="close-btn" onclick="closeAdjustModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="adjustForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Select Customer *</label>
                            <select class="filter-select" name="customer_id" required>
                                <option value="">Choose customer</option>
                                <option value="1">John Santos - Current: 1,250 pts</option>
                                <option value="2">Maria Garcia - Current: 680 pts</option>
                                <option value="3">Robert Chen - Current: 2,450 pts</option>
                                <option value="4">Ana Reyes - Current: 320 pts</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Adjustment Type *</label>
                            <select class="filter-select" name="adjustment_type" required>
                                <option value="">Select type</option>
                                <option value="add">Add Points</option>
                                <option value="subtract">Subtract Points</option>
                                <option value="set">Set Points</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Points Amount *</label>
                            <input type="number" class="form-input" name="points" placeholder="0" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Reason *</label>
                            <select class="filter-select" name="reason" required>
                                <option value="">Select reason</option>
                                <option value="promotion">Promotional Bonus</option>
                                <option value="correction">Correction</option>
                                <option value="compensation">Customer Compensation</option>
                                <option value="birthday">Birthday Gift</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" name="notes" placeholder="Add notes about this adjustment..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeAdjustModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveAdjustment()">Apply Adjustment</button>
            </div>
        </div>
    </div>

    <style>
        /* Tier Structure Styling */
        .tiers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .tier-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .tier-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .tier-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .tier-icon {
            font-size: 32px;
        }

        .tier-name {
            font-size: 18px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 2px;
        }

        .tier-range {
            font-size: 13px;
            color: var(--gray-600);
        }

        .tier-benefits {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--gray-200);
        }

        .tier-benefit {
            font-size: 13px;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tier-members {
            font-size: 12px;
            font-weight: 600;
            color: var(--primary);
            padding-top: 12px;
            border-top: 1px solid var(--gray-200);
        }

        /* Action Badges */
        .action-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
        }

        .action-earned {
            background: #d1fae5;
            color: #065f46;
        }

        .action-redeemed {
            background: #fef3c7;
            color: #92400e;
        }

        .action-adjusted {
            background: #dbeafe;
            color: #1e40af;
        }

        /* Points Display */
        .points-positive {
            color: var(--success);
            font-weight: 700;
            font-size: 14px;
        }

        .points-negative {
            color: var(--danger);
            font-weight: 700;
            font-size: 14px;
        }

        /* Tier Badges */
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
    </style>

    <script>
        // Modal Functions
        function openAdjustModal() {
            document.getElementById('adjustModal').classList.add('active');
        }

        function closeAdjustModal() {
            document.getElementById('adjustModal').classList.remove('active');
            document.getElementById('adjustForm').reset();
        }

        function saveAdjustment() {
            const form = document.getElementById('adjustForm');
            if (form.checkValidity()) {
                alert('Points adjustment applied successfully!');
                closeAdjustModal();
                // Here you would send data to PHP backend
            } else {
                form.reportValidity();
            }
        }

        // Close modal on overlay click
        document.getElementById('adjustModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeAdjustModal();
            }
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
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
                
                if (action === 'View') {
                    alert('View transaction details');
                } else if (action === 'Receipt') {
                    alert('Generate receipt');
                }
            });
        });
    </script>
</body>
</html>