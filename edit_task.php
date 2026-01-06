<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "project_management");
$project_id = $_GET['project_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = $_POST['task_id'];
    $due_date = $_POST['due_date'];
    $assigned_member = $_POST['assigned_member'];

    $conn->query("UPDATE tasks SET due_date='$due_date', assigned_member='$assigned_member' WHERE task_id=$task_id");
    echo "<p style='color: green;'>Task updated successfully!</p>";
}

$result = $conn->query("SELECT * FROM tasks WHERE project_id = $project_id");
?>

<h2>Edit Tasks for Project ID: <?php echo $project_id; ?></h2>
<?php while($task = $result->fetch_assoc()): ?>
    <form method="post">
        <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
        <p><strong><?php echo $task['task_name']; ?></strong></p>
        <label>Due Date:</label><br>
        <input type="date" name="due_date" value="<?php echo $task['due_date']; ?>"><br>
        <label>Assigned Member:</label><br>
        <input type="text" name="assigned_member" value="<?php echo $task['assigned_member']; ?>"><br>
        <button type="submit">Update Task</button>
    </form>
    <hr>
<?php endwhile; ?>
