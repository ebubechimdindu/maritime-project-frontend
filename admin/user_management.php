<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Add status column to users table if it doesn't exist
$check_status_column = "SHOW COLUMNS FROM users LIKE 'status'";
$result = $conn->query($check_status_column);
if ($result->num_rows == 0) {
    $alter_table = "ALTER TABLE users ADD COLUMN status ENUM('active', 'disabled') DEFAULT 'active'";
    $conn->query($alter_table);
}

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_password'])) {
        $user_id = $_POST['user_id'];
        $new_password = $_POST['new_password'];
        
        $sql = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_password, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Password updated successfully";
        } else {
            $_SESSION['error_message'] = "Error updating password";
        }
    }

    if (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        $new_status = $_POST['new_status'];
        
        $sql = "UPDATE users SET status = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User status updated successfully";
        } else {
            $_SESSION['error_message'] = "Error updating user status";
        }
    }
}

// Fetch users with their vessel counts and incident counts
$sql = "SELECT u.*, 
        (SELECT COUNT(*) FROM vessels v WHERE v.user_id = u.user_id) as vessel_count,
        (SELECT COUNT(*) FROM incidents i WHERE i.user_id = u.user_id) as incident_count
        FROM users u ORDER BY u.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - NautiGuard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables@1.10.18/media/css/jquery.dataTables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include('includes/admin_sidebar.php'); ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-users"></i> User Management</h1>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="usersTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Vessels</th>
                                    <th>Incidents</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $user['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo $user['vessel_count']; ?> vessels
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">
                                                <?php echo $user['incident_count']; ?> incidents
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($user['status'] ?? 'active'); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" onclick="showPasswordModal(<?php echo $user['user_id']; ?>)">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button class="btn btn-sm btn-<?php echo $user['status'] == 'active' ? 'danger' : 'success'; ?>"
                                                    onclick="toggleUserStatus(<?php echo $user['user_id']; ?>, '<?php echo $user['status'] == 'active' ? 'disabled' : 'active'; ?>')">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                            <button class="btn btn-sm btn-info" onclick="viewUserDetails(<?php echo $user['user_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Update Modal -->
    <div class="modal fade" id="passwordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update User Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="passwordUpdateForm" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="user_id" id="passwordUserId">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="userDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/admin_footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                order: [[0, 'desc']]
            });
        });

        function showPasswordModal(userId) {
            document.getElementById('passwordUserId').value = userId;
            new bootstrap.Modal(document.getElementById('passwordModal')).show();
        }

        function toggleUserStatus(userId, newStatus) {
            if (confirm('Are you sure you want to ' + (newStatus === 'active' ? 'enable' : 'disable') + ' this user?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="user_id" value="${userId}">
                    <input type="hidden" name="new_status" value="${newStatus}">
                    <input type="hidden" name="toggle_status" value="1">
                `;
                document.body.append(form);
                form.submit();
            }
        }

        function viewUserDetails(userId) {
            fetch(`get_user_details.php?user_id=${userId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('userDetailsContent').innerHTML = `
                        <div class="user-details">
                            <h4>${data.full_name}</h4>
                            <p><strong>Email:</strong> ${data.email}</p>
                            <p><strong>Username:</strong> ${data.username}</p>
                            <p><strong>Gender:</strong> ${data.gender}</p>
                            <p><strong>Joined:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            
                            <h5>Registered Vessels</h5>
                            <ul>
                                ${data.vessels.map(vessel => `
                                    <li>${vessel.vessel_name} (IMO: ${vessel.imo_number})</li>
                                `).join('')}
                            </ul>
                            
                            <h5>Recent Incidents</h5>
                            <ul>
                                ${data.incidents.map(incident => `
                                    <li>${incident.incident_type} - ${new Date(incident.date_time).toLocaleDateString()}</li>
                                `).join('')}
                            </ul>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('userDetailsModal')).show();
                });
        }
    </script>
</body>
</html>