<?php
session_start();
require_once('../backend/conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $vessel_name = $conn->real_escape_string($_POST['vessel_name']);
    $imo_number = $conn->real_escape_string($_POST['imo_number']);
    $vessel_type = $conn->real_escape_string($_POST['vessel_type']);
    $flag_state = $conn->real_escape_string($_POST['flag_state']);
    $gross_tonnage = !empty($_POST['gross_tonnage']) ? $conn->real_escape_string($_POST['gross_tonnage']) : NULL;
    $year_built = !empty($_POST['year_built']) ? $conn->real_escape_string($_POST['year_built']) : NULL;
    $location = $conn->real_escape_string($_POST['location']);
    $status = $conn->real_escape_string($_POST['status']);
    $departing_from = $conn->real_escape_string($_POST['departing_from']);
    $arriving_at = $conn->real_escape_string($_POST['arriving_at']);
    $departure_time = $conn->real_escape_string($_POST['departure_time']);
    $estimated_arrival = $conn->real_escape_string($_POST['estimated_arrival']);
    $classification_society = !empty($_POST['classification_society']) ? $conn->real_escape_string($_POST['classification_society']) : NULL;

    // Prepare SQL statement
    $sql = "INSERT INTO vessels (
        user_id, vessel_name, imo_number, vessel_type, flag_state, 
        gross_tonnage, year_built, location, status, departing_from, 
        arriving_at, departure_time, estimated_arrival, classification_society
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "issssdisssssss",
        $user_id, $vessel_name, $imo_number, $vessel_type, $flag_state,
        $gross_tonnage, $year_built, $location, $status, $departing_from,
        $arriving_at, $departure_time, $estimated_arrival, $classification_society
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Vessel added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding vessel: " . $conn->error;
    }

    $stmt->close();
    header("Location: vessel_management.php");
    exit();
}

// Handle vessel updates
if (isset($_POST['update_vessel'])) {
    $vessel_id = $conn->real_escape_string($_POST['vessel_id']);
    
    // Add similar validation and sanitization as above
    // Update query for existing vessel
    $sql = "UPDATE vessels SET 
            vessel_name = ?, 
            imo_number = ?,
            vessel_type = ?,
            flag_state = ?,
            gross_tonnage = ?,
            year_built = ?,
            location = ?,
            status = ?,
            departing_from = ?,
            arriving_at = ?,
            departure_time = ?,
            estimated_arrival = ?,
            classification_society = ?
            WHERE vessel_id = ? AND user_id = ?";
    
    $stmt = $conn->prepare($sql);
    // Add bind_param and execute for update
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Vessel updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating vessel: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: vessel_management.php");
    exit();
}
?>