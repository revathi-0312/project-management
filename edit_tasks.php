<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_management");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_GET['project_name'])) {
    die("Project name not specified.");
}

$project_name = $conn->real_escape_string($_GET['project_name']);

// Get project ID
$project_query = "SELECT project_id FROM projects WHERE project_name = '$project_name'";
$project_result = $conn->query($project_query);
if ($project_result->num_rows == 0) die("Project not found.");
$project = $project_result->fetch_assoc();
$project_id = $project['project_id'];

// Get tasks
$task_query = "SELECT * FROM tasks WHERE project_id = $project_id";
$task_result = $conn->query($task_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tasks - <?php echo htmlspecialchars($project_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 20px;
        }

        h1 {
            color: #343a40;
        }

        .task-container {
            background: #ffffff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0px 1px 4px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            margin-top: 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>

<h1>Tasks for Project: <?php echo htmlspecialchars($project_name); ?></h1>

<form method="post" action="update_tasks.php">
    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
    <input type="hidden" name="project_name" value="<?php echo htmlspecialchars($project_name); ?>"> <!-- Add this hidden input -->

    <?php while ($task = $task_result->fetch_assoc()): ?>
        <div class="task-container">
            <input type="hidden" name="task_ids[]" value="<?php echo $task['task_id']; ?>">

            <!-- Task Title -->
            <label>Task Title:</label>
            <input type="text" name="task_names[]" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>

            

            <!-- End Date -->
            <label>End Date:</label>
            <input type="date" name="end_dates[]" value="<?php echo htmlspecialchars($task['end_date']); ?>" required>

            <!-- Description -->
            <label>Description:</label>
            <input type="text" name="descriptions[]" value="<?php echo htmlspecialchars($task['description']); ?>" required>

            <!-- Status -->
            <label>Status:</label>
            <input type="text" name="statuses[]" value="<?php echo htmlspecialchars($task['status']); ?>" required>
        </div>
    <?php endwhile; ?>

    <input type="submit" value="Update Tasks">
</form>


</body>
</html>
