<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    
    // Get user details
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    // Get user's vessels
    $sql = "SELECT * FROM vessels WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $vessels = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get user's incidents
    $sql = "SELECT * FROM incidents WHERE user_id = ? ORDER BY date_time DESC LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $incidents = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $user['vessels'] = $vessels;
    $user['vessels'] = $vessels;
    $user['incidents'] = $incidents;
    
    header('Content-Type: application/json');
    echo json_encode($user);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No user ID provided']);
}
?>
