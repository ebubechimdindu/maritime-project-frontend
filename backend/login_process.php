<?php
session_start();
require_once 'conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Get user from database including status
    $sql = "SELECT user_id, username, password_hash, full_name, status FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if ($user['status'] === 'disabled') {
            header("Location: userLogin.php?error=disabled");
            exit();
        }

        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];

            header("Location: user_dashboard.php");
            exit();
        } else {
            header("Location: userLogin.php?error=invalid");
            exit();
        }
    } else {
        header("Location: userLogin.php?error=invalid");
        exit();
    }
}

$conn->close();
