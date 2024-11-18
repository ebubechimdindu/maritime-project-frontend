<?php include 'dashboard_process.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NautiGuard Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>

<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="logo">
                <h2>NautiGuard</h2>
            </div>
            <ul class="nav-links">
                <li><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="incident_report.php"><i class="fas fa-exclamation-triangle"></i> Report Incident</a></li>
                <li><a href="vessel_management.php"><i class="fas fa-ship"></i> Vessel Management</a></li>
                <li><a href="incident_history.php"><i class="fas fa-history"></i> Incident History</a></li>
                <li><a href="weather_alerts.php"><i class="fas fa-cloud"></i> Weather Alerts</a></li>
                <li><a href="compliance.php"><i class="fas fa-clipboard-check"></i> Compliance</a></li>
                <li><a href="emergency_contacts.php"><i class="fas fa-phone-alt"></i> Emergency Contacts</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="top-bar">
                <div class="search-bar">
                    <input type="text" placeholder="Search incidents, vessels...">
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo "Welcome " . $full_name; ?></span>
                    <div class="alert-icon">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                </div>
            </header>

            <div class="dashboard-grid">
                <!-- Quick Actions Card -->
                <!-- Quick Actions Card -->
                <div class="card">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <button class="btn btn-danger"><i class="fas fa-exclamation-circle"></i> Report Emergency</button>
                        <a href="vessel_management.php" class="btn btn-primary"><i class="fas fa-plus"></i> Register New Vessel</a>
                        <button class="btn btn-info"><i class="fas fa-file-alt"></i> Generate Safety Report</button>
                    </div>
                </div>


                <!-- Active Incidents Card -->
                <div class="card">
                    <h3>Active Incidents</h3>
                    <ul class="incident-list">
                        <li class="high-priority">
                            <span class="incident-type">Engine Failure</span>
                            <span class="incident-location">Gulf of Aden</span>
                            <span class="incident-time">2h ago</span>
                        </li>
                        <li class="medium-priority">
                            <span class="incident-type">Weather Warning</span>
                            <span class="incident-location">South China Sea</span>
                            <span class="incident-time">4h ago</span>
                        </li>
                    </ul>
                </div>

                <!-- Vessel Status Card -->
                <div class="card">
                    <h3>Vessel Status</h3>
                    <div class="vessel-stats">
                        <div class="stat-item">
                            <span class="number"><?php echo $active_result['count']; ?></span>
                            <span class="label">Active Vessels</span>
                        </div>
                        <div class="stat-item">
                            <span class="number"><?php echo $in_port_result['count']; ?></span>
                            <span class="label">In Port</span>
                        </div>
                        <div class="stat-item">
                            <span class="number"><?php echo $at_sea_result['count']; ?></span>
                            <span class="label">At Sea</span>
                        </div>
                    </div>
                </div>


                <!-- Weather Alerts Card -->
                <div class="card">
                    <h3>Weather Alerts</h3>
                    <div class="weather-alerts">
                        <div class="alert-item warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Storm Warning: South Pacific Region</span>
                        </div>
                        <div class="alert-item info">
                            <i class="fas fa-info-circle"></i>
                            <span>High Waves: Mediterranean Sea</span>
                        </div>
                    </div>
                </div>

                <!-- Compliance Status Card -->
                <div class="card">
                    <h3>Compliance Status</h3>
                    <div class="compliance-status">
                        <div class="progress-item">
                            <label>Safety Certifications</label>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 92%">92%</div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <label>Documentation</label>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: 85%">85%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="card">
                    <h3>Recent Activities</h3>
                    <ul class="activity-list">
                        <li><i class="fas fa-check text-success"></i> Safety inspection completed - Vessel MV Atlantic</li>
                        <li><i class="fas fa-file text-primary"></i> New incident report submitted</li>
                        <li><i class="fas fa-ship text-info"></i> Vessel MSC Pacific registered</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <!-- Logout Modal with Modern UI -->
    <div class="modal fade logout-modal-wrapper" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modern-logout-modal">
                <div class="modal-body">
                    <div class="logout-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                    </div>
                    <h3 class="logout-title">Log Out</h3>
                    <p class="logout-message">Are you sure you want to log out of your account?</p>
                    <div class="logout-actions">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
                        <a href="logout.php" class="btn btn-logout">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>