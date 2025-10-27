<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - CRM System</title>
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
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="#" class="active">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search reports...">
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
                    <span>Reports & Analytics</span>
                </div>
                <h1 class="page-title">Reports & Analytics</h1>
                <p class="page-subtitle">Comprehensive insights and performance metrics</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-secondary">
                    <span>📊</span>
                    <span>Custom Report</span>
                </button>
                <button class="btn btn-secondary">
                    <span>📥</span>
                    <span>Export All</span>
                </button>
                <button class="btn btn-primary">
                    <span>📧</span>
                    <span>Schedule Report</span>
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-header">
                <div class="filter-title">📅 Report Filters</div>
                <button class="btn btn-secondary" style="padding: 6px 12px; font-size: 13px;">Reset Filters</button>
            </div>
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">Date Range</label>
                    <select class="filter-select">
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Quarter</option>
                        <option>Last Quarter</option>
                        <option>This Year</option>
                        <option>Custom Range</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Report Type</label>
                    <select class="filter-select">
                        <option>All Reports</option>
                        <option>Sales Reports</option>
                        <option>Lead Reports</option>
                        <option>Performance Reports</option>
                        <option>Activity Reports</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Team Member</label>
                    <select class="filter-select">
                        <option>All Team Members</option>
                        <option>John Santos</option>
                        <option>Maria Garcia</option>
                        <option>Robert Chen</option>
                        <option>Ana Reyes</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Region</label>
                    <select class="filter-select">
                        <option>All Regions</option>
                        <option>Metro Manila</option>
                        <option>Visayas</option>
                        <option>Mindanao</option>
                        <option>North Luzon</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '💰',
                    'value' => '₱12.4M',
                    'label' => 'Total Revenue',
                    'sublabel' => 'vs ₱10.2M last month',
                    'trend' => '+21.6%',
                    'trend_dir' => 'up',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '📈',
                    'value' => '156',
                    'label' => 'Deals Closed',
                    'sublabel' => 'vs 128 last month',
                    'trend' => '+21.9%',
                    'trend_dir' => 'up',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '🎯',
                    'value' => '68%',
                    'label' => 'Win Rate',
                    'sublabel' => 'vs 62% last month',
                    'trend' => '+9.7%',
                    'trend_dir' => 'up',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '⏱️',
                    'value' => '18 days',
                    'label' => 'Avg. Sales Cycle',
                    'sublabel' => 'vs 22 days last month',
                    'trend' => '-18.2%',
                    'trend_dir' => 'up',
                    'color' => '#fef3c7'
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

        <!-- Charts Section -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">Monthly Revenue</div>
                    <div class="chart-actions">
                        <button class="icon-btn">⟳</button>
                        <button class="icon-btn">⬇</button>
                    </div>
                </div>
                <div class="bar-chart">
                    <?php
                    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct'];
                    $values = [50,65,70,80,90,110,105,115,100,120];
                    foreach ($months as $i => $month) {
                        $height = $values[$i] * 2; // simple scaling
                        echo '<div class="bar" style="height:'.$height.'px;">
                                <span class="bar-value">₱'.$values[$i].'k</span>
                                <span class="bar-label">'.$month.'</span>
                              </div>';
                    }
                    ?>
                </div>
            </div>

            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">Lead Conversion Trend</div>
                    <div class="chart-actions">
                        <button class="icon-btn">⟳</button>
                        <button class="icon-btn">⬇</button>
                    </div>
                </div>
                <div class="line-chart">
                    <div class="line-chart-grid">
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                    </div>
                    <div class="line-chart-content">
                        <?php
                        $points = [10,30,45,40,60,70,65,85];
                        foreach ($points as $p) {
                            echo '<div class="line-point" style="margin-bottom:'.$p.'%;"><span class="line-point-label">'.$p.'%</span></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports Table Section -->
        <div class="reports-table-section">
            <div class="table-header">
                <h2 class="section-title">Recent Reports</h2>
                <button class="btn btn-secondary">View All</button>
            </div>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Report Name</th>
                            <th>Category</th>
                            <th>Date Generated</th>
                            <th>Generated By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $reports = [
                            ['name'=>'Q3 Sales Overview','category'=>'Sales','date'=>'Oct 10, 2025','by'=>'Maria Garcia'],
                            ['name'=>'Leads Performance Report','category'=>'Leads','date'=>'Oct 8, 2025','by'=>'John Santos'],
                            ['name'=>'Team Productivity Metrics','category'=>'Performance','date'=>'Oct 5, 2025','by'=>'Ana Reyes'],
                            ['name'=>'Daily Activity Summary','category'=>'Activity','date'=>'Oct 3, 2025','by'=>'Robert Chen']
                        ];
                        foreach ($reports as $r) {
                            $badgeClass = match($r['category']) {
                                'Sales' => 'category-sales',
                                'Leads' => 'category-leads',
                                'Performance' => 'category-performance',
                                'Activity' => 'category-activity',
                                default => ''
                            };
                            echo "<tr>
                                    <td class='report-name'>{$r['name']}</td>
                                    <td><span class='category-badge {$badgeClass}'>{$r['category']}</span></td>
                                    <td>{$r['date']}</td>
                                    <td>{$r['by']}</td>
                                    <td class='action-buttons'>
                                        <button class='action-btn'>View</button>
                                        <button class='action-btn'>Export</button>
                                    </td>
                                  </tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>