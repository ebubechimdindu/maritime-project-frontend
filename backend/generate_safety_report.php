<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch compliance data
$compliance_query = "SELECT v.vessel_name, v.imo_number, vc.* 
                    FROM vessels v 
                    LEFT JOIN vessel_compliance vc ON v.vessel_id = vc.vessel_id 
                    WHERE v.user_id = '$user_id'";
$compliance_result = $conn->query($compliance_query);

// Set headers for download
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="safety_report.html"');

// Generate HTML report
echo '<!DOCTYPE html>
<html>
<head>
    <title>Safety Compliance Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .vessel-section { margin-bottom: 30px; border-bottom: 1px solid #ccc; padding-bottom: 20px; }
        .compliance-item { margin: 10px 0; }
        .status { padding: 3px 8px; border-radius: 3px; }
        .passed { background: #d4edda; color: #155724; }
        .failed { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NautiGuard Safety Compliance Report</h1>
        <p>Generated on: ' . date('Y-m-d H:i:s') . '</p>
    </div>';

while ($row = $compliance_result->fetch_assoc()) {
    echo '<div class="vessel-section">
        <h2>Vessel: ' . htmlspecialchars($row['vessel_name']) . '</h2>
        <p>IMO Number: ' . htmlspecialchars($row['imo_number']) . '</p>
        
        <h3>Compliance Checks:</h3>
        <div class="compliance-item">
            <p>Safety Equipment: <span class="status ' . ($row['safety_equipment_check'] ? 'passed">Passed' : 'failed">Not Checked') . '</span></p>
            <p>Navigation Systems: <span class="status ' . ($row['navigation_systems_check'] ? 'passed">Passed' : 'failed">Not Checked') . '</span></p>
            <p>Crew Certification: <span class="status ' . ($row['crew_certification_check'] ? 'passed">Passed' : 'failed">Not Checked') . '</span></p>
            <p>Environmental Compliance: <span class="status ' . ($row['environmental_compliance'] ? 'passed">Passed' : 'failed">Not Checked') . '</span></p>
            <p>Hull Integrity: <span class="status ' . ($row['hull_integrity_check'] ? 'passed">Passed' : 'failed">Not Checked') . '</span></p>
        </div>
        
        <p>Last Checked: ' . ($row['last_checked'] ?? 'Not available') . '</p>
        <p>Next Check Due: ' . ($row['next_check_due'] ?? 'Not scheduled') . '</p>
    </div>';
}

echo '</body></html>';
?>
