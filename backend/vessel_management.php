<?php
session_start();
require_once 'conn.php';
// Add this function after require_once 'conn.php';
function getStatusBadgeClass($status)
{
    $statusClasses = [
        'active' => 'success',
        'in_port' => 'info',
        'maintenance' => 'warning',
        'inactive' => 'secondary'
    ];
    return $statusClasses[$status] ?? 'primary';
}


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add new vessel
    $vessel_name = $conn->real_escape_string($_POST['vessel_name']);
    $imo_number = $conn->real_escape_string($_POST['imo_number']);
    $vessel_type = $conn->real_escape_string($_POST['vessel_type']);
    $flag_state = $conn->real_escape_string($_POST['flag_state']);
    $gross_tonnage = !empty($_POST['gross_tonnage']) ? $conn->real_escape_string($_POST['gross_tonnage']) : NULL;
    $year_built = !empty($_POST['year_built']) ? $conn->real_escape_string($_POST['year_built']) : NULL;
    $classification_society = !empty($_POST['classification_society']) ? $conn->real_escape_string($_POST['classification_society']) : NULL;
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "INSERT INTO vessels (user_id, vessel_name, imo_number, vessel_type, flag_state, gross_tonnage, year_built, classification_society, status) 
            VALUES ('$user_id', '$vessel_name', '$imo_number', '$vessel_type', '$flag_state', '$gross_tonnage', '$year_built', '$classification_society', '$status')";

    if ($conn->query($sql)) {
        $_SESSION['success_message'] = "Vessel added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding vessel: " . $conn->error;
    }

    header("Location: vessel_management.php");
    exit();
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $vessel_id = $conn->real_escape_string($_GET['id']);

    $sql = "DELETE FROM vessels WHERE vessel_id = '$vessel_id' AND user_id = '$user_id'";

    if ($conn->query($sql)) {
        $_SESSION['success_message'] = "Vessel deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting vessel: " . $conn->error;
    }

    header("Location: vessel_management.php");
    exit();
}

// Fetch all vessels for the current user
$sql = "SELECT * FROM vessels WHERE user_id = '$user_id' ORDER BY registration_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vessel Management - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>

<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Vessel Management</h1>
                <div class="user-info">
                    <span class="user-name">Hello <?php echo $_SESSION['full_name']; ?></span>
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

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="mb-4">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVesselModal">
                        <i class="fas fa-plus"></i> Add New Vessel
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Vessel Name</th>
                                        <th>IMO Number</th>
                                        <th>Type</th>
                                        <th>Flag State</th>
                                        <th>Status</th>
                                        <th>Last Inspection</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($vessel = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($vessel['vessel_name']); ?></td>
                                            <td><?php echo htmlspecialchars($vessel['imo_number']); ?></td>
                                            <td><?php echo htmlspecialchars($vessel['vessel_type']); ?></td>
                                            <td><?php echo htmlspecialchars($vessel['flag_state']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getStatusBadgeClass($vessel['status']); ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $vessel['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $vessel['last_inspection_date']; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="editVessel(<?php echo $vessel['vessel_id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" onclick="deleteVessel(<?php echo $vessel['vessel_id']; ?>)">
                                                    <i class="fas fa-trash"></i>
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
        </main>
    </div>

    <!-- Add Vessel Modal -->
    <div class="modal fade" id="addVesselModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Vessel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="vessel_management.php" method="POST" id="addVesselForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vessel_name" class="form-label">Vessel Name</label>
                                <input type="text" class="form-control" id="vessel_name" name="vessel_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="imo_number" class="form-label">IMO Number</label>
                                <input type="text" class="form-control" id="imo_number" name="imo_number" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vessel_type" class="form-label">Vessel Type</label>
                                <select class="form-select" id="vessel_type" name="vessel_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Cargo">Cargo</option>
                                    <option value="Tanker">Tanker</option>
                                    <option value="Passenger">Passenger</option>
                                    <option value="Fishing">Fishing</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="flag_state" class="form-label">Flag State</label>
                                <input type="text" class="form-control" id="flag_state" name="flag_state" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gross_tonnage" class="form-label">Gross Tonnage</label>
                                <input type="number" step="0.01" class="form-control" id="gross_tonnage" name="gross_tonnage">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="year_built" class="form-label">Year Built</label>
                                <input type="number" class="form-control" id="year_built" name="year_built">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="classification_society" class="form-label">Classification Society</label>
                            <input type="text" class="form-control" id="classification_society" name="classification_society">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="in_port">In Port</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Vessel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
    <script>
        function getStatusBadgeClass(status) {
            const statusClasses = {
                'active': 'success',
                'in_port': 'info',
                'maintenance': 'warning',
                'inactive': 'secondary'
            };
            return statusClasses[status] || 'primary';
        }

        function editVessel(vesselId) {
            window.location.href = `edit_vessel.php?id=${vesselId}`;
        }

        function deleteVessel(vesselId) {
            if (confirm('Are you sure you want to delete this vessel?')) {
                window.location.href = `vessel_management.php?action=delete&id=${vesselId}`;
            }
        }
    </script>
</body>

</html>