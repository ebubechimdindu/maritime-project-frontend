<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vessel_id = $_POST['vessel_id'];
    $inspection_notes = $_POST['inspection_notes'];
    $inspection_rating = $_POST['inspection_rating'];
    
    $sql = "UPDATE vessels SET 
            last_inspection_date = NOW(),
            approval_notes = ?
            WHERE vessel_id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $inspection_notes, $vessel_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Inspection completed successfully";
    }
    
    header('Location: vessel_management.php');
    exit();
}

$vessel_id = $_GET['id'] ?? null;
if (!$vessel_id) {
    header('Location: vessel_management.php');
    exit();
}

$sql = "SELECT v.*, u.full_name FROM vessels v 
        JOIN users u ON v.user_id = u.user_id 
        WHERE v.vessel_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $vessel_id);
$stmt->execute();
$vessel = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vessel Inspection - NautiGuard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="adminStyle.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include('includes/admin_sidebar.php'); ?>
        
        <div class="admin-content">
            <div class="admin-header">
                <h1><i class="fas fa-clipboard-check"></i> Vessel Inspection</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4>Inspecting: <?php echo htmlspecialchars($vessel['vessel_name']); ?></h4>
                    <p>Owner: <?php echo htmlspecialchars($vessel['full_name']); ?></p>
                    
                    <form method="POST" class="mt-4">
                        <input type="hidden" name="vessel_id" value="<?php echo $vessel_id; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Inspection Rating</label>
                            <select name="inspection_rating" class="form-select" required>
                                <option value="excellent">Excellent</option>
                                <option value="very_good">Very Good</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Inspection Notes</label>
                            <textarea name="inspection_notes" class="form-control" rows="4" required></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="vessel_management.php" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Complete Inspection</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('includes/admin_footer.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>