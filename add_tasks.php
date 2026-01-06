<?php
// add_tasks.php

// Database connection
$conn = new mysqli('localhost', 'root', '', 'project_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name   = $_POST['task_name'];
    $description = $_POST['description'];
    $start_date  = $_POST['start_date'];  // Get the start date
    $end_date    = $_POST['end_date'];
    $project_id  = $_POST['project_id'];
    $assigned_to = $_POST['assigned_to'];
    $status      = $_POST['status'];

    // Check if 'end_date' is a valid date
    if (empty($end_date) || !strtotime($end_date)) {
        die("Invalid end date");
    }

    // Debug output for verification
    var_dump($end_date);

    // Check if the selected project_id exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM projects WHERE project_id = ?");
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        die("Selected project does not exist. Please choose a valid project.");
    }

    // Proceed with inserting the task
    $stmt = $conn->prepare("INSERT INTO tasks (task_name, description, start_date, end_date, project_id, assigned_to, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiss", $task_name, $description, $start_date, $end_date, $project_id, $assigned_to, $status);

    if (!$stmt->execute()) {
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }

    $stmt->close();
    $success_message = "Task added successfully!";
}

// Fetch all projects
$projects = $conn->query("SELECT * FROM projects");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Task</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('tasksimg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', sans-serif;
        }

        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 12px;
            margin-top: 20px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .success {
            text-align: center;
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="form-container">
            <h1>Add New Task</h1>
            <?php if (!empty($success_message)): ?>
                <div class="success"><?php echo $success_message; ?></div>
            <?php endif; ?>
                <!-- HTML Form -->
<form method="POST" action="">
    <label for="task_name">Task Name:</label>
    <input type="text" name="task_name" id="task_name" required>

    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea>

    <label for="start_date">Start Date:</label>
    <input type="date" name="start_date" id="start_date" required>

    <label for="end_date">Due Date:</label>
    <input type="date" name="end_date" id="end_date" required>

    <label for="project_id">Select Project:</label>
    <select name="project_id" id="project_id" required>
        <?php while($project = $projects->fetch_assoc()): ?>
            <option value="<?php echo $project['project_id']; ?>">
                <?php echo htmlspecialchars($project['project_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="assigned_to">Assigned To:</label>
    <input type="text" name="assigned_to" id="assigned_to">

    <label for="status">Status:</label>
    <select name="status" id="status">
        <option value="Not Started">Not Started</option>
        <option value="In Progress">In Progress</option>
    </select>

    <input type="submit" value="Add Task">
</form>


        </div>
    </div>


</body>
</html>
