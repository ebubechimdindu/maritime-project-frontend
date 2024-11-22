<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (isset($_GET['vessel_id'])) {
    $vessel_id = $_GET['vessel_id'];
    
    $sql = "SELECT v.*, u.username, u.full_name 
            FROM vessels v 
            JOIN users u ON v.user_id = u.user_id 
            WHERE v.vessel_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $vessel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $vessel = $result->fetch_assoc();
    
    header('Content-Type: application/json');
    echo json_encode($vessel);
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No vessel ID provided']);
}
?>