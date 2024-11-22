<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Handle compliance actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['issue_warning'])) {
        $compliance_id = $_POST['compliance_id'];
        $warning_message = $_POST['warning_message'];
        $deadline_date = $_POST['deadline_date'];

        $sql = "UPDATE vessel_compliance 
                SET compliance_status = 'warning',
                    warning_message = ?,
                    deadline_date = ?
                WHERE compliance_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $warning_message, $deadline_date, $compliance_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Compliance warning issued successfully";
        }
    }

    if (isset($_POST['override_status'])) {
        $compliance_id = $_POST['compliance_id'];
        $new_status = $_POST['new_status'];
        $admin_notes = $_POST['admin_notes'];

        $sql = "UPDATE vessel_compliance 
        SET compliance_status = ?,
            admin_notes = ?,
            override_by = ?,
            override_date = NOW()
        WHERE compliance_id = ?";
        $stmt = $conn->prepare($sql);
        $admin_id = $_SESSION['id'];
        $stmt->bind_param("ssii", $new_status, $admin_notes, $admin_id, $compliance_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Compliance status overridden successfully";
        }
    }
}

// Fetch all compliance records with vessel and user details
$sql = "SELECT vc.*, v.vessel_name, v.imo_number, u.full_name as owner_name,
        (
            safety_equipment_check + navigation_systems_check + 
            crew_certification_check + environmental_compliance + 
            hull_integrity_check + firefighting_equipment + 
            medical_supplies_check + radio_equipment_check + 
            waste_management_check + security_systems_check
        ) as compliance_score
        FROM vessel_compliance vc
        JOIN vessels v ON vc.vessel_id = v.vessel_id
        JOIN users u ON vc.user_id = u.user_id
        ORDER BY compliance_score DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Oversight - NautiGuard Admin</title>
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
                <h1><i class="fas fa-clipboard-check"></i> Compliance Oversight</h1>
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

            <!-- Compliance Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Compliant Vessels</h5>
                            <h2><?php
                                $compliant = $conn->query("SELECT COUNT(*) as count FROM vessel_compliance WHERE compliance_status = 'compliant'")->fetch_assoc();
                                echo $compliant['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Warnings Issued</h5>
                            <h2><?php
                                $warnings = $conn->query("SELECT COUNT(*) as count FROM vessel_compliance WHERE compliance_status = 'warning'")->fetch_assoc();
                                echo $warnings['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Non-Compliant</h5>
                            <h2><?php
                                $non_compliant = $conn->query("SELECT COUNT(*) as count FROM vessel_compliance WHERE compliance_status = 'non_compliant'")->fetch_assoc();
                                echo $non_compliant['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Due for Check</h5>
                            <h2><?php
                                $due = $conn->query("SELECT COUNT(*) as count FROM vessel_compliance WHERE next_check_due <= NOW()")->fetch_assoc();
                                echo $due['count'];
                                ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compliance Table -->
            <div class="card">
                <div class="card-body">
                    <table id="complianceTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Vessel</th>
                                <th>Owner</th>
                                <th>Compliance Score</th>
                                <th>Status</th>
                                <th>Last Checked</th>
                                <th>Next Due</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($record = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($record['vessel_name']); ?>
                                        <br>
                                        <small class="text-muted">IMO: <?php echo htmlspecialchars($record['imo_number']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($record['owner_name']); ?></td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-<?php
                                                                        echo $record['compliance_score'] >= 8 ? 'success' : ($record['compliance_score'] >= 6 ? 'warning' : 'danger');
                                                                        ?>"
                                                role="progressbar"
                                                style="width: <?php echo ($record['compliance_score'] / 10) * 100; ?>%">
                                                <?php echo $record['compliance_score']; ?>/10
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php
                                                                echo $record['compliance_status'] == 'compliant' ? 'success' : ($record['compliance_status'] == 'warning' ? 'warning' : 'danger');
                                                                ?>">
                                            <?php echo ucfirst($record['compliance_status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d', strtotime($record['last_checked'])); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($record['next_check_due'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-info" onclick="viewCompliance(<?php echo $record['compliance_id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="issueWarning(<?php echo $record['compliance_id']; ?>)">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="overrideStatus(<?php echo $record['compliance_id']; ?>)">
                                                <i class="fas fa-edit"></i>
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
    </div>

    <!-- Include modals and JavaScript -->
    <?php include('includes/compliance_modals.php'); ?>
    <?php include('includes/admin_footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="../js/compliance_oversight.js"></script>
</body>

</html>