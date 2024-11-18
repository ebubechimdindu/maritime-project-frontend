<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$vessel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch vessel details
$sql = "SELECT * FROM vessels WHERE vessel_id = '$vessel_id' AND user_id = '$user_id'";
$result = $conn->query($sql);
$vessel = $result->fetch_assoc();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vessel_id = $_POST['vessel_id'];
    $vessel_name = $conn->real_escape_string($_POST['vessel_name'] ?? '');
    $imo_number = $conn->real_escape_string($_POST['imo_number'] ?? '');
    $vessel_type = $conn->real_escape_string($_POST['vessel_type'] ?? '');
    $flag_state = $conn->real_escape_string($_POST['flag_state'] ?? '');
    $gross_tonnage = !empty($_POST['gross_tonnage']) ? $conn->real_escape_string($_POST['gross_tonnage']) : NULL;
    $year_built = !empty($_POST['year_built']) ? $conn->real_escape_string($_POST['year_built']) : NULL;
    $classification_society = $conn->real_escape_string($_POST['classification_society'] ?? '');
    $status = $conn->real_escape_string($_POST['status'] ?? '');
    $location = $conn->real_escape_string($_POST['location'] ?? '');
    $departing_from = $conn->real_escape_string($_POST['departing_from'] ?? '');
    $arriving_at = $conn->real_escape_string($_POST['arriving_at'] ?? '');
    $departure_time = $conn->real_escape_string($_POST['departure_time'] ?? '');
    $estimated_arrival = $conn->real_escape_string($_POST['estimated_arrival'] ?? '');


    $sql = "UPDATE vessels SET 
    vessel_name = '$vessel_name',
    imo_number = '$imo_number',
    vessel_type = '$vessel_type',
    flag_state = '$flag_state',
    gross_tonnage = " . ($gross_tonnage ? "'$gross_tonnage'" : "NULL") . ",
    year_built = " . ($year_built ? "'$year_built'" : "NULL") . ",
    classification_society = '$classification_society',
    status = '$status',
    location = '$location',
    departing_from = '$departing_from',
    arriving_at = '$arriving_at',
    departure_time = '$departure_time',
    estimated_arrival = '$estimated_arrival'
    WHERE vessel_id = '$vessel_id' AND user_id = '$user_id'";


    if ($conn->query($sql)) {
        $_SESSION['success_message'] = "Vessel updated successfully!";
        header("Location: vessel_management.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error updating vessel: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vessel - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Edit Vessel</h1>
                <div class="user-info">
                    <span class="user-name">Hello <?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="container mt-4">
                <div class="card">
                    <div class="card-body">
                        <form id="editVesselForm" method="POST">
                            <input type="hidden" name="vessel_id" value="<?php echo $vessel['vessel_id']; ?>">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="vessel_name" class="form-label">Vessel Name</label>
                                    <input type="text" class="form-control" id="vessel_name" name="vessel_name" value="<?php echo htmlspecialchars($vessel['vessel_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="imo_number" class="form-label">IMO Number</label>
                                    <input type="text" class="form-control" id="imo_number" name="imo_number" value="<?php echo htmlspecialchars($vessel['imo_number']); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="vessel_type" class="form-label">Vessel Type</label>
                                    <select class="form-select" id="vessel_type" name="vessel_type" required>
                                        <option value="Cargo" <?php echo $vessel['vessel_type'] == 'Cargo' ? 'selected' : ''; ?>>Cargo</option>
                                        <option value="Tanker" <?php echo $vessel['vessel_type'] == 'Tanker' ? 'selected' : ''; ?>>Tanker</option>
                                        <option value="Passenger" <?php echo $vessel['vessel_type'] == 'Passenger' ? 'selected' : ''; ?>>Passenger</option>
                                        <option value="Fishing" <?php echo $vessel['vessel_type'] == 'Fishing' ? 'selected' : ''; ?>>Fishing</option>
                                        <option value="Other" <?php echo $vessel['vessel_type'] == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="flag_state" class="form-label">Flag State</label>
                                    <input type="text" class="form-control" id="flag_state" name="flag_state" value="<?php echo htmlspecialchars($vessel['flag_state']); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="gross_tonnage" class="form-label">Gross Tonnage</label>
                                    <input type="number" step="0.01" class="form-control" id="gross_tonnage" name="gross_tonnage" value="<?php echo htmlspecialchars($vessel['gross_tonnage']); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="year_built" class="form-label">Year Built</label>
                                    <input type="number" class="form-control" id="year_built" name="year_built" value="<?php echo htmlspecialchars($vessel['year_built']); ?>">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Current Location</label>
                                    <select class="form-select" id="location" name="location" required>
                                        <option value="in_port" <?php echo $vessel['location'] == 'in_port' ? 'selected' : ''; ?>>In Port</option>
                                        <option value="at_sea" <?php echo $vessel['location'] == 'at_sea' ? 'selected' : ''; ?>>At Sea</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="active" <?php echo $vessel['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="in_port" <?php echo $vessel['status'] == 'in_port' ? 'selected' : ''; ?>>In Port</option>
                                        <option value="maintenance" <?php echo $vessel['status'] == 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                        <option value="inactive" <?php echo $vessel['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="departing_from" class="form-label">Departing From</label>
                                    <input type="text" class="form-control" id="departing_from" name="departing_from" value="<?php echo htmlspecialchars($vessel['departing_from'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="arriving_at" class="form-label">Arriving At</label>
                                    <input type="text" class="form-control" id="arriving_at" name="arriving_at" value="<?php echo htmlspecialchars($vessel['arriving_at'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="departure_time" class="form-label">Departure Time</label>
                                    <input type="datetime-local" class="form-control" id="departure_time" name="departure_time" value="<?php echo date('Y-m-d\TH:i', strtotime($vessel['departure_time'] ?? 'now')); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="estimated_arrival" class="form-label">Estimated Arrival Time</label>
                                    <input type="datetime-local" class="form-control" id="estimated_arrival" name="estimated_arrival" value="<?php echo date('Y-m-d\TH:i', strtotime($vessel['estimated_arrival'] ?? 'now')); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="classification_society" class="form-label">Classification Society</label>
                                <input type="text" class="form-control" id="classification_society" name="classification_society" value="<?php echo htmlspecialchars($vessel['classification_society']); ?>">
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="vessel_management.php" class="btn btn-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to make changes to this vessel which is <strong><?php echo $vessel['status']; ?></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Confirm Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmEdit() {
            var modal = new bootstrap.Modal(document.getElementById('confirmEditModal'));
            modal.show();
        }

        function submitForm() {
            document.getElementById('editVesselForm').submit();
        }
    </script>
</body>

</html>