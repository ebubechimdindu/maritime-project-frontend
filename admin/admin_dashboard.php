<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="adminStyle.css">
</head>

<body class="admin-body">
    <div class="admin-container">
        <?php include('includes/admin_sidebar.php'); ?>

        <div class="admin-content">
            <div class="admin-header">
                <h1>Admin Dashboard</h1>
                <div class="admin-header-right">
                    <div class="notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="admin-profile">
                        <img src="../img/logo.png" alt="Admin">
                        <span>Admin</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <!-- Quick Actions Card -->
                <div class="card">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                    <div class="action-buttons">
                        <a href="emergency_response.php" class="btn btn-danger">
                            <i class="fas fa-exclamation-circle"></i> Emergency Response
                        </a>
                        <a href="incident_management.php" class="btn btn-warning">
                            <i class="fas fa-exclamation-triangle"></i> Manage Incidents
                        </a>
                        <a href="user_management.php" class="btn btn-primary">
                            <i class="fas fa-users"></i> Manage Users
                        </a>
                    </div>
                </div>

                <!-- System Overview Card -->
                <div class="card">
                    <h3><i class="fas fa-chart-pie"></i> System Overview</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <i class="fas fa-users"></i>
                            <span class="number">150</span>
                            <span class="label">Active Users</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-ship"></i>
                            <span class="number">45</span>
                            <span class="label">Vessels</span>
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-exclamation-circle"></i>
                            <span class="number">12</span>
                            <span class="label">Active Incidents</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Incidents Card -->
                <div class="card">
                    <h3><i class="fas fa-history"></i> Recent Incidents</h3>
                    <div class="incident-list">
                        <div class="incident-item high">
                            <span class="badge bg-danger">Critical</span>
                            <span class="incident-info">Engine Failure - MV Pacific</span>
                            <span class="incident-time">2h ago</span>
                        </div>
                        <div class="incident-item medium">
                            <span class="badge bg-warning">Medium</span>
                            <span class="incident-info">Navigation System Issue - MV Atlantic</span>
                            <span class="incident-time">5h ago</span>
                        </div>
                    </div>
                </div>

                <!-- Compliance Status Card -->
                <div class="card">
                    <h3><i class="fas fa-clipboard-check"></i> Compliance Overview</h3>
                    <div class="compliance-stats">
                        <div class="progress-item">
                            <label>Safety Compliance</label>
                            <div class="progress">
                                <div class="progress-bar bg-success" style="width: 85%">85%</div>
                            </div>
                        </div>
                        <div class="progress-item">
                            <label>Documentation Status</label>
                            <div class="progress">
                                <div class="progress-bar bg-info" style="width: 92%">92%</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Communication Hub Card -->
                <div class="card">
                    <h3><i class="fas fa-comments"></i> Communication Hub</h3>
                    <div class="communication-stats">
                        <div class="comm-item">
                            <i class="fas fa-envelope"></i>
                            <span class="number">24</span>
                            <span class="label">New Messages</span>
                        </div>
                        <div class="comm-item">
                            <i class="fas fa-bell"></i>
                            <span class="number">8</span>
                            <span class="label">Alerts</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities Card -->
                <div class="card">
                    <h3><i class="fas fa-clock"></i> Recent Activities</h3>
                    <ul class="activity-list">
                        <li>
                            <i class="fas fa-user-plus text-success"></i>
                            <span>New user registration approved</span>
                            <small>10 minutes ago</small>
                        </li>
                        <li>
                            <i class="fas fa-ship text-primary"></i>
                            <span>Vessel documentation updated</span>
                            <small>1 hour ago</small>
                        </li>
                        <li>
                            <i class="fas fa-file-alt text-info"></i>
                            <span>Compliance report generated</span>
                            <small>2 hours ago</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/admin_footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>