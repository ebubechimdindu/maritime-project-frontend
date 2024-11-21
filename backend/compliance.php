<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's vessels
$vessels_query = "SELECT * FROM vessels WHERE user_id = '$user_id'";
$vessels_result = $conn->query($vessels_query);

// Handle compliance form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vessel_id = $conn->real_escape_string($_POST['vessel_id']);
    $checks = [
        'safety_equipment_check',
        'navigation_systems_check',
        'crew_certification_check',
        'environmental_compliance',
        'hull_integrity_check',
        'firefighting_equipment',
        'medical_supplies_check',
        'radio_equipment_check',
        'waste_management_check',
        'security_systems_check'
    ];

    $check_values = [];
    foreach ($checks as $check) {
        $check_values[$check] = isset($_POST[$check]) ? 1 : 0;
    }

    // Check if compliance record exists
    $check_exists = "SELECT compliance_id FROM vessel_compliance WHERE vessel_id = '$vessel_id'";
    $result = $conn->query($check_exists);

    if ($result->num_rows > 0) {
        // Update existing record
        $updates = [];
        foreach ($check_values as $check => $value) {
            $updates[] = "$check = $value";
        }
        $updates[] = "last_checked = NOW()";
        $updates[] = "next_check_due = DATE_ADD(NOW(), INTERVAL 3 MONTH)";

        $sql = "UPDATE vessel_compliance SET " . implode(", ", $updates) .
            " WHERE vessel_id = '$vessel_id' AND user_id = '$user_id'";
    } else {
        // Insert new record
        $checks_sql = implode(", ", array_keys($check_values));
        $values_sql = implode(", ", $check_values);

        $sql = "INSERT INTO vessel_compliance (vessel_id, user_id, $checks_sql, last_checked, next_check_due) 
                VALUES ('$vessel_id', '$user_id', $values_sql, NOW(), DATE_ADD(NOW(), INTERVAL 3 MONTH))";
    }

    if ($conn->query($sql)) {
        $_SESSION['success_message'] = "Compliance checks updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating compliance checks: " . $conn->error;
    }
}

// Fetch selected vessel's compliance data if vessel is selected
$compliance_data = null;
if (isset($_GET['vessel_id'])) {
    $vessel_id = $conn->real_escape_string($_GET['vessel_id']);
    $compliance_query = "SELECT * FROM vessel_compliance WHERE vessel_id = '$vessel_id' AND user_id = '$user_id'";
    $compliance_result = $conn->query($compliance_query);
    if ($compliance_result->num_rows > 0) {
        $compliance_data = $compliance_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vessel Compliance - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Vessel Compliance Checks</h1>
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

                <!-- Vessel Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Select Vessel</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="mb-3">
                            <select class="form-select" name="vessel_id" onchange="this.form.submit()">
                                <option value="">Choose a vessel...</option>
                                <?php while ($vessel = $vessels_result->fetch_assoc()): ?>
                                    <option value="<?php echo $vessel['vessel_id']; ?>"
                                        <?php echo (isset($_GET['vessel_id']) && $_GET['vessel_id'] == $vessel['vessel_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vessel['vessel_name']); ?> (IMO: <?php echo htmlspecialchars($vessel['imo_number']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if (isset($_GET['vessel_id'])): ?>
                    <!-- Compliance Checklist -->
                    <div class="card">
                        <div class="card-header">
                            <h3>Compliance Checklist</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="vessel_id" value="<?php echo $_GET['vessel_id']; ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="safety_equipment_check"
                                                name="safety_equipment_check" <?php echo ($compliance_data && $compliance_data['safety_equipment_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="safety_equipment_check">Safety Equipment Check</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="navigation_systems_check"
                                                name="navigation_systems_check" <?php echo ($compliance_data && $compliance_data['navigation_systems_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="navigation_systems_check">Navigation Systems Check</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="crew_certification_check"
                                                name="crew_certification_check" <?php echo ($compliance_data && $compliance_data['crew_certification_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="crew_certification_check">Crew Certification Check</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="environmental_compliance"
                                                name="environmental_compliance" <?php echo ($compliance_data && $compliance_data['environmental_compliance']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="environmental_compliance">Environmental Compliance</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="hull_integrity_check"
                                                name="hull_integrity_check" <?php echo ($compliance_data && $compliance_data['hull_integrity_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="hull_integrity_check">Hull Integrity Check</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="firefighting_equipment"
                                                name="firefighting_equipment" <?php echo ($compliance_data && $compliance_data['firefighting_equipment']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="firefighting_equipment">Firefighting Equipment</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="medical_supplies_check"
                                                name="medical_supplies_check" <?php echo ($compliance_data && $compliance_data['medical_supplies_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="medical_supplies_check">Medical Supplies Check</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="radio_equipment_check"
                                                name="radio_equipment_check" <?php echo ($compliance_data && $compliance_data['radio_equipment_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="radio_equipment_check">Radio Equipment Check</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="waste_management_check"
                                                name="waste_management_check" <?php echo ($compliance_data && $compliance_data['waste_management_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="waste_management_check">Waste Management Check</label>
                                        </div>
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="security_systems_check"
                                                name="security_systems_check" <?php echo ($compliance_data && $compliance_data['security_systems_check']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="security_systems_check">Security Systems Check</label>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($compliance_data): ?>
                                    <div class="mb-3">
                                        <p><strong>Last Checked:</strong> <?php echo date('Y-m-d H:i', strtotime($compliance_data['last_checked'])); ?></p>
                                        <p><strong>Next Check Due:</strong> <?php echo date('Y-m-d H:i', strtotime($compliance_data['next_check_due'])); ?></p>
                                    </div>
                                <?php endif; ?>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Compliance Checks
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Compliance Status Overview -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3>Compliance Status Overview</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($compliance_data): ?>
                                <?php
                                $total_checks = 10;
                                $completed_checks = array_sum(array_map('intval', array_slice($compliance_data, 3, 10)));
                                $compliance_percentage = ($completed_checks / $total_checks) * 100;
                                $status_class = $compliance_percentage >= 80 ? 'success' : ($compliance_percentage >= 50 ? 'warning' : 'danger');
                                ?>
                                <div class="progress mb-3" style="height: 25px;">
                                    <div class="progress-bar bg-<?php echo $status_class; ?>"
                                        role="progressbar"
                                        style="width: <?php echo $compliance_percentage; ?>%"
                                        aria-valuenow="<?php echo $compliance_percentage; ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                        <?php echo round($compliance_percentage); ?>% Complete
                                    </div>
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    Compliance checks should be performed every 3 months to maintain vessel safety standards.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No compliance checks have been recorded for this vessel yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>