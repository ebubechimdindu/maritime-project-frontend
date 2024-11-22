<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Fetch active emergencies
$sql = "SELECT i.*, v.vessel_name, v.imo_number, u.full_name as reporter_name,
        GROUP_CONCAT(t.team_name) as assigned_teams
        FROM incidents i 
        JOIN vessels v ON i.vessel_id = v.vessel_id
        JOIN users u ON i.user_id = u.user_id
        LEFT JOIN incident_assignments ia ON i.incident_id = ia.incident_id
        LEFT JOIN response_teams t ON ia.team_id = t.team_id
        WHERE i.severity_level IN ('critical', 'high')
        AND i.status != 'closed'
        GROUP BY i.incident_id
        ORDER BY i.created_at DESC";
$emergencies = $conn->query($sql);

// Fetch response teams status
$teams_sql = "SELECT * FROM response_teams WHERE status = 'active'";
$teams = $conn->query($teams_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Response - NautiGuard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include('includes/admin_sidebar.php'); ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-ambulance"></i> Emergency Response</h1>
                <div class="alert-controls">
                    <button class="btn btn-danger" onclick="soundAlarm()">
                        <i class="fas fa-bell"></i> Sound Emergency Alarm
                    </button>
                </div>
            </div>

            <!-- Emergency Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Active Emergencies</h5>
                            <h2><?php echo $emergencies->num_rows; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Teams Deployed</h5>
                            <h2><?php 
                                $deployed = $conn->query("SELECT COUNT(DISTINCT team_id) as count FROM incident_assignments WHERE status = 'responding'")->fetch_assoc();
                                echo $deployed['count'];
                            ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Available Teams</h5>
                            <h2><?php echo $teams->num_rows; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Avg Response Time</h5>
                            <h2>15 min</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Emergencies -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h3><i class="fas fa-exclamation-circle"></i> Active Emergencies</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="emergenciesTable">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Vessel</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Severity</th>
                                    <th>Teams Assigned</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($emergency = $emergencies->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('H:i', strtotime($emergency['created_at'])); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($emergency['vessel_name']); ?>
                                            <br>
                                            <small class="text-muted">IMO: <?php echo htmlspecialchars($emergency['imo_number']); ?></small>
                                        </td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $emergency['incident_type'])); ?></td>
                                        <td><?php echo htmlspecialchars($emergency['location']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $emergency['severity_level'] == 'critical' ? 'danger' : 'warning'; ?>">
                                                <?php echo ucfirst($emergency['severity_level']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $emergency['assigned_teams'] ?? 'None'; ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $emergency['status'] == 'resolved' ? 'success' : 
                                                    ($emergency['status'] == 'investigating' ? 'warning' : 'info'); 
                                            ?>">
                                                <?php echo ucfirst($emergency['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-info" onclick="viewEmergencyDetails(<?php echo $emergency['incident_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-primary" onclick="deployTeam(<?php echo $emergency['incident_id']; ?>)">
                                                    <i class="fas fa-users"></i>
                                                </button>
                                                <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $emergency['incident_id']; ?>)">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Emergency Contacts Directory -->
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-phone"></i> Emergency Contacts</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5>Medical Services</h5>
                            <ul class="list-unstyled">
                                <li><strong>Maritime Medical:</strong> +1 (555) 777-6666</li>
                                <li><strong>Emergency Medical:</strong> +1 (555) 999-8888</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5>Fire & Rescue</h5>
                            <ul class="list-unstyled">
                                <li><strong>Fire Response:</strong> +1 (555) 444-3333</li>
                                <li><strong>Coast Guard:</strong> +1 (555) 222-1111</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5>Security Services</h5>
                            <ul class="list-unstyled">
                                <li><strong>Maritime Police:</strong> +1 (555) 111-0000</li>
                                <li><strong>Port Security:</strong> +1 (555) 333-2222</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include emergency response modals -->
    <?php include('includes/emergency_modals.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="../js/emergency_response.js"></script>
</body>
</html>