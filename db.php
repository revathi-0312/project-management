<?php
// Database connection parameters
$servername = 'localhost'; // Change if your database is hosted elsewhere
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password
$dbname = "project_management";

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>