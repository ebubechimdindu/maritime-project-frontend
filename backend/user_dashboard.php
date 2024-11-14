<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

// Get user data from session
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

// Display user's full name in the top bar
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }

        .logo h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        .nav-links {
            list-style: none;
        }

        .nav-links li {
            margin-bottom: 15px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 10px;
            transition: 0.3s;
        }

        .nav-links a:hover {
            background: #34495e;
            border-radius: 5px;
        }

        .main-content {
            flex: 1;
            background: #f5f6fa;
            padding: 20px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .search-bar input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 300px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .stats {
            display: flex;
            justify-content: space-around;
        }

        .stat-item {
            text-align: center;
        }

        .number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }

        .label {
            color: #7f8c8d;
        }

        .activity-list {
            list-style: none;
        }

        .activity-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn {
            padding: 10px;
            border: none;
            border-radius: 5px;
            background: #3498db;
            color: white;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #2980b9;
        }

        .logout-modal-wrapper .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
        }

        .modern-logout-modal {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 420px;
            width: 90%;
            margin: 0 auto;
        }

        .modern-logout-modal .modal-body {
            text-align: center;
            padding: 40px 30px;
            background: white;
        }

        .logout-icon {
            width: 100px;
            height: 100px;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .logout-icon svg {
            width: 48px;
            height: 48px;
            color: #e74c3c;
            stroke-width: 2;
        }

        .logout-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .logout-message {
            color: #7f8c8d;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .logout-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .btn-cancel,
        .btn-logout {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-cancel {
            background-color: #f1f2f6;
            color: #2f3542;
            border: none;
        }

        .btn-cancel:hover {
            background-color: #dfe4ea;
        }

        .btn-logout {
            background-color: #e74c3c;
            color: white;
            border: none;
        }

        .btn-logout:hover {
            background-color: #c0392b;
        }

        /* Modal Animation */
        .modal.fade .modal-dialog {
            transform: scale(0.7);
            opacity: 0;
            transition: all 0.3s ease-in-out;
        }

        .modal.fade.show .modal-dialog {
            transform: scale(1);
            opacity: 1;
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <div class="logo">
                <h2>User Dashboard</h2>
            </div>
            <ul class="nav-links">
                <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="#"><i class="fas fa-chart-bar"></i> Analytics</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>

                <li><a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <main class="main-content">
            <header class="top-bar">
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-info">
                    <span class="user-name"><?php echo "Welcome " . $full_name; ?></span>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="card">
                    <h3>Statistics</h3>
                    <div class="stats">
                        <div class="stat-item">
                            <span class="number">150</span>
                            <span class="label">Total Orders</span>
                        </div>
                        <div class="stat-item">
                            <span class="number">$2.5k</span>
                            <span class="label">Revenue</span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3>Recent Activities</h3>
                    <ul class="activity-list">
                        <li>New order received - #12345</li>
                        <li>Profile updated</li>
                        <li>Payment processed</li>
                    </ul>
                </div>

                <div class="card">
                    <h3>Quick Actions</h3>
                    <div class="action-buttons">
                        <button class="btn">Create Order</button>
                        <button class="btn">Generate Report</button>
                        <button class="btn">View Messages</button>
                    </div>
                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>