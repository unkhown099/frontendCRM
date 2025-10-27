<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management - CRM System</title>
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
                <li><a href="#" class="active">Contacts</a></li>
                <li><a href="./dealsManagement.php">Deals</a></li>
                <li><a href="./tasksManagement.php">Tasks</a></li>
                <li><a href="./reportsManagement.php">Reports & Analytics</a></li>
            </ul>
            <div class="nav-right">
                <div class="search-wrapper">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-box" placeholder="Search contacts...">
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
                    <span>Contact Management</span>
                </div>
                <h1 class="page-title">Contact Management</h1>
                <p class="page-subtitle">Organize and manage all your business contacts</p>
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
                    <span>Add Contact</span>
                </button>
            </div>
        </div>

        <div class="stats-grid">
            <?php
            $stats = [
                [
                    'icon' => '👥',
                    'value' => '2,456',
                    'label' => 'Total Contacts',
                    'color' => '#dbeafe'
                ],
                [
                    'icon' => '✅',
                    'value' => '1,834',
                    'label' => 'Active Contacts',
                    'color' => '#d1fae5'
                ],
                [
                    'icon' => '🤝',
                    'value' => '892',
                    'label' => 'Customers',
                    'color' => '#ddd6fe'
                ],
                [
                    'icon' => '🎯',
                    'value' => '534',
                    'label' => 'Prospects',
                    'color' => '#fef3c7'
                ],
                [
                    'icon' => '⭐',
                    'value' => '156',
                    'label' => 'Partners',
                    'color' => '#e9d5ff'
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

        <div class="view-controls">
            <div class="view-toggle">
                <button class="view-btn active" onclick="switchView('grid')">
                    <span>📱</span> Grid View
                </button>
                <button class="view-btn" onclick="switchView('list')">
                    <span>📋</span> List View
                </button>
            </div>
            <div class="filter-group">
                <select class="filter-select">
                    <option>All Types</option>
                    <option>Customer</option>
                    <option>Partner</option>
                    <option>Vendor</option>
                    <option>Prospect</option>
                </select>
                <select class="filter-select">
                    <option>All Status</option>
                    <option>Active</option>
                    <option>Inactive</option>
                </select>
                <select class="filter-select">
                    <option>Sort by Name</option>
                    <option>Sort by Date</option>
                    <option>Sort by Company</option>
                </select>
            </div>
        </div>

        <!-- Grid View -->
        <div class="contacts-grid" id="gridView">
            <?php
            $contacts = [
                [
                    'id' => '#CONT-001',
                    'name' => 'John Santos',
                    'initials' => 'JS',
                    'title' => 'Chief Technology Officer',
                    'company' => 'TechCorp Philippines',
                    'email' => 'john.santos@techcorp.ph',
                    'phone' => '+63 917 123 4567',
                    'location' => 'Manila, Philippines',
                    'type' => 'Customer',
                    'status' => 'Active',
                    'tags' => ['VIP', 'Tech']
                ],
                [
                    'id' => '#CONT-002',
                    'name' => 'Maria Garcia',
                    'initials' => 'MG',
                    'title' => 'Marketing Director',
                    'company' => 'Global Solutions Inc',
                    'email' => 'maria.g@globalsol.com',
                    'phone' => '+63 918 234 5678',
                    'location' => 'Makati, Philippines',
                    'type' => 'Partner',
                    'status' => 'Active',
                    'tags' => ['Marketing', 'Strategic']
                ],
                [
                    'id' => '#CONT-003',
                    'name' => 'Robert Chen',
                    'initials' => 'RC',
                    'title' => 'Founder & CEO',
                    'company' => 'Innovation Labs',
                    'email' => 'r.chen@innovationlabs.io',
                    'phone' => '+63 919 345 6789',
                    'location' => 'Taguig, Philippines',
                    'type' => 'Customer',
                    'status' => 'Active',
                    'tags' => ['VIP', 'Innovation']
                ],
                [
                    'id' => '#CONT-004',
                    'name' => 'Ana Reyes',
                    'initials' => 'AR',
                    'title' => 'Business Development Manager',
                    'company' => 'StartUp Innovations',
                    'email' => 'ana@startupinc.ph',
                    'phone' => '+63 920 456 7890',
                    'location' => 'Quezon City, Philippines',
                    'type' => 'Prospect',
                    'status' => 'Active',
                    'tags' => ['Startup']
                ],
                [
                    'id' => '#CONT-005',
                    'name' => 'David Lim',
                    'initials' => 'DL',
                    'title' => 'Operations Manager',
                    'company' => 'Enterprise Solutions Group',
                    'email' => 'david.lim@entgroup.com',
                    'phone' => '+63 921 567 8901',
                    'location' => 'Pasig, Philippines',
                    'type' => 'Vendor',
                    'status' => 'Active',
                    'tags' => ['Operations']
                ],
                [
                    'id' => '#CONT-006',
                    'name' => 'Sarah Tan',
                    'initials' => 'ST',
                    'title' => 'Sales Director',
                    'company' => 'RetailCo Philippines',
                    'email' => 'sarah.tan@retailco.ph',
                    'phone' => '+63 922 678 9012',
                    'location' => 'Cebu City, Philippines',
                    'type' => 'Customer',
                    'status' => 'Active',
                    'tags' => ['Retail', 'Sales']
                ]
            ];

            foreach ($contacts as $contact) {
                echo '<div class="contact-card">';
                echo '<div class="contact-header">';
                echo '<div class="contact-avatar-large">' . $contact['initials'] . '</div>';
                echo '<div class="contact-info">';
                echo '<div class="contact-name">' . $contact['name'] . '</div>';
                echo '<div class="contact-title">' . $contact['title'] . '</div>';
                echo '<div class="contact-company">🏢 ' . $contact['company'] . '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="contact-details">';
                echo '<div class="contact-detail">';
                echo '<div class="contact-detail-icon">📧</div>';
                echo '<span>' . $contact['email'] . '</span>';
                echo '</div>';
                echo '<div class="contact-detail">';
                echo '<div class="contact-detail-icon">📱</div>';
                echo '<span>' . $contact['phone'] . '</span>';
                echo '</div>';
                echo '<div class="contact-detail">';
                echo '<div class="contact-detail-icon">📍</div>';
                echo '<span>' . $contact['location'] . '</span>';
                echo '</div>';
                echo '</div>';
                echo '<div class="contact-tags">';
                foreach ($contact['tags'] as $tag) {
                    echo '<span class="contact-tag">' . $tag . '</span>';
                }
                echo '</div>';
                echo '<div class="contact-actions">';
                echo '<button class="contact-action-btn">Message</button>';
                echo '<button class="contact-action-btn">Call</button>';
                echo '<button class="contact-action-btn">View</button>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- List View -->
        <div class="list-view" id="listView">
            <div class="content-card">
                <div class="card-header">
                    <h2 class="section-title">All Contacts (2,456)</h2>
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
                                <th>Contact ID</th>
                                <th>Name</th>
                                <th>Company</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($contacts as $contact) {
                                $typeClass = 'type-' . strtolower($contact['type']);
                                $statusClass = 'status-' . strtolower($contact['status']);
                                
                                echo '<tr>';
                                echo '<td><input type="checkbox"></td>';
                                echo '<td><span class="contact-id">' . $contact['id'] . '</span></td>';
                                echo '<td>';
                                echo '<div class="contact-name-cell">';
                                echo '<div class="contact-avatar">' . $contact['initials'] . '</div>';
                                echo '<div class="contact-name-info">';
                                echo '<div class="contact-name-primary">' . $contact['name'] . '</div>';
                                echo '<div class="contact-name-secondary">' . $contact['title'] . '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>';
                                echo '<div class="company-cell">';
                                echo '<div class="company-icon">🏢</div>';
                                echo '<span>' . $contact['company'] . '</span>';
                                echo '</div>';
                                echo '</td>';
                                echo '<td>' . $contact['email'] . '</td>';
                                echo '<td>' . $contact['phone'] . '</td>';
                                echo '<td><span class="type-badge ' . $typeClass . '">' . strtoupper($contact['type']) . '</span></td>';
                                echo '<td><span class="' . $statusClass . '">● ' . $contact['status'] . '</span></td>';
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
                        Showing <strong>1-6</strong> of <strong>2,456</strong> contacts
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

    <!-- Add Contact Modal -->
    <div class="modal-overlay" id="contactModal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Add New Contact</h3>
                <button class="close-btn" onclick="closeModal()">✕</button>
            </div>
            <div class="modal-body">
                <form id="contactForm">
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
                            <label class="form-label">Phone *</label>
                            <input type="tel" class="form-input" placeholder="+63 XXX XXX XXXX" required>
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
                            <label class="form-label">Contact Type *</label>
                            <select class="filter-select" required>
                                <option value="">Select type</option>
                                <option>Customer</option>
                                <option>Partner</option>
                                <option>Vendor</option>
                                <option>Prospect</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status *</label>
                            <select class="filter-select" required>
                                <option value="">Select status</option>
                                <option selected>Active</option>
                                <option>Inactive</option>
                            </select>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Address</label>
                            <input type="text" class="form-input" placeholder="Street address">
                        </div>
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" class="form-input" placeholder="City">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <input type="text" class="form-input" placeholder="Country">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-input" placeholder="Add tags (comma separated)">
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Notes</label>
                            <textarea class="form-textarea" placeholder="Add any additional notes about this contact..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-primary" onclick="saveContact()">Save Contact</button>
            </div>
        </div>
    </div>

    <script>
        // View switching
        function switchView(view) {
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const viewButtons = document.querySelectorAll('.view-btn');

            viewButtons.forEach(btn => btn.classList.remove('active'));

            if (view === 'grid') {
                gridView.style.display = 'grid';
                listView.classList.remove('active');
                viewButtons[0].classList.add('active');
            } else {
                gridView.style.display = 'none';
                listView.classList.add('active');
                viewButtons[1].classList.add('active');
            }
        }

        // Modal Functions
        function openModal() {
            document.getElementById('contactModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('contactModal').classList.remove('active');
        }

        function saveContact() {
            alert('Contact saved successfully!');
            closeModal();
        }

        // Close modal on overlay click
        document.getElementById('contactModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
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

        // Contact card clicks
        document.querySelectorAll('.contact-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('button')) {
                    console.log('Contact card clicked');
                }
            });
        });

        // Contact action buttons
        document.querySelectorAll('.contact-action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Contact action:', action);
            });
        });

        // Table action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const action = this.textContent.trim();
                console.log('Action:', action);
                
                if (action === 'Delete') {
                    if (confirm('Are you sure you want to delete this contact?')) {
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