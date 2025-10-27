<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks Management - CRM System</title>
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
                <li><a href="./dealsManagement.php">Deals</a></li>
                <li><a href="#" class="active">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search tasks...">
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
                    <span>Tasks Management</span>
                </div>
                <h1 class="page-title">Tasks & Activities</h1>
                <p class="page-subtitle">Manage and track all your tasks and follow-ups</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📋</span>
                    <span>My Tasks</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Reports</span>
                </button>
                <button class="btn btn-primary" onclick="openModal()">
                    <span>+</span>
                    <span>Add Task</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '📝',
                    'value' => '84',
                    'label' => 'Total Tasks',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '⏳',
                    'value' => '23',
                    'label' => 'In Progress',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '✅',
                    'value' => '45',
                    'label' => 'Completed',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '🔴',
                    'value' => '8',
                    'label' => 'Overdue',
                    'color' => '#fee2e2'
                ],
                [
                    'icon' => '📅',
                    'value' => '12',
                    'label' => 'Due Today',
                    'color' => '#e9d5ff'
                ],
                [
                    'icon' => '👤',
                    'value' => '28',
                    'label' => 'Assigned to Me',
                    'color' => '#ddd6fe'
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

        <div class="tabs-section">
            <div class="tabs">
                <button class="tab-btn active" onclick="switchView('board')">
                    <span>📊</span> Board View
                </button>
                <button class="tab-btn" onclick="switchView('list')">
                    <span>📋</span> List View
                </button>
            </div>
            <div class="filter-group">
                <select class="filter-select">
                    <option>All Tasks</option>
                    <option>My Tasks</option>
                    <option>Team Tasks</option>
                </select>
                <select class="filter-select">
                    <option>All Priority</option>
                    <option>High</option>
                    <option>Medium</option>
                    <option>Low</option>
                </select>
                <select class="filter-select">
                    <option>All Time</option>
                    <option>Today</option>
                    <option>This Week</option>
                    <option>This Month</option>
                </select>
            </div>
        </div>

        <!-- Board View (Kanban) -->
        <div class="task-board" id="boardView">
            <?php
            $taskColumns = [
                [
                    'status' => 'To Do',
                    'icon' => '📋',
                    'count' => 16,
                    'tasks' => [
                        [
                            'id' => '#TASK-001',
                            'title' => 'Follow up with TechCorp lead',
                            'description' => 'Schedule demo meeting and send proposal',
                            'priority' => 'High',
                            'due_date' => 'Today',
                            'assignee' => 'JS',
                            'tags' => ['Sales', 'Follow-up'],
                            'related' => '🏢 TechCorp'
                        ],
                        [
                            'id' => '#TASK-005',
                            'title' => 'Prepare Q4 sales report',
                            'description' => 'Compile data from all regions',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 15',
                            'assignee' => 'MG',
                            'tags' => ['Report'],
                            'related' => '📊 Analytics'
                        ],
                        [
                            'id' => '#TASK-009',
                            'title' => 'Update CRM database',
                            'description' => 'Add new contacts from trade show',
                            'priority' => 'Low',
                            'due_date' => 'Dec 20',
                            'assignee' => 'AR',
                            'tags' => ['Data Entry'],
                            'related' => '💼 Operations'
                        ]
                    ]
                ],
                [
                    'status' => 'In Progress',
                    'icon' => '⏳',
                    'count' => 23,
                    'tasks' => [
                        [
                            'id' => '#TASK-002',
                            'title' => 'Create proposal for Innovation Labs',
                            'description' => 'Include pricing and timeline details',
                            'priority' => 'High',
                            'due_date' => 'Today',
                            'assignee' => 'RC',
                            'tags' => ['Proposal', 'Urgent'],
                            'related' => '💡 Innovation Labs'
                        ],
                        [
                            'id' => '#TASK-006',
                            'title' => 'Client onboarding - Global Solutions',
                            'description' => 'Set up account and training schedule',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 16',
                            'assignee' => 'DL',
                            'tags' => ['Onboarding'],
                            'related' => '🌐 Global Solutions'
                        ]
                    ]
                ],
                [
                    'status' => 'Review',
                    'icon' => '👀',
                    'count' => 8,
                    'tasks' => [
                        [
                            'id' => '#TASK-003',
                            'title' => 'Review contract terms with Legal',
                            'description' => 'Enterprise agreement for new client',
                            'priority' => 'High',
                            'due_date' => 'Dec 14',
                            'assignee' => 'ST',
                            'tags' => ['Legal', 'Contract'],
                            'related' => '📄 Enterprise Group'
                        ],
                        [
                            'id' => '#TASK-007',
                            'title' => 'Marketing campaign approval',
                            'description' => 'Review Q1 campaign materials',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 18',
                            'assignee' => 'MC',
                            'tags' => ['Marketing'],
                            'related' => '📱 Campaign'
                        ]
                    ]
                ],
                [
                    'status' => 'Completed',
                    'icon' => '✅',
                    'count' => 45,
                    'tasks' => [
                        [
                            'id' => '#TASK-004',
                            'title' => 'Send welcome email to new clients',
                            'description' => 'Include onboarding resources',
                            'priority' => 'Medium',
                            'due_date' => 'Dec 10',
                            'assignee' => 'LW',
                            'tags' => ['Email', 'Completed'],
                            'related' => '📧 Communications'
                        ],
                        [
                            'id' => '#TASK-008',
                            'title' => 'Update product pricing',
                            'description' => 'Reflect new 2025 rates',
                            'priority' => 'Low',
                            'due_date' => 'Dec 08',
                            'assignee' => 'JS',
                            'tags' => ['Pricing'],
                            'related' => '💰 Finance'
                        ]
                    ]
                ]
            ];

            foreach ($taskColumns as $column) {
                echo '<div class="task-column">';
                echo '<div class="task-column-header">';
                echo '<div class="task-column-title">';
                echo '<span class="task-column-name">' . $column['icon'] . ' ' . $column['status'] . '</span>';
                echo '<span class="task-count">' . $column['count'] . '</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="task-list">';
                
                foreach ($column['tasks'] as $task) {
                    $priorityClass = 'priority-' . strtolower($task['priority']);
                    $dateClass = '';
                    if ($task['due_date'] === 'Today') {
                        $dateClass = 'today';
                    } elseif (strtotime($task['due_date']) < time()) {
                        $dateClass = 'overdue';
                    }
                    
                    echo '<div class="task-card">';
                    echo '<div class="task-card-header">';
                    echo '<input type="checkbox" class="task-checkbox" ' . ($column['status'] === 'Completed' ? 'checked' : '') . '>';
                    echo '<div class="task-content">';
                    echo '<div class="task-title">' . $task['title'] . '</div>';
                    echo '<div class="task-description">' . $task['description'] . '</div>';
                    echo '<div class="task-meta">';
                    echo '<div class="task-meta-item">';
                    echo '<div class="task-avatar">' . $task['assignee'] . '</div>';
                    echo '</div>';
                    echo '<div class="task-meta-item">';
                    echo '<span class="priority-badge ' . $priorityClass . '">' . $task['priority'] . '</span>';
                    echo '</div>';
                    echo '<div class="task-meta-item task-date ' . $dateClass . '">';
                    echo '📅 ' . $task['due_date'];
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="task-tags">';
                    foreach ($task['tags'] as $tag) {
                        echo '<span class="task-tag">' . $tag . '</span>';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- List View -->
        <div class="list-view" id="listView">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="section-title">All Tasks (84)</h2>
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
                                <th>Task ID</th>
                                <th>Task Name</th>
                                <th>Related To</th>
                                <th>Assignee</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Due Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $allTasks = [
                                [
                                    'id' => '#TASK-001',
                                    'name' => 'Follow up with TechCorp lead',
                                    'description' => 'Schedule demo meeting and send proposal',
                                    'related' => 'TechCorp Philippines',
                                    'related_icon' => '🏢',
                                    'assignee' => 'John Santos',
                                    'assignee_initials' => 'JS',
                                    'priority' => 'High',
                                    'status' => 'To Do',
                                    'due_date' => 'Today'
                                ],
                                [
                                    'id' => '#TASK-002',
                                    'name' => 'Create proposal for Innovation Labs',
                                    'description' => 'Include pricing and timeline details',
                                    'related' => 'Innovation Labs',
                                    'related_icon' => '💡',
                                    'assignee' => 'Robert Chen',
                                    'assignee_initials' => 'RC',
                                    'priority' => 'High',
                                    'status' => 'In Progress',
                                    'due_date' => 'Today'
                                ],
                                [
                                    'id' => '#TASK-003',
                                    'name' => 'Review contract terms with Legal',
                                    'description' => 'Enterprise agreement for new client',
                                    'related' => 'Enterprise Group',
                                    'related_icon' => '🏛️',
                                    'assignee' => 'Sarah Tan',
                                    'assignee_initials' => 'ST',
                                    'priority' => 'High',
                                    'status' => 'Review',
                                    'due_date' => 'Dec 14, 2024'
                                ],
                                [
                                    'id' => '#TASK-004',
                                    'name' => 'Send welcome email to new clients',
                                    'description' => 'Include onboarding resources',
                                    'related' => 'Communications',
                                    'related_icon' => '📧',
                                    'assignee' => 'Lisa Wong',
                                    'assignee_initials' => 'LW',
                                    'priority' => 'Medium',
                                    'status' => 'Completed',
                                    'due_date' => 'Dec 10, 2024'
                                ],
                                [
                                    'id' => '#TASK-005',
                                    'name' => 'Prepare Q4 sales report',
                                    'description' => 'Compile data from all regions',
                                    'related' => 'Analytics',
                                    'related_icon' => '📊',
                                    'assignee' => 'Maria Garcia',
                                    'assignee_initials' => 'MG',
                                    'priority' => 'Medium',
                                    'status' => 'To Do',
                                    'due_date' => 'Dec 15, 2024'
                                ],
                                [
                                    'id' => '#TASK-006',
                                    'name' => 'Client onboarding - Global Solutions',
                                    'description' => 'Set up account and training schedule',
                                    'related' => 'Global Solutions',
                                    'related_icon' => '🌐',
                                    'assignee' => 'David Lim',
                                    'assignee_initials' => 'DL',
                                    'priority' => 'Medium',
                                    'status' => 'In Progress',
                                    'due_date' => 'Dec 16, 2024'
                                ],
                                [
                                    'id' => '#TASK-007',
                                    'name' => 'Marketing campaign approval',
                                    'description' => 'Review Q1 campaign materials',
                                    'related' => 'Marketing Campaign',
                                    'related_icon' => '📱',
                                    'assignee' => 'Michael Cruz',
                                    'assignee_initials' => 'MC',
                                    'priority' => 'Medium',
                                    'status' => 'Review',
                                    'due_date' => 'Dec 18, 2024'
                                ],
                                [
                                    'id' => '#TASK-008',
                                    'name' => 'Update product pricing',
                                    'description' => 'Reflect new 2025 rates',
                                    'related' => 'Finance',
                                    'related_icon' => '💰',
                                    'assignee' => 'John Santos',
                                    'assignee_initials' => 'JS',
                                    'priority' => 'Low',
                                    'status' => 'Completed',
                                    'due_date' => 'Dec 08, 2024'
                                ],
                                [
                                    'id' => '#TASK-009',
                                    'name' => 'Update CRM database',
                                    'description' => 'Add new contacts from trade show',
                                    'related' => 'Operations',
                                    'related_icon' => '💼',
                                    'assignee' => 'Ana Reyes',
                                    'assignee_initials' => 'AR',
                                    'priority' => 'Low',
                                    'status' => 'To Do',
                                    'due_date' => 'Dec 20, 2024'
                                ]
                            ];

                            foreach ($allTasks as $task) {
                                $statusClass = 'status-' . strtolower(str_replace(' ', '-', $task['status']));
                                $priorityClass = 'priority-' . strtolower($task['priority']);
                                
                                echo '<tr>';
                                echo '<td><input type="checkbox" ' . ($task['status'] === 'Completed' ? 'checked' : '') . '></td>';
                                echo '<td><span class="task-id">' . $task['id'] . '</span></td>';
                                echo '<td>';
                                echo '<div class="task-name-cell">';
                                echo '<div class="task-name-primary">' . $task['name'] . '</div>';
                                echo '<div class="task-name-secondary">' . $task['description'] . '</div>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="company-cell">';
                                echo '<div class="company-icon">' . $task['related_icon'] . '</div>';
                                echo '<span>' . $task['related'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="assignee-cell">';
                                echo '<div class="task-avatar">' . $task['assignee_initials'] . '</div>';
                                echo '<span>' . $task['assignee'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td><span class="priority-badge ' . $priorityClass . '">' . $task['priority'] . '</span></td>';
                                echo '<td><span class="status-badge ' . $statusClass . '">' . strtoupper($task['status']) . '</span></td>';
                                echo '<td>' . $task['due_date'] . '</td>';
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
                        Showing <strong>1-9</strong> of <strong>84</strong> tasks
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

    <!-- Add Task Modal -->
    <div class="modal-overlay" id="taskModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Task</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Task Name *</label>
                            <input type="text" class="form-input" placeholder="Enter task name" required>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Description</label>
                            <textarea class="form-textarea" placeholder="Add task description and details..."></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Related To</label>
                            <select class="filter-select">
                                <option value="">Select relation</option>
                                <option>Lead</option>
                                <option>Contact</option>
                                <option>Deal</option>
                                <option>Company</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Select Record</label>
                            <input type="text" class="form-input" placeholder="Search...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Task Type *</label>
                            <select class="filter-select" required>
                                <option value="">Select type</option>
                                <option>Call</option>
                                <option>Email</option>
                                <option>Meeting</option>
                                <option>Follow-up</option>
                                <option>Other</option>
                            </select>
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
                                <option selected>To Do</option>
                                <option>In Progress</option>
                                <option>Review</option>
                                <option>Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Due Date *</label>
                            <input type="date" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Assign To *</label>
                            <select class="filter-select" required>
                                <option value="">Select assignee</option>
                                <option>John Santos</option>
                                <option>Maria Garcia</option>
                                <option>Robert Chen</option>
                                <option>Ana Reyes</option>
                                <option>David Lim</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Reminder</label>
                            <select class="filter-select">
                                <option>No reminder</option>
                                <option>1 hour before</option>
                                <option>1 day before</option>
                                <option>1 week before</option>
                            </select>
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
                <button class="btn btn-primary" onclick="saveTask()">Create Task</button>
            </div>
        </div>
    </div>

    <script>
        // View switching
        function switchView(view) {
            const boardView = document.getElementById('boardView');
            const listView = document.getElementById('listView');
            const tabButtons = document.querySelectorAll('.tab-btn');

            tabButtons.forEach(btn => btn.classList.remove('active'));

            if (view === 'board') {
                boardView.style.display = 'grid';
                listView.classList.remove('active');
                tabButtons[0].classList.add('active');
            } else {
                boardView.style.display = 'none';
                listView.classList.add('active');
                tabButtons[1].classList.add('active');
            }
        }

        // Modal Functions
        function openModal() {
            document.getElementById('taskModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('taskModal').classList.remove('active');
        }

        function saveTask() {
            alert('Task created successfully!');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('taskModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Search functionality
        const searchBox = document.querySelector('.search-box');
        searchBox.addEventListener('input', function(e) {
            console.log('Searching for:', e.target.value);
        });

        // Task checkbox handling
        document.querySelectorAll('.task-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const taskCard = this.closest('.task-card');
                if (this.checked) {
                    taskCard.style.opacity = '0.6';
                    taskCard.querySelector('.task-title').style.textDecoration = 'line-through';
                } else {
                    taskCard.style.opacity = '1';
                    taskCard.querySelector('.task-title').style.textDecoration = 'none';
                }
            });
        });

        // Table checkbox handling
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

        // Task card clicks
        document.querySelectorAll('.task-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.task-checkbox')) {
                    console.log('Task card clicked:', this.querySelector('.task-title').textContent);
                }
            });
        });

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Action:', action);
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this task?')) {
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