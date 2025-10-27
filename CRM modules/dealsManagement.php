<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deals Management - CRM System</title>
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