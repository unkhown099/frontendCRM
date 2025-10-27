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
            $stats = [
                [
                    'icon' => '👥',
                    'value' => '1,847',
                    'label' => 'Total Leads',
                    'sublabel' => 'Active in pipeline',
                    'trend' => '+12.5%',
                    'trend_dir' => 'up',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '✅',
                    'value' => '342',
                    'label' => 'Qualified Leads',
                    'sublabel' => 'Ready for conversion',
                    'trend' => '+8.2%',
                    'trend_dir' => 'up',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '💰',
                    'value' => '₱2.4M',
                    'label' => 'Pipeline Value',
                    'sublabel' => 'Potential revenue',
                    'trend' => '+23.1%',
                    'trend_dir' => 'up',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '📈',
                    'value' => '87%',
                    'label' => 'Conversion Rate',
                    'sublabel' => 'This quarter',
                    'trend' => '+5.4%',
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
                        $leads = [
                            [
                                'id' => '#LEAD-001',
                                'name' => 'John Santos',
                                'initials' => 'JS',
                                'email' => 'john.santos@techcorp.ph',
                                'company' => 'TechCorp PH',
                                'company_icon' => '🏢',
                                'value' => '₱125,000',
                                'priority' => 'High',
                                'status' => 'Qualified'
                            ]
                        ];

                        foreach ($leads as $lead) {
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
                <div class="showing-text">
                    Showing <strong>1-5</strong> of <strong>1,847</strong> leads
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