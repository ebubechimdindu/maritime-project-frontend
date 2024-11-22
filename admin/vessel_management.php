<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Handle vessel actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve_vessel'])) {
        $vessel_id = $_POST['vessel_id'];
        $sql = "UPDATE vessels SET 
                approval_status = 'approved', 
                approval_date = NOW() 
                WHERE vessel_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $vessel_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Vessel approved successfully";
        }
    }

    if (isset($_POST['reject_vessel'])) {
        $vessel_id = $_POST['vessel_id'];
        $notes = $_POST['rejection_notes'];
        $sql = "UPDATE vessels SET 
                approval_status = 'rejected', 
                approval_date = NOW(),
                approval_notes = ? 
                WHERE vessel_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $notes, $vessel_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Vessel rejected";
        }
    }

    if (isset($_POST['suspend_vessel'])) {
        $vessel_id = $_POST['vessel_id'];
        $sql = "UPDATE vessels SET status = 'suspended' WHERE vessel_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $vessel_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Vessel operations suspended";
        }
    }
    if (isset($_POST['unsuspend_vessel'])) {
        $vessel_id = $_POST['vessel_id'];
        $sql = "UPDATE vessels SET status = 'active' WHERE vessel_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $vessel_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Vessel operations reactivated successfully";
        }
    }
}

