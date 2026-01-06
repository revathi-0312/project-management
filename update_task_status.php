<?php
// update_task_status.php

// Database connection
$conn = new mysqli('localhost', 'root', '', 'project_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get current date
$currentDate = date('Y-m-d');

// Update tasks to 'In Progress' where current date is between start_date and due_date
$inProgressSql = "UPDATE tasks SET status = 'In Progress' WHERE start_date <= ? AND due_date >= ?";
$stmt = $conn->prepare($inProgressSql);
$stmt->bind_param("ss", $currentDate, $currentDate);
$stmt->execute();
$stmt->close();

// Update tasks to 'Completed' where current date is past due_date
$completedSql = "UPDATE tasks SET status = 'Completed' WHERE due_date < ?";
$stmt = $conn->prepare($completedSql);
$stmt->bind_param("s", $currentDate);
$stmt->execute();
$stmt->close();

$conn->close();
?>
