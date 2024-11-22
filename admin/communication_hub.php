<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Handle broadcast actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send_broadcast'])) {
        $message = $_POST['message'];
        $type = $_POST['type'];
        $priority = $_POST['priority'];
        $target_audience = $_POST['target_audience'];
        
        $sql = "INSERT INTO broadcasts (message, type, priority, target_audience, sent_by) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $admin_id = $_SESSION['id'];
        $stmt->bind_param("ssssi", $message, $type, $priority, $target_audience, $admin_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Broadcast sent successfully";
        }
    }
}

// Fetch recent communications
$sql = "SELECT b.*, a.username as sender 
        FROM broadcasts b 
        JOIN admin a ON b.sent_by = a.id 
        ORDER BY b.created_at DESC 
        LIMIT 50";
$communications = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communication Hub - NautiGuard Admin</title>
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
                <h1><i class="fas fa-comments"></i> Communication Hub</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBroadcastModal">
                    <i class="fas fa-bullhorn"></i> New Broadcast
                </button>
            </div>

            <!-- Communication Stats -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Active Alerts</h5>
                            <h2><?php 
                                $active = $conn->query("SELECT COUNT(*) as count FROM broadcasts WHERE status = 'active'")->fetch_assoc();
                                echo $active['count'];
                            ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Safety Alerts</h5>
                            <h2><?php 
                                $safety = $conn->query("SELECT COUNT(*) as count FROM broadcasts WHERE type = 'safety' AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)")->fetch_assoc();
                                echo $safety['count'];
                            ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Messages Sent Today</h5>
                            <h2><?php 
                                $today = $conn->query("SELECT COUNT(*) as count FROM broadcasts WHERE DATE(created_at) = CURDATE()")->fetch_assoc();
                                echo $today['count'];
                            ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Recipients Reached</h5>
                            <h2>All Vessels</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Quick Actions</h5>
                            <div class="btn-group">
                                <button class="btn btn-danger" onclick="sendEmergencyBroadcast()">
                                    <i class="fas fa-exclamation-triangle"></i> Emergency Broadcast
                                </button>
                                <button class="btn btn-warning" onclick="sendWeatherAlert()">
                                    <i class="fas fa-cloud"></i> Weather Alert
                                </button>
                                <button class="btn btn-info" onclick="sendNavigationUpdate()">
                                    <i class="fas fa-compass"></i> Navigation Update
                                </button>
                                <button class="btn btn-success" onclick="sendSafetyReminder()">
                                    <i class="fas fa-shield-alt"></i> Safety Reminder
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Communication Log -->
            <div class="card">
                <div class="card-header">
                    <h3>Communication Log</h3>
                </div>
                <div class="card-body">
                    <table id="communicationTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Message</th>
                                <th>Priority</th>
                                <th>Audience</th>
                                <th>Sender</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($comm = $communications->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i', strtotime($comm['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getBroadcastTypeClass($comm['type']); ?>">
                                            <?php echo ucfirst($comm['type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(substr($comm['message'], 0, 50)) . '...'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo getPriorityClass($comm['priority']); ?>">
                                            <?php echo ucfirst($comm['priority']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $comm['target_audience'])); ?></td>
                                    <td><?php echo htmlspecialchars($comm['sender']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $comm['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($comm['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info" onclick="viewMessage(<?php echo $comm['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="resendMessage(<?php echo $comm['id']; ?>)">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                            <?php if ($comm['status'] == 'active'): ?>
                                                <button class="btn btn-sm btn-danger" onclick="deactivateMessage(<?php echo $comm['id']; ?>)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include communication modals -->
    <?php include('includes/communication_modals.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="js/communication_hub.js"></script>
</body>
</html>

<?php
function getBroadcastTypeClass($type) {
    $classes = [
        'emergency' => 'danger',
        'safety' => 'warning',
        'weather' => 'info',
        'navigation' => 'primary',
        'general' => 'secondary'
    ];
    return $classes[$type] ?? 'secondary';
}

function getPriorityClass($priority) {
    $classes = [
        'high' => 'danger',
        'medium' => 'warning',
        'low' => 'info'
    ];
    return $classes[$priority] ?? 'secondary';
}
?>