// Fetch all vessels with user and compliance information
$sql = "SELECT v.*, u.username, u.full_name,
        (SELECT COUNT(*) FROM vessel_compliance vc 
         WHERE vc.vessel_id = v.vessel_id AND 
         (vc.safety_equipment_check = 1 OR 
          vc.navigation_systems_check = 1 OR 
          vc.crew_certification_check = 1)) as compliance_count
        FROM vessels v
        JOIN users u ON v.user_id = u.user_id
        ORDER BY v.registration_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vessel Management - NautiGuard Admin</title>
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
                <h1><i class="fas fa-ship"></i> Vessel Management</h1>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Vessel Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Vessels</h5>
                            <h2><?php echo $result->num_rows; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Pending Approval</h5>
                            <h2><?php
                                $pending = $conn->query("SELECT COUNT(*) as count FROM vessels WHERE approval_status = 'pending'")->fetch_assoc();
                                echo $pending['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Active Vessels</h5>
                            <h2><?php
                                $active = $conn->query("SELECT COUNT(*) as count FROM vessels WHERE status = 'active'")->fetch_assoc();
                                echo $active['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Suspended</h5>
                            <h2><?php
                                $suspended = $conn->query("SELECT COUNT(*) as count FROM vessels WHERE status = 'suspended'")->fetch_assoc();
                                echo $suspended['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Vessels Table -->
            <div class="card">
                <div class="card-body">
                    <table id="vesselsTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vessel Name</th>
                                <th>IMO Number</th>
                                <th>Owner</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Approval Status</th>
                                <th>Compliance</th>
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($vessel = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vessel['vessel_name']); ?></td>
                                    <td><?php echo htmlspecialchars($vessel['imo_number']); ?></td>
                                    <td><?php echo htmlspecialchars($vessel['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($vessel['vessel_type']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php
                                                                echo $vessel['status'] == 'active' ? 'success' : ($vessel['status'] == 'suspended' ? 'danger' : 'warning');
                                                                ?>">
                                            <?php echo ucfirst($vessel['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php
                                                                echo $vessel['approval_status'] == 'approved' ? 'success' : ($vessel['approval_status'] == 'rejected' ? 'danger' : 'warning');
                                                                ?>">
                                            <?php echo ucfirst($vessel['approval_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: <?php echo ($vessel['compliance_count'] / 10) * 100; ?>%">
                                                <?php echo $vessel['compliance_count']; ?>/10
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo $vessel['location'] == 'at_sea' ? 'At Sea' : 'In Port'; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info" onclick="viewVesselDetails(<?php echo $vessel['vessel_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="vessel_inspection.php?id=<?php echo $vessel['vessel_id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-clipboard-check"></i>
                                            </a>
                                            <?php if ($vessel['status'] == 'suspended'): ?>
                                                <button class="btn btn-sm btn-success" onclick="unsuspendVessel(<?php echo $vessel['vessel_id']; ?>)">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-warning" onclick="suspendVessel(<?php echo $vessel['vessel_id']; ?>)">
                                                    <i class="fas fa-pause"></i>
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
    <!-- Vessel Details Modal -->
    <div class="modal fade" id="vesselDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-ship"></i> Vessel Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="vesselDetailsContent">
                        <div class="vessel-details">
                            <div class="vessel-header mb-4">
                                <h4 class="vessel-name"></h4>
                                <span class="badge status-badge"></span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-section">
                                        <h5 class="section-title">Basic Information</h5>
                                        <div class="info-group">
                                            <p><strong>IMO Number:</strong> <span class="imo-number"></span></p>
                                            <p><strong>Vessel Type:</strong> <span class="vessel-type"></span></p>
                                            <p><strong>Flag State:</strong> <span class="flag-state"></span></p>
                                            <p><strong>Gross Tonnage:</strong> <span class="gross-tonnage"></span></p>
                                            <p><strong>Year Built:</strong> <span class="year-built"></span></p>
                                            <p><strong>Classification Society:</strong> <span class="classification-society"></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-section">
                                        <h5 class="section-title">Registration & Inspection</h5>
                                        <div class="info-group">
                                            <p><strong>Registration Date:</strong> <span class="registration-date"></span></p>
                                            <p><strong>Last Inspection:</strong> <span class="last-inspection"></span></p>
                                            <p><strong>Inspection Rating:</strong> <span class="inspection-rating"></span></p>
                                            <p><strong>Approval Status:</strong> <span class="approval-status"></span></p>
                                            <p><strong>Approval Date:</strong> <span class="approval-date"></span></p>
                                            <p><strong>Inspection Notes:</strong> <span class="inspection-notes"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="info-section">
                                        <h5 class="section-title">Current Journey Information</h5>
                                        <div class="info-group">
                                            <div class="journey-status p-3 mb-3 bg-light rounded">
                                                <p><strong>Current Location:</strong> <span class="current-location"></span></p>
                                                <p><strong>Departing From:</strong> <span class="departing-from"></span></p>
                                                <p><strong>Departure Time:</strong> <span class="departure-time"></span></p>
                                                <p><strong>Arriving At:</strong> <span class="arriving-at"></span></p>
                                                <p><strong>Estimated Arrival:</strong> <span class="estimated-arrival"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="info-section">
                                        <h5 class="section-title">Compliance Status</h5>
                                        <div class="compliance-checks p-3 bg-light rounded">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><i class="fas fa-check-circle text-success"></i> Safety Equipment</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Navigation Systems</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Crew Certification</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Environmental Compliance</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Hull Integrity</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><i class="fas fa-check-circle text-success"></i> Firefighting Equipment</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Medical Supplies</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Radio Equipment</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Waste Management</p>
                                                    <p><i class="fas fa-check-circle text-success"></i> Security Systems</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printVesselDetails()">
                        <i class="fas fa-print"></i> Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Vessel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="vessel_id" id="rejection_vessel_id">
                        <div class="mb-3">
                            <label for="rejection_notes" class="form-label">Rejection Reason</label>
                            <textarea class="form-control" name="rejection_notes" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="reject_vessel" class="btn btn-danger">Reject</button>
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
            $('#vesselsTable').DataTable();
        });

        function viewVesselDetails(vesselId) {
            fetch(`get_vessel_details.php?vessel_id=${vesselId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('vesselDetailsContent').innerHTML = `
                        <div class="vessel-details">
                            <h4>${data.vessel_name}</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>IMO Number:</strong> ${data.imo_number}</p>
                                    <p><strong>Type:</strong> ${data.vessel_type}</p>
                                    <p><strong>Flag State:</strong> ${data.flag_state}</p>
                                    <p><strong>Gross Tonnage:</strong> ${data.gross_tonnage}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Year Built:</strong> ${data.year_built}</p>
                                    <p><strong>Classification Society:</strong> ${data.classification_society}</p>
                                    <p><strong>Last Inspection:</strong> ${data.last_inspection}</p>
                                    <p><strong>Registration Date:</strong> ${data.registration_date}</p>
                                </div>
                            </div>
                            <h5>Current Journey</h5>
                            <p><strong>Departing From:</strong> ${data.departing_from}</p>
                            <p><strong>Arriving At:</strong> ${data.arriving_at}</p>
                            <p><strong>Estimated Arrival:</strong> ${data.estimated_arrival}</p>
                        </div>
                    `;
                    new bootstrap.Modal(document.getElementById('vesselDetailsModal')).show();
                });
        }

        function approveVessel(vesselId) {
            if (confirm('Are you sure you want to approve this vessel?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                                        <input type="hidden" name="vessel_id" value="${vesselId}">
                    <input type="hidden" name="approve_vessel" value="1">
                `;
                document.body.append(form);
                form.submit();
            }
        }

        function rejectVessel(vesselId) {
            document.getElementById('rejection_vessel_id').value = vesselId;
            new bootstrap.Modal(document.getElementById('rejectionModal')).show();
        }

        function suspendVessel(vesselId) {
            if (confirm('Are you sure you want to suspend this vessel\'s operations?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="vessel_id" value="${vesselId}">
                    <input type="hidden" name="suspend_vessel" value="1">
                `;
                document.body.append(form);
                form.submit();
            }
        }
        function unsuspendVessel(vesselId) {
    if (confirm('Are you sure you want to reactivate this vessel\'s operations?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="vessel_id" value="${vesselId}">
            <input type="hidden" name="unsuspend_vessel" value="1">
        `;
        document.body.append(form);
        form.submit();
    }
}

    </script>
</body>

</html>