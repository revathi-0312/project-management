<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'project_management';

// Create a new MySQLi connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch projects for the dropdown
$projects = [];
$project_query = "SELECT project_id, project_name FROM projects";
$result = $conn->query($project_query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize POST data
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.0;
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $date_incurred = isset($_POST['date_incurred']) ? $_POST['date_incurred'] : '';

    // Validate required fields
    if ($project_id > 0 && $amount > 0 && !empty($description) && !empty($date_incurred)) {
        // Prepare and bind parameters to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO expenses (project_id, amount, description, date_incurred) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $project_id, $amount, $description, $date_incurred);
if ($stmt->execute()) {
    // Calculate total expenses for the project
    $total_query = $conn->prepare("SELECT SUM(amount) FROM expenses WHERE project_id = ?");
    $total_query->bind_param("i", $project_id);
    $total_query->execute();
    $total_query->bind_result($total_spent);
    $total_query->fetch();
    $total_query->close();

    // Retrieve the estimated budget for the project
    $budget_query = $conn->prepare("SELECT estimated_budget FROM projects WHERE project_id = ?");
    $budget_query->bind_param("i", $project_id);
    $budget_query->execute();
    $budget_query->bind_result($estimated_budget);
    $budget_query->fetch();
    $budget_query->close();

    // Determine the budget status
    if ($total_spent < $estimated_budget) {
        $budget_status = 'Under Budget';
    } elseif ($total_spent == $estimated_budget) {
        $budget_status = 'On Budget';
    } else {
        $budget_status = 'Over Budget';
    }

    // Update the projects table with the new actual_spent and budget_status
    $update_project = $conn->prepare("UPDATE projects SET actual_spent = ?, budget_status = ? WHERE project_id = ?");
    $update_project->bind_param("dsi", $total_spent, $budget_status, $project_id);
    $update_project->execute();
    $update_project->close();

    $message = "<p style='color: green;'>Expense added and project budget updated successfully.</p>";
} else {
    $message = "<p style='color: red;'>Error: " . $stmt->error . "</p>";
}

    $stmt->close();
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-image: url('expense.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }

        select,
        input[type="number"],
        input[type="datetime-local"],
        textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: none;
            background-color: #007bff;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }

            select,
            input[type="number"],
            input[type="datetime-local"],
            textarea,
            button {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Expense</h1>
        <form method="POST" action="">
            <label for="project_id">Project Name:</label>
            <select name="project_id" required>
                <option value="">-- Select Project --</option>
                <?php foreach ($projects as $project): ?>
                    <option value="<?php echo htmlspecialchars($project['project_id']); ?>">
                        <?php echo htmlspecialchars($project['project_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="amount">Amount:</label>
            <input type="number" step="0.01" name="amount" required>

            <label for="description">Description:</label>
            <textarea name="description" required></textarea>

            <label for="date_incurred">Date Incurred:</label>
            <input type="datetime-local" name="date_incurred" required>

            <button type="submit">Add Expense</button>
        </form>

        <?php if (!empty($message)) echo $message; ?>

    </div>
</body>
</html>