<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's vessels for the dropdown
$vessels_query = "SELECT vessel_id, vessel_name, imo_number FROM vessels WHERE user_id = '$user_id'";
$vessels_result = $conn->query($vessels_query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vessel_id = $conn->real_escape_string($_POST['vessel_id']);
    $incident_type = $conn->real_escape_string($_POST['incident_type']);
    $severity_level = $conn->real_escape_string($_POST['severity_level']);
    $location = $conn->real_escape_string($_POST['location']);
    $description = $conn->real_escape_string($_POST['description']);
    $date_time = $conn->real_escape_string($_POST['date_time']);
    
    // Handle file upload
    $attachments = [];
    if (!empty($_FILES['attachments']['name'][0])) {
        $upload_dir = '../uploads/incidents/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['attachments']['name'][$key];
            $file_path = $upload_dir . time() . '_' . $file_name;
            if (move_uploaded_file($tmp_name, $file_path)) {
                $attachments[] = $file_path;
            }
        }
    }
    
    $attachments_json = json_encode($attachments);
    
    $sql = "INSERT INTO incidents (user_id, vessel_id, incident_type, severity_level, location, description, date_time, status, attachments) 
            VALUES ('$user_id', '$vessel_id', '$incident_type', '$severity_level', '$location', '$description', '$date_time', 'reported', '$attachments_json')";
    
    if ($conn->query($sql)) {
        $_SESSION['success_message'] = "Incident reported successfully!";
        header("Location: incident_history.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error reporting incident: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Incident - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="main-content">
            <header class="top-bar">
                <h1>Report Maritime Incident</h1>
                <div class="user-info">
                    <span class="user-name"><?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="container mt-4">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="vessel_id" class="form-label">Select Vessel</label>
                                    <select class="form-select" name="vessel_id" required>
                                        <option value="">Choose vessel...</option>
                                        <?php while ($vessel = $vessels_result->fetch_assoc()): ?>
                                            <option value="<?php echo $vessel['vessel_id']; ?>">
                                                <?php echo htmlspecialchars($vessel['vessel_name']); ?> 
                                                (IMO: <?php echo htmlspecialchars($vessel['imo_number']); ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="incident_type" class="form-label">Incident Type</label>
                                    <select class="form-select" name="incident_type" required>
                                        <option value="">Select type...</option>
                                        <option value="collision">Collision</option>
                                        <option value="fire">Fire/Explosion</option>
                                        <option value="grounding">Grounding</option>
                                        <option value="equipment_failure">Equipment Failure</option>
                                        <option value="environmental">Environmental Incident</option>
                                        <option value="security">Security Incident</option>
                                        <option value="medical">Medical Emergency</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="severity_level" class="form-label">Severity Level</label>
                                    <select class="form-select" name="severity_level" required>
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                        <option value="critical">Critical</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="date_time" class="form-label">Date & Time of Incident</label>
                                    <input type="datetime-local" class="form-control" name="date_time" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" name="location" 
                                       placeholder="Coordinates or description of location" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Incident Description</label>
                                <textarea class="form-control" name="description" rows="5" 
                                          placeholder="Provide detailed description of the incident..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="attachments" class="form-label">Attachments</label>
                                <input type="file" class="form-control" name="attachments[]" multiple 
                                       accept="image/*,.pdf,.doc,.docx">
                                <small class="text-muted">Upload relevant photos or documents (Max 5 files)</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Incident Report
                                </button>
                                <a href="user_dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
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
</body>
</html>