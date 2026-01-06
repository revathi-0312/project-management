<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_management";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update project if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_name = $conn->real_escape_string($_POST['original_name']);
    $project_name = $conn->real_escape_string($_POST['project_name']);
    $start_date = $conn->real_escape_string($_POST['start_date']);
    $end_date = $conn->real_escape_string($_POST['end_date']);
    $image = $conn->real_escape_string($_POST['image']);

    $update_sql = "
        UPDATE projects 
        SET project_name = '$project_name', 
            start_date = '$start_date', 
            end_date = '$end_date', 
            image = '$image' 
        WHERE project_name = '$original_name'
    ";

    if ($conn->query($update_sql)) {
        echo "<p style='color:green;'>Project updated successfully!</p>";
    } else {
        echo "<p style='color:red;'>Update failed: " . $conn->error . "</p>";
    }

    // Update project_name for re-fetch
    $_GET['project_name'] = $project_name;
}

// Get project_name from URL
if (!isset($_GET['project_name'])) {
    die("Project name not provided.");
}

$project_name = $conn->real_escape_string($_GET['project_name']);

// Fetch project using project_name
$sql = "SELECT * FROM projects WHERE project_name = '$project_name'";
$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}

if ($result->num_rows === 0) {
    die("No project found with that name.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Project</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f4f4f4;
        }
        form {
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            max-width: 500px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007BFF;
            color: white;
            padding: 10px 15px;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        h1 {
            text-align: center;
            color: #007BFF;
        }
    </style>
</head>
<body>

<h1>Edit Project: <?php echo htmlspecialchars($row['project_name']); ?></h1>

<form method="post" action="edit_project.php">
    <input type="hidden" name="original_name" value="<?php echo htmlspecialchars($row['project_name']); ?>">

    <label>Project Name:</label>
    <input type="text" name="project_name" value="<?php echo htmlspecialchars($row['project_name']); ?>" required>

    <label>Start Date:</label>
    <input type="date" name="start_date" value="<?php echo htmlspecialchars($row['start_date']); ?>" required>

    <label>End Date:</label>
    <input type="date" name="end_date" value="<?php echo htmlspecialchars($row['end_date']); ?>" required>

    <label>Image URL:</label>
    <input type="text" name="image" value="<?php echo htmlspecialchars($row['image']); ?>">

    <input type="submit" value="Update Project">
</form>

</body>
</html>

<?php
$conn->close();
?>
