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
                    <input type="text" class="search-box" placeholder="Search tickets...">
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
        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '🎫',
                    'value' => '342',
                    'label' => 'Total Tickets',
                    'sublabel' => 'All time',
                    'trend' => '+8.5%',
                    'trend_dir' => 'up',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '🔓',
                    'value' => '45',
                    'label' => 'Open Tickets',
                    'sublabel' => 'Awaiting response',
                    'trend' => '-12.3%',
                    'trend_dir' => 'down',
                    'color' => '#fee2e2'
                ],
                [
                    'icon' => '⏳',
                    'value' => '28',
                    'label' => 'In Progress',
                    'sublabel' => 'Being handled',
                    'trend' => '+5.2%',
                    'trend_dir' => 'up',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '✅',
                    'value' => '256',
                    'label' => 'Resolved',
                    'sublabel' => 'Closed tickets',
                    'trend' => '+15.8%',
                    'trend_dir' => 'up',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '⏱️',
                    'value' => '2.5hrs',
                    'label' => 'Avg Response Time',
                    'sublabel' => 'First response',
                    'trend' => '-18.2%',
                    'trend_dir' => 'up',
                    'color' => '#e9d5ff'
                ],
                [
                    'icon' => '📈',
                    'value' => '94%',
                    'label' => 'Satisfaction Rate',
                    'sublabel' => 'Customer feedback',
                    'trend' => '+3.1%',
                    'trend_dir' => 'up',
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
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset Filters</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select">
                        <option>All Status</option>
                        <option>Open</option>
                        <option>In Progress</option>
                        <option>Resolved</option>
                        <option>Closed</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Priority</label>
                    <select class="filter-select">
                        <option>All Priority</option>
                        <option>Critical</option>
                        <option>High</option>
                        <option>Medium</option>
                        <option>Low</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Category</label>
                    <select class="filter-select">
                        <option>All Categories</option>
                        <option>Product Issue</option>
                        <option>Order Issue</option>
                        <option>Refund Request</option>
                        <option>Complaint</option>
                        <option>Inquiry</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date Range</label>
                    <select class="filter-select">
                        <option>All Time</option>
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Support Tickets Table -->
        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">Support Tickets (342)</h2>
                <div class="card-actions">
                    <button class="icon-btn" title="Filter">🔽</button>
                    <button class="icon-btn" title="Sort">⇅</button>
                    <button class="icon-btn" title="Refresh">🔄</button>
                </div>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th><input type="checkbox"></th>
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
                    <tbody>
                        <?php
                        $tickets = [
                            [
                                'id' => '#TKT-001',
                                'customer' => 'John Santos',
                                'customer_initials' => 'JS',
                                'subject' => 'Defective shoes - Size 42',
                                'description' => 'Customer received shoes with manufacturing defect',
                                'category' => 'Product Issue',
                                'priority' => 'High',
                                'status' => 'Open',
                                'assigned' => 'Maria Lopez',
                                'assigned_initials' => 'ML',
                                'created' => '2 hours ago',
                                'updated' => '1 hour ago',
                                'sale_ref' => '#SALE-5678'
                            ],
                            [
                                'id' => '#TKT-002',
                                'customer' => 'Ana Reyes',
                                'customer_initials' => 'AR',
                                'subject' => 'Wrong size delivered',
                                'description' => 'Ordered size 38 but received size 40',
                                'category' => 'Order Issue',
                                'priority' => 'High',
                                'status' => 'In Progress',
                                'assigned' => 'Carlos Diaz',
                                'assigned_initials' => 'CD',
                                'created' => '5 hours ago',
                                'updated' => '30 mins ago',
                                'sale_ref' => '#SALE-5645'
                            ],
                            [
                                'id' => '#TKT-003',
                                'customer' => 'Robert Chen',
                                'customer_initials' => 'RC',
                                'subject' => 'Refund request for damaged item',
                                'description' => 'Item damaged during shipping',
                                'category' => 'Refund Request',
                                'priority' => 'Medium',
                                'status' => 'In Progress',
                                'assigned' => 'Maria Lopez',
                                'assigned_initials' => 'ML',
                                'created' => '1 day ago',
                                'updated' => '2 hours ago',
                                'sale_ref' => '#SALE-5612'
                            ],
                            [
                                'id' => '#TKT-004',
                                'customer' => 'Sarah Tan',
                                'customer_initials' => 'ST',
                                'subject' => 'Product inquiry - Availability',
                                'description' => 'Asking about stock for specific model',
                                'category' => 'Inquiry',
                                'priority' => 'Low',
                                'status' => 'Resolved',
                                'assigned' => 'Juan Cruz',
                                'assigned_initials' => 'JC',
                                'created' => '2 days ago',
                                'updated' => '1 day ago',
                                'sale_ref' => '-'
                            ],
                            [
                                'id' => '#TKT-005',
                                'customer' => 'Michael Cruz',
                                'customer_initials' => 'MC',
                                'subject' => 'Poor customer service complaint',
                                'description' => 'Unhappy with staff treatment at store',
                                'category' => 'Complaint',
                                'priority' => 'Critical',
                                'status' => 'Open',
                                'assigned' => 'Manager',
                                'assigned_initials' => 'MG',
                                'created' => '3 hours ago',
                                'updated' => '2 hours ago',
                                'sale_ref' => '-'
                            ],
                            [
                                'id' => '#TKT-006',
                                'customer' => 'Lisa Wong',
                                'customer_initials' => 'LW',
                                'subject' => 'Loyalty points not credited',
                                'description' => 'Points missing from recent purchase',
                                'category' => 'Inquiry',
                                'priority' => 'Medium',
                                'status' => 'Resolved',
                                'assigned' => 'Carlos Diaz',
                                'assigned_initials' => 'CD',
                                'created' => '3 days ago',
                                'updated' => '2 days ago',
                                'sale_ref' => '#SALE-5589'
                            ],
                            [
                                'id' => '#TKT-007',
                                'customer' => 'David Lim',
                                'customer_initials' => 'DL',
                                'subject' => 'Exchange request - Different color',
                                'description' => 'Customer wants to exchange for different color',
                                'category' => 'Order Issue',
                                'priority' => 'Low',
                                'status' => 'In Progress',
                                'assigned' => 'Juan Cruz',
                                'assigned_initials' => 'JC',
                                'created' => '1 day ago',
                                'updated' => '5 hours ago',
                                'sale_ref' => '#SALE-5602'
                            ],
                            [
                                'id' => '#TKT-008',
                                'customer' => 'Maria Garcia',
                                'customer_initials' => 'MG',
                                'subject' => 'Delayed delivery complaint',
                                'description' => 'Order not received within promised timeframe',
                                'category' => 'Complaint',
                                'priority' => 'High',
                                'status' => 'Closed',
                                'assigned' => 'Maria Lopez',
                                'assigned_initials' => 'ML',
                                'created' => '5 days ago',
                                'updated' => '4 days ago',
                                'sale_ref' => '#SALE-5534'
                            ]
                        ];

                        foreach ($tickets as $ticket) {
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
                            
                            echo '<tr>';
                            echo '<td><input type="checkbox"></td>';
                            echo '<td><span class="contact-id">' . $ticket['id'] . '</span></td>';
                            echo '<td>';
                            echo '<div class="contact-name-cell">';
                            echo '<div class="contact-avatar">' . $ticket['customer_initials'] . '</div>';
                            echo '<div class="contact-name-info">';
                            echo '<div class="contact-name-primary">' . $ticket['customer'] . '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td>';
                            echo '<div class="ticket-subject">';
                            echo '<div class="ticket-subject-title">' . $ticket['subject'] . '</div>';
                            echo '<div class="ticket-subject-desc">' . $ticket['description'] . '</div>';
                            if ($ticket['sale_ref'] !== '-') {
                                echo '<div class="ticket-sale-ref">🔗 ' . $ticket['sale_ref'] . '</div>';
                            }
                            echo '</div>';
                            echo '</td>';
                            echo '<td><span class="category-badge ' . $categoryClass . '">' . $ticket['category'] . '</span></td>';
                            echo '<td><span class="priority-badge ' . $priorityClass . '">' . $ticket['priority'] . '</span></td>';
                            echo '<td><span class="status-badge ' . $statusClass . '">' . strtoupper($ticket['status']) . '</span></td>';
                            echo '<td>';
                            echo '<div class="contact-name-cell">';
                            echo '<div class="contact-avatar" style="width: 32px; height: 32px; font-size: 12px;">' . $ticket['assigned_initials'] . '</div>';
                            echo '<span>' . $ticket['assigned'] . '</span>';
                            echo '</div>';
                            echo '</td>';
                            echo '<td>' . $ticket['created'] . '</td>';
                            echo '<td>' . $ticket['updated'] . '</td>';
                            echo '<td>';
                            echo '<div class="action-buttons">';
                            echo '<button class="action-btn">View</button>';
                            echo '<button class="action-btn">Update</button>';
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
                    Showing <strong>1-8</strong> of <strong>342</strong> tickets
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
                                <option value="1">John Santos - john.santos@email.com</option>
                                <option value="2">Maria Garcia - maria.garcia@email.com</option>
                                <option value="3">Robert Chen - robert.chen@email.com</option>
                                <option value="4">Ana Reyes - ana.reyes@email.com</option>
                                <option value="5">David Lim - david.lim@email.com</option>
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
                                <option value="1">Manila Branch</option>
                                <option value="2">Makati Branch</option>
                                <option value="3">Quezon City Branch</option>
                                <option value="4">Cebu Branch</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Assign To *</label>
                            <select class="filter-select" name="assigned_to" required>
                                <option value="">Select agent</option>
                                <option value="1">Maria Lopez</option>
                                <option value="2">Carlos Diaz</option>
                                <option value="3">Juan Cruz</option>
                                <option value="4">Manager</option>
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
    </style>

    <script>
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
                // Here you would send data to PHP backend
                // PHP will validate user role (can_create_support_tickets)
                // Insert into SupportTickets table
            } else {
                form.reportValidity();
            }
        }

        // Close modal on overlay click
        document.getElementById('ticketModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTicketModal();
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

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                
                if (action === 'View') {
                    alert('View ticket details');
                } else if (action === 'Update') {
                    alert('Update ticket status');
                }
            });
        });

        // Table row clicks
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
                    const ticketId = this.querySelector('.contact-id').textContent;
                    alert('Opening ticket: ' + ticketId);
                }
            });
        });
    </script>
</body>
</html>