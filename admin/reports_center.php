<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Fetch report categories
$categories = [
    'incident' => 'Incident Reports',
    'compliance' => 'Compliance Reports',
    'safety' => 'Safety Audit Reports',
    'vessel' => 'Vessel Status Reports',
    'emergency' => 'Emergency Response Reports'
];

// Handle report generation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_type = $_POST['report_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    // Generate report logic will be implemented here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Center - NautiGuard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="adminStyle.css">
    <link rel="stylesheet" href="adminAnalytics.css">
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include('includes/admin_sidebar.php'); ?>
        
        <div class="admin-content">
            <div class="analytics-container">
                <!-- Report Generation Section -->
                <div class="filter-section">
                    <form method="POST" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" name="report_type" required>
                                <?php foreach ($categories as $key => $value): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">Generate</button>
                        </div>
                    </form>
                </div>

                <!-- Recent Reports -->
                <div class="row">
                    <?php foreach ($categories as $key => $value): ?>
                        <div class="col-md-4">
                            <div class="report-card">
                                <h4><i class="fas fa-file-alt"></i> <?php echo $value; ?></h4>
                                <p>Last generated: Today</p>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary" onclick="viewReport('<?php echo $key; ?>')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="btn btn-sm btn-success" onclick="downloadReport('<?php echo $key; ?>')">
                                        <i class="fas fa-download"></i> Download
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>