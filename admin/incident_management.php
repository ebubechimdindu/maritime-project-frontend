<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Handle incident actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_status'])) {
        $incident_id = $_POST['incident_id'];
        $new_status = $_POST['new_status'];
        $sql = "UPDATE incidents SET status = ? WHERE incident_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $incident_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Incident status updated successfully";
        }
    }

    if (isset($_POST['assign_team'])) {
        $incident_id = $_POST['incident_id'];
        $team_id = $_POST['team_id'];
        $sql = "INSERT INTO incident_assignments (incident_id, team_id, assigned_at) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $incident_id, $team_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Response team assigned successfully";
        }
    }
}

// Fetch all incidents with vessel and user details
$sql = "SELECT i.*, v.vessel_name, u.full_name as reporter_name,
        GROUP_CONCAT(t.team_name) as assigned_teams
        FROM incidents i 
        JOIN vessels v ON i.vessel_id = v.vessel_id
        JOIN users u ON i.user_id = u.user_id
        LEFT JOIN incident_assignments ia ON i.incident_id = ia.incident_id
        LEFT JOIN response_teams t ON ia.team_id = t.team_id
        GROUP BY i.incident_id
        ORDER BY i.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Management - NautiGuard Admin</title>
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
                <h1><i class="fas fa-exclamation-triangle"></i> Incident Management</h1>
            </div>

            <!-- Success Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Incident Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Critical Incidents</h5>
                            <h2><?php
                                $critical = $conn->query("SELECT COUNT(*) as count FROM incidents WHERE severity_level = 'critical' AND status != 'closed'")->fetch_assoc();
                                echo $critical['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Active Incidents</h5>
                            <h2><?php
                                $active = $conn->query("SELECT COUNT(*) as count FROM incidents WHERE status IN ('reported', 'investigating')")->fetch_assoc();
                                echo $active['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Resolved Today</h5>
                            <h2><?php
                                $resolved = $conn->query("SELECT COUNT(*) as count FROM incidents WHERE status = 'resolved' AND DATE(updated_at) = CURDATE()")->fetch_assoc();
                                echo $resolved['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Total Incidents</h5>
                            <h2><?php echo $result->num_rows; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incidents Table -->
            <div class="card">
                <div class="card-body">
                    <table id="incidentsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date/Time</th>
                                <th>Vessel</th>
                                <th>Type</th>
                                <th>Severity</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Teams Assigned</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($incident = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $incident['incident_id']; ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($incident['date_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($incident['vessel_name']); ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $incident['incident_type'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php
                                                                echo $incident['severity_level'] == 'critical' ? 'danger' : ($incident['severity_level'] == 'high' ? 'warning' : ($incident['severity_level'] == 'medium' ? 'info' : 'success'));
                                                                ?>">
                                            <?php echo ucfirst($incident['severity_level']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($incident['location']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php
                                                                echo $incident['status'] == 'resolved' ? 'success' : ($incident['status'] == 'investigating' ? 'warning' : ($incident['status'] == 'reported' ? 'info' : 'secondary'));
                                                                ?>">
                                            <?php echo ucfirst($incident['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $incident['assigned_teams'] ?? 'None'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info" onclick="viewIncidentDetails(<?php echo $incident['incident_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="assignTeam(<?php echo $incident['incident_id']; ?>)">
                                                <i class="fas fa-users"></i>
                                            </button>
                                            <?php if ($incident['status'] != 'resolved' && $incident['status'] != 'closed'): ?>
                                                <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $incident['incident_id']; ?>, 'resolved')">
                                                    <i class="fas fa-check"></i>
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

    <!-- Incident Details Modal -->
    <div class="modal fade" id="incidentDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-circle"></i> Incident Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="incidentDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Team Assignment Modal -->
    <div class="modal fade" id="teamAssignmentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Response Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="incident_id" id="assignment_incident_id">
                        <div class="mb-3">
                            <label class="form-label">Select Team</label>
                            <select name="team_id" class="form-select" required>
                                <option value="">Choose team...</option>
                                <option value="1">Fire Service Team</option>
                                <option value="2">Maritime Police</option>
                                <option value="3">First Responders</option>
                                <option value="4">Environmental Response Team</option>
                                <option value="5">Medical Emergency Team</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="assign_team" class="btn btn-primary">Assign Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include('includes/admin_footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#incidentsTable').DataTable({
                order: [
                    [0, 'desc']
                ]
            });
        });

        function viewIncidentDetails(incidentId) {
            fetch(`get_incident_details.php?id=${incidentId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('incidentDetailsContent').innerHTML = `
                        <div class="incident-details">
                                                       <div class="row">
                                <div class="col-md-6">
                                    <h5>Basic Information</h5>
                                    <p><strong>Reporter:</strong> ${data.reporter_name}</p>
                                    <p><strong>Vessel:</strong> ${data.vessel_name}</p>
                                    <p><strong>Type:</strong> ${data.incident_type}</p>
                                    <p><strong>Severity:</strong> <span class="badge bg-${getSeverityClass(data.severity_level)}">${data.severity_level}</span></p>
                                    <p><strong>Status:</strong> <span class="badge bg-${getStatusClass(data.status)}">${data.status}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <h5>Location & Time</h5>
                                    <p><strong>Location:</strong> ${data.location}</p>
                                    <p><strong>Date/Time:</strong> ${new Date(data.date_time).toLocaleString()}</p>
                                    <p><strong>Reported:</strong> ${new Date(data.created_at).toLocaleString()}</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h5>Description</h5>
                                <p>${data.description}</p>
                            </div>
                            ${data.attachments ? `
                                <div class="mt-4">
                                    <h5>Attachments</h5>
                                    <div class="attachment-list">
                                        ${JSON.parse(data.attachments).map(att => `
                                            <a href="${att}" target="_blank" class="btn btn-sm btn-outline-primary m-1">
                                                <i class="fas fa-paperclip"></i> View Attachment
                                            </a>
                                        `).join('')}
                                    </div>
                                </div>
                            ` : ''}
                            <div class="mt-4">
                                <h5>Response Teams</h5>
                                <p>${data.assigned_teams || 'No teams assigned yet'}</p>
                            </div>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('incidentDetailsModal')).show();
                });
        }

        function assignTeam(incidentId) {
            document.getElementById('assignment_incident_id').value = incidentId;
            new bootstrap.Modal(document.getElementById('teamAssignmentModal')).show();
        }

        function updateStatus(incidentId, status) {
            if (confirm('Are you sure you want to update this incident\'s status?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="incident_id" value="${incidentId}">
                    <input type="hidden" name="new_status" value="${status}">
                    <input type="hidden" name="update_status" value="1">
                `;
                document.body.append(form);
                form.submit();
            }
        }

        function getSeverityClass(severity) {
            const classes = {
                'critical': 'danger',
                'high': 'warning',
                'medium': 'info',
                'low': 'success'
            };
            return classes[severity] || 'secondary';
        }

        function getStatusClass(status) {
            const classes = {
                'resolved': 'success',
                'investigating': 'warning',
                'reported': 'info',
                'closed': 'secondary'
            };
            return classes[status] || 'secondary';
        }
    </script>
</body>

</html>