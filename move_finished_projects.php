<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_management";

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current date
$currentDate = date("Y-m-d");

// Prepare a statement to select projects that have ended
$sql = "SELECT * FROM projects WHERE end_date < ? AND status != 'finished'";  // Only select projects that haven't been moved already
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("s", $currentDate);
$stmt->execute();
$result = $stmt->get_result();

// Loop through projects that have finished
while ($row = $result->fetch_assoc()) {
    $project_name = $row['project_name'];  // Use project_name as a reference

    // Check if the project already exists in finished_projects
    $checkSql = "SELECT 1 FROM finished_projects WHERE name = ?";
    $checkStmt = $conn->prepare($checkSql);
    if (!$checkStmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $checkStmt->bind_param("s", $project_name);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows === 0) {  // Only move if the project is not already moved
        // Insert the project into finished_projects
        $insertSql = "INSERT INTO finished_projects (name, image, details, company, end_date)
                      VALUES (?, ?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        if (!$insertStmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $insertStmt->bind_param(
            "sssss",  // Parameter types (string, string, string, string, date)
            $row['project_name'],
            $row['image'],
            $row['description'],
            $row['name'],  // Assuming 'name' is the company or client name
            $row['end_date']
        );
        $insertStmt->execute();
        $insertStmt->close();

        // Update the project status to 'finished' to prevent it from being moved again
        $updateSql = "UPDATE projects SET status = 'finished' WHERE project_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        if (!$updateStmt) {
            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        }
        $updateStmt->bind_param("i", $row['project_id']);
        $updateStmt->execute();
        $updateStmt->close();
    }

    $checkStmt->close();
}

// Close connections
$stmt->close();
$conn->close();
?>
