<?php
require_once 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $conn->real_escape_string($_POST['gender']);
    
    // Verify passwords match
    if ($password !== $confirm_password) {
        header("Location: userSignup.php?error=password_mismatch");
        exit();
    }
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if username or email already exists
    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = $conn->query($check_query);
    
    if ($result->num_rows > 0) {
        header("Location: userSignup.php?error=exists");
        exit();
    }
    
    // Insert new user
    $sql = "INSERT INTO users (full_name, email, username, password_hash, gender) 
            VALUES ('$name', '$email', '$username', '$password_hash', '$gender')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: userLogin.php?registration=success");
    } else {
        header("Location: userSignup.php?error=failed");
    }
}

$conn->close();
?>
