<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Management - CRM System</title>
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
                <li><a href="#" class="active">Leads</a></li>
                <li><a href="./contactManagement.php">Contacts</a></li>
                <li><a href="./dealsManagement.php">Deals</a></li>
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports and Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search leads...">
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
                    <span>Lead Management</span>
                </div>
                <h1 class="page-title">Lead Management</h1>
                <p class="page-subtitle">Manage and track all your leads in one place</p>
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
                    <span>Add New Lead</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '📋',
                    'value' => '1,847',
                    'label' => 'Total Leads',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '🆕',
                    'value' => '234',
                    'label' => 'New This Week',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '✅',
                    'value' => '342',
                    'label' => 'Qualified',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '🔄',
                    'value' => '128',
                    'label' => 'In Progress',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '🎯',
                    'value' => '89',
                    'label' => 'Converted',
                    'color' => '#e9d5ff'
                ],
                [
                    'icon' => '❌',
                    'value' => '56',
                    'label' => 'Lost',
                    'color' => '#fee2e2'
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

        <div class="filters-section">
            <div class="filters-header">
                <div class="filters-title">🔍 Filter Leads</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset</button>
            </div>
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select class="filter-select">
                        <option>All Status</option>
                        <option>New</option>
                        <option>Contacted</option>
                        <option>Qualified</option>
                        <option>Negotiating</option>
                        <option>Converted</option>
                        <option>Lost</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Priority</label>
                    <select class="filter-select">
                        <option>All Priority</option>
                        <option>High</option>
                        <option>Medium</option>
                        <option>Low</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Source</label>
                    <select class="filter-select">
                        <option>All Sources</option>
                        <option>Website</option>
                        <option>Referral</option>
                        <option>Social Media</option>
                        <option>Cold Call</option>
                        <option>Email Campaign</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Date Range</label>
                    <select class="filter-select">
                        <option>All Time</option>
                        <option>Today</option>
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>This Quarter</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="section-title">All Leads (1,847)</h2>
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
                            <th>Lead ID</th>
                            <th>Contact</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Source</th>
                            <th>Value</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $leads = [
                            [
                                'id' => '#LEAD-001',
                                'name' => 'John Santos',
                                'initials' => 'JS',
                                'email' => 'john.santos@techcorp.ph',
                                'company' => 'TechCorp PH',
                                'company_icon' => '🏢',
                                'phone' => '+63 917 123 4567',
                                'source' => 'Website',
                                'value' => '₱125,000',
                                'priority' => 'High',
                                'status' => 'Qualified',
                                'created' => '2 days ago'
                            ],
                            [
                                'id' => '#LEAD-002',
                                'name' => 'Maria Garcia',
                                'initials' => 'MG',
                                'email' => 'maria.g@globalsol.com',
                                'company' => 'Global Solutions',
                                'company_icon' => '🌐',
                                'phone' => '+63 918 234 5678',
                                'source' => 'Referral',
                                'value' => '₱85,000',
                                'priority' => 'Medium',
                                'status' => 'Contacted',
                                'created' => '3 days ago'
                            ],
                            [
                                'id' => '#LEAD-003',
                                'name' => 'Robert Chen',
                                'initials' => 'RC',
                                'email' => 'r.chen@innovationlabs.io',
                                'company' => 'Innovation Labs',
                                'company_icon' => '💡',
                                'phone' => '+63 919 345 6789',
                                'source' => 'Social Media',
                                'value' => '₱200,000',
                                'priority' => 'High',
                                'status' => 'Converted',
                                'created' => '5 days ago'
                            ],
                            [
                                'id' => '#LEAD-004',
                                'name' => 'Ana Reyes',
                                'initials' => 'AR',
                                'email' => 'ana@startupinc.ph',
                                'company' => 'StartUp Inc',
                                'company_icon' => '🚀',
                                'phone' => '+63 920 456 7890',
                                'source' => 'Cold Call',
                                'value' => '₱45,000',
                                'priority' => 'Low',
                                'status' => 'New',
                                'created' => '1 week ago'
                            ],
                            [
                                'id' => '#LEAD-005',
                                'name' => 'David Lim',
                                'initials' => 'DL',
                                'email' => 'david.lim@entgroup.com',
                                'company' => 'Enterprise Group',
                                'company_icon' => '🏛️',
                                'phone' => '+63 921 567 8901',
                                'source' => 'Email Campaign',
                                'value' => '₱150,000',
                                'priority' => 'High',
                                'status' => 'Negotiating',
                                'created' => '1 week ago'
                            ],
                            [
                                'id' => '#LEAD-006',
                                'name' => 'Sarah Tan',
                                'initials' => 'ST',
                                'email' => 'sarah.tan@retailco.ph',
                                'company' => 'RetailCo',
                                'company_icon' => '🛍️',
                                'phone' => '+63 922 678 9012',
                                'source' => 'Website',
                                'value' => '₱95,000',
                                'priority' => 'Medium',
                                'status' => 'Qualified',
                                'created' => '2 weeks ago'
                            ],
                            [
                                'id' => '#LEAD-007',
                                'name' => 'Michael Cruz',
                                'initials' => 'MC',
                                'email' => 'mcruz@digitalagency.com',
                                'company' => 'Digital Agency Pro',
                                'company_icon' => '📱',
                                'phone' => '+63 923 789 0123',
                                'source' => 'Referral',
                                'value' => '₱175,000',
                                'priority' => 'High',
                                'status' => 'Contacted',
                                'created' => '3 weeks ago'
                            ],
                            [
                                'id' => '#LEAD-008',
                                'name' => 'Lisa Wong',
                                'initials' => 'LW',
                                'email' => 'lisa@fashionhub.ph',
                                'company' => 'Fashion Hub',
                                'company_icon' => '👗',
                                'phone' => '+63 924 890 1234',
                                'source' => 'Social Media',
                                'value' => '₱65,000',
                                'priority' => 'Low',
                                'status' => 'Lost',
                                'created' => '1 month ago'
                            ]
                        ];

                        foreach ($leads as $lead) {
                            $statusClass = 'status-' . strtolower($lead['status']);
                            $priorityClass = 'priority-' . strtolower($lead['priority']);
                            
                            echo '<tr>';
                            echo '<td><input type="checkbox"></td>';
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
                            echo '<td>' . $lead['phone'] . '</td>';
                            echo '<td><span class="source-badge">' . $lead['source'] . '</span></td>';
                            echo '<td><span class="value-cell">' . $lead['value'] . '</span></td>';
                            echo '<td><span class="priority-badge ' . $priorityClass . '">' . $lead['priority'] . '</span></td>';
                            echo '<td><span class="status-badge ' . $statusClass . '">' . strtoupper($lead['status']) . '</span></td>';
                            echo '<td>' . $lead['created'] . '</td>';
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
                    Showing <strong>1-8</strong> of <strong>1,847</strong> leads
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

    <!-- Add Lead Modal -->
    <div class="modal-overlay" id="leadModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Lead</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="leadForm">
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
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-input" placeholder="+63 XXX XXX XXXX">
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
                            <label class="form-label">Lead Source *</label>
                            <select class="filter-select" required>
                                <option value="">Select source</option>
                                <option>Website</option>
                                <option>Referral</option>
                                <option>Social Media</option>
                                <option>Cold Call</option>
                                <option>Email Campaign</option>
                                <option>Trade Show</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deal Value</label>
                            <input type="text" class="form-input" placeholder="₱0.00">
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
                                <option selected>New</option>
                                <option>Contacted</option>
                                <option>Qualified</option>
                                <option>Negotiating</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" placeholder="Add any additional notes about this lead..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveLead()">Save Lead</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Functions
        function openModal() {
            document.getElementById('leadModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('leadModal').classList.remove('active');
        }

        function saveLead() {
            alert('Lead saved successfully!');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('leadModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Table interactions
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('click', function(e) {
                if (!e.target.closest('button') && !e.target.closest('input[type="checkbox"]')) {
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

        // Select all checkbox
        const selectAll = document.querySelector('.table thead input[type="checkbox"]');
        const rowCheckboxes = document.querySelectorAll('.table tbody input[type="checkbox"]');

        selectAll.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Filter change listeners
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                console.log('Filter changed:', this.value);
            });
        });

        // Reset filters
        document.querySelector('.filters-section .btn-secondary').addEventListener('click', function() {
            document.querySelectorAll('.filter-select').forEach(select => {
                select.selectedIndex = 0;
            });
        });

        // Action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Action:', action);
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this lead?')) {
                        this.closest('tr').remove();
                    }
                }
            });
        });
    </script>
</body>
</html>