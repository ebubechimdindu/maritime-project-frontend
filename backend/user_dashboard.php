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
?>

<!-- Add this at the beginning of your existing HTML -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
        
        <main class="main-content">
            <header class="top-bar">
                <div class="search-bar">
                    <input type="text" placeholder="Search...">
                </div>
                <div class="user-info">
                    <span class="user-name">John Doe</span>
                    <img src="avatar.jpg" alt="User Avatar" class="avatar">
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
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
    </style>
</body>
</body>
</html>