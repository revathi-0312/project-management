<?php
// Database connection parameters
$servername = "localhost"; // Change as needed
$username = "root"; // Change as needed
$password = ""; // Change as needed
$dbname = "project_management"; // Change as needed

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert a test user
$test_username = "admin";
$test_password = password_hash("Admin", PASSWORD_DEFAULT); // Hash the password

$stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $test_username, $test_password);
$stmt->execute();

echo "Test user created successfully.";

$stmt->close();
$conn->close();
?>