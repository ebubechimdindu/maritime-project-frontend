<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: adminLogin.php');
    exit();
}

// Fetch incident statistics
$incident_stats = $conn->query("
    SELECT 
        COUNT(*) as total_incidents,
        SUM(CASE WHEN severity_level = 'critical' THEN 1 ELSE 0 END) as critical_incidents,
        SUM(CASE WHEN severity_level = 'high' THEN 1 ELSE 0 END) as high_incidents,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_incidents
    FROM incidents
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
")->fetch_assoc();

// Fetch compliance statistics
$compliance_stats = $conn->query("
    SELECT 
        AVG(safety_equipment_check + navigation_systems_check + 
            crew_certification_check + environmental_compliance + 
            hull_integrity_check + firefighting_equipment + 
            medical_supplies_check + radio_equipment_check + 
            waste_management_check + security_systems_check) * 10 as avg_compliance_score
    FROM vessel_compliance
")->fetch_assoc();

// Fetch monthly incident trends
$monthly_trends = $conn->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as incident_count
    FROM incidents
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 12
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safety Analytics - NautiGuard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="adminStyle.css">
    <link rel="stylesheet" href="adminAnalytics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="admin-body">
    <div class="admin-container">
        <?php include('includes/admin_sidebar.php'); ?>
        
        <div class="admin-content">
            <div class="analytics-container">
                <!-- Key Metrics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo $incident_stats['total_incidents']; ?></div>
                            <div class="metric-label">Total Incidents</div>
                            <div class="trend-indicator">
                                <i class="fas fa-arrow-up"></i> 12% from last month
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo round($compliance_stats['avg_compliance_score'], 1); ?>%</div>
                            <div class="metric-label">Average Compliance Score</div>
                            <div class="trend-indicator trend-up">
                                <i class="fas fa-arrow-up"></i> 5% improvement
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?php echo $incident_stats['critical_incidents']; ?></div>
                            <div class="metric-label">Critical Incidents</div>
                            <div class="trend-indicator trend-down">
                                <i class="fas fa-arrow-down"></i> 8% decrease
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="metric-card">
                            <div class="metric-value"><?php 
                                echo round(($incident_stats['resolved_incidents'] / $incident_stats['total_incidents']) * 100);
                            ?>%</div>
                            <div class="metric-label">Resolution Rate</div>
                            <div class="trend-indicator">
                                <i class="fas fa-arrow-up"></i> 3% improvement
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Incident Trends</h3>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="incidentTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Compliance Distribution</h3>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="complianceChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Analytics -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart-card">
                            <div class="chart-header">
                                <h3 class="chart-title">Safety Performance Index</h3>
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="safetyIndexChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/safety_analytics.js"></script>
</body>
</html>