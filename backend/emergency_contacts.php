<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Contacts - NautiGuard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_style.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <main class="main-content">
            <header class="top-bar">
                <h1>Emergency Contacts</h1>
                <div class="user-info">
                    <span class="user-name"><?php echo $_SESSION['full_name']; ?></span>
                </div>
            </header>

            <div class="container mt-4">
                <!-- Global Emergency Numbers -->
                <div class="card mb-4">
                    <div class="card-header bg-danger text-white">
                        <h3><i class="fas fa-phone-alt"></i> Global Emergency Numbers</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>International Maritime Distress</h5>
                                <p><strong>Channel 16 VHF (156.8 MHz)</strong></p>
                                <p><strong>Global SAR Coordination: </strong>+1 (555) 0123-4567</p>
                            </div>
                            <div class="col-md-6">
                                <h5>24/7 Emergency Hotline</h5>
                                <p><strong>Maritime Rescue: </strong>+1 (555) 7890-1234</p>
                                <p><strong>Satellite Phone: </strong>+870 123 456 789</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Regional Maritime Centers -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-globe"></i> Regional Maritime Centers</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <h5>Asia Pacific</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Singapore MRCC:</strong> +65 6226 1232</li>
                                    <li><strong>Hong Kong MRCC:</strong> +852 2233 7999</li>
                                    <li><strong>Japanese Coast Guard:</strong> +81 3 3591 9000</li>
                                </ul>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h5>Europe</h5>
                                <ul class="list-unstyled">
                                    <li><strong>UK Coastguard:</strong> +44 2392 552100</li>
                                    <li><strong>Mediterranean MRCC:</strong> +39 0659 084409</li>
                                    <li><strong>Rotterdam Port:</strong> +31 10 252 1000</li>
                                </ul>
                            </div>
                            <div class="col-md-4 mb-3">
                                <h5>Americas</h5>
                                <ul class="list-unstyled">
                                    <li><strong>US Coast Guard:</strong> +1 202-372-2100</li>
                                    <li><strong>Panama Canal:</strong> +507 272-1111</li>
                                    <li><strong>Canadian Coast Guard:</strong> +1 613-993-0999</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Specialized Emergency Services -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><i class="fas fa-ambulance"></i> Specialized Emergency Services</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h5>Medical Evacuation</h5>
                                <ul class="list-unstyled">
                                    <li><strong>International SOS:</strong> +1 (555) 999-8888</li>
                                    <li><strong>Maritime Medical:</strong> +1 (555) 777-6666</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5>Fire & Hazmat</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Emergency Response:</strong> +1 (555) 444-3333</li>
                                    <li><strong>Chemical Spill:</strong> +1 (555) 222-1111</li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h5>Security Services</h5>
                                <ul class="list-unstyled">
                                    <li><strong>Piracy Report Center:</strong> +60 3 2031 0014</li>
                                    <li><strong>Maritime Security:</strong> +1 (555) 111-0000</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-info-circle"></i> Emergency Procedures</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5>When Reporting an Emergency:</h5>
                            <ol>
                                <li>State "MAYDAY, MAYDAY, MAYDAY" for life-threatening emergencies</li>
                                <li>Provide vessel name and call sign</li>
                                <li>State your position (latitude/longitude)</li>
                                <li>Describe the nature of emergency</li>
                                <li>Specify type of assistance needed</li>
                                <li>Report number of persons aboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>