<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all incidents with vessel details
$sql = "SELECT i.*, v.vessel_name, v.imo_number 
        FROM incidents i 
        JOIN vessels v ON i.vessel_id = v.vessel_id 
        WHERE i.user_id = '$user_id' 
        ORDER BY i.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident History - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Incident History</h1>
                <div class="user-info">
                    <span class="user-name"><?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="container mt-4">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Vessel</th>
                                        <th>Type</th>
                                        <th>Location</th>
                                        <th>Severity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($incident = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date('Y-m-d H:i', strtotime($incident['date_time'])); ?></td>
                                            <td>
                                                <?php echo htmlspecialchars($incident['vessel_name']); ?>
                                                <br>
                                                <small class="text-muted">IMO: <?php echo htmlspecialchars($incident['imo_number']); ?></small>
                                            </td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $incident['incident_type'])); ?></td>
                                            <td><?php echo htmlspecialchars($incident['location']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        echo $incident['severity_level'] == 'critical' ? 'danger' : ($incident['severity_level'] == 'high' ? 'warning' : ($incident['severity_level'] == 'medium' ? 'info' : 'success'));
                                                                        ?>">
                                                    <?php echo ucfirst($incident['severity_level']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php
                                                                        echo $incident['status'] == 'resolved' ? 'success' : ($incident['status'] == 'investigating' ? 'warning' : ($incident['status'] == 'reported' ? 'info' : 'secondary'));
                                                                        ?>">
                                                    <?php echo ucfirst($incident['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="viewIncidentDetails(<?php echo $incident['incident_id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($incident['status'] !== 'resolved'): ?>
                                                    <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $incident['incident_id']; ?>, 'resolved')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($incident['attachments']): ?>
                                                    <button class="btn btn-sm btn-primary" onclick="viewAttachments(<?php echo $incident['incident_id']; ?>)">
                                                        <i class="fas fa-paperclip"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Incident Details Modal -->
    <div class="modal fade" id="incidentDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Incident Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="incidentDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Attachments Modal -->
    <div class="modal fade" id="attachmentsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Incident Attachments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="attachmentsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewIncidentDetails(incidentId) {
            fetch(`get_incident_details.php?id=${incidentId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('incidentDetailsContent').innerHTML = `
                        <div class="incident-details">
                            <h4>${data.vessel_name}</h4>
                            <p><strong>Type:</strong> ${data.incident_type}</p>
                            <p><strong>Location:</strong> ${data.location}</p>
                            <p><strong>Date/Time:</strong> ${data.date_time}</p>
                            <p><strong>Severity:</strong> ${data.severity_level}</p>
                            <p><strong>Description:</strong></p>
                            <p>${data.description}</p>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('incidentDetailsModal')).show();
                });
        }

        function viewAttachments(incidentId) {
            fetch(`get_incident_attachments.php?id=${incidentId}`)
                .then(response => response.json())
                .then(data => {
                    let attachmentsHtml = '<div class="list-group">';
                    data.attachments.forEach(attachment => {
                        attachmentsHtml += `
                            <a href="${attachment}" class="list-group-item list-group-item-action" target="_blank">
                                <i class="fas fa-file"></i> ${attachment.split('/').pop()}
                            </a>
                        `;
                    });
                    attachmentsHtml += '</div>';
                    document.getElementById('attachmentsContent').innerHTML = attachmentsHtml;
                    new bootstrap.Modal(document.getElementById('attachmentsModal')).show();
                });
        }

        function updateStatus(incidentId, status) {
            if (confirm('Are you sure you want to mark this incident as resolved?')) {
                fetch('update_incident_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `incident_id=${incidentId}&status=${status}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        }
    </script>
</body>

</html>