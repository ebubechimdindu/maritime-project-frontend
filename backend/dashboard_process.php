<?php
session_start();
require_once 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$user_id = $_SESSION['user_id'];

// Get vessel counts by status
$active_vessels_sql = "SELECT COUNT(*) as count FROM vessels WHERE user_id = '$user_id' AND status = 'active'";
$in_port_sql = "SELECT COUNT(*) as count FROM vessels WHERE user_id = '$user_id' AND status = 'in_port'";
$at_sea_sql = "SELECT COUNT(*) as count FROM vessels WHERE user_id = '$user_id' AND status = 'active' AND location = 'at_sea'";

$active_result = $conn->query($active_vessels_sql)->fetch_assoc();
$in_port_result = $conn->query($in_port_sql)->fetch_assoc();
$at_sea_result = $conn->query($at_sea_sql)->fetch_assoc();
?>