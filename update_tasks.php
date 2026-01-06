<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_management");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Debugging: Check if $_POST is receiving the data
echo '<pre>';
var_dump($_POST);
echo '</pre>';

if (
    isset($_POST['project_id']) &&
    isset($_POST['task_ids']) &&
    isset($_POST['task_names']) &&
    isset($_POST['end_dates']) &&
    isset($_POST['descriptions']) &&
    isset($_POST['statuses'])
) {
    $project_id = $_POST['project_id'];
    $task_ids = $_POST['task_ids'];
    $task_names = $_POST['task_names'];
    $end_dates = $_POST['end_dates'];
    $descriptions = $_POST['descriptions'];
    $statuses = $_POST['statuses'];

    // Update tasks in the database
    for ($i = 0; $i < count($task_ids); $i++) {
        $task_id = $task_ids[$i];
        $task_name = isset($task_names[$i]) ? $conn->real_escape_string($task_names[$i]) : '';
        $end_date = isset($end_dates[$i]) ? $conn->real_escape_string($end_dates[$i]) : '';
        $description = isset($descriptions[$i]) ? $conn->real_escape_string($descriptions[$i]) : '';
        $status = isset($statuses[$i]) ? $conn->real_escape_string($statuses[$i]) : '';

        if (empty($task_name) || empty($end_date) || empty($description) || empty($status)) {
            die("One or more required fields are missing for task with ID $task_id.");
        }

        $update_query = "UPDATE tasks SET 
            task_name = '$task_name',  
            end_date = '$end_date', 
            description = '$description', 
            status = '$status' 
            WHERE task_id = $task_id";

        if (!$conn->query($update_query)) {
            die("Error updating task with ID $task_id: " . $conn->error);
        }
    }

    // Redirect to dashboard after successful update
    header("Location: dashboard.php");
    exit();
} else {
    die("Missing required data.");
}
?>
