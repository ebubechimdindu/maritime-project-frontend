<?php
// Database configuration
$host = 'localhost'; // or your database host
$username = 'your_username'; // your database username
$password = 'your_password'; // your database password
$database = 'maritime'; // your database name

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// Close connection (optional, usually closed at the end of the script)
// $conn->close();
?>