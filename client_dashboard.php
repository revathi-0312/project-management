<?php
session_start();

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit();
}

$client_id = $_SESSION['client_id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_management";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Project Overview
$task_count_sql = "SELECT projects.project_name, COUNT(tasks.task_id) AS task_count, 
                          projects.status 
                   FROM projects 
                   LEFT JOIN tasks ON projects.project_id = tasks.project_id 
                   WHERE projects.client_id = ? 
                   GROUP BY projects.project_id, projects.project_name, projects.status";

$task_count_stmt = $conn->prepare($task_count_sql);
$task_count_stmt->bind_param("i", $client_id);
$task_count_stmt->execute();
$task_count_result = $task_count_stmt->get_result();

$project_names = [];
$task_counts = [];
$project_statuses = [];

while ($row = $task_count_result->fetch_assoc()) {
    $project_names[] = $row['project_name'];
    $task_counts[] = $row['task_count'];
    $project_statuses[] = $row['status'];
}

// 2. Task Status Overview
$tasks_sql = "SELECT tasks.status AS task_status, COUNT(*) AS task_status_count 
              FROM tasks 
              INNER JOIN projects ON tasks.project_id = projects.project_id 
              WHERE projects.client_id = ? 
              GROUP BY tasks.status";

$tasks_stmt = $conn->prepare($tasks_sql);
$tasks_stmt->bind_param("i", $client_id);
$tasks_stmt->execute();
$tasks_result = $tasks_stmt->get_result();

$task_statuses = [
    'Completed' => 0,
    'In Progress' => 0,
    'Not Started' => 0
];

while ($row = $tasks_result->fetch_assoc()) {
    $task_statuses[$row['task_status']] = $row['task_status_count'];
}

// 3. Budget Overview
$budget_sql = "SELECT project_name, estimated_budget, actual_spent, budget_status 
               FROM projects 
               WHERE client_id = ?";

$budget_stmt = $conn->prepare($budget_sql);
$budget_stmt->bind_param("i", $client_id);
$budget_stmt->execute();
$budget_result = $budget_stmt->get_result();

$project_budgets = [];
$budget_alerts = [];

while ($row = $budget_result->fetch_assoc()) {
    $project_budgets[] = [
        'name' => $row['project_name'],
        'estimated' => $row['estimated_budget'],
        'spent' => $row['actual_spent'],
        'status' => $row['budget_status']
    ];
    if ($row['actual_spent'] > $row['estimated_budget']) {
        $budget_alerts[] = "Alert: Project '{$row['project_name']}' has exceeded its budget!";
    } elseif ($row['actual_spent'] > 0.9 * $row['estimated_budget']) {
        $budget_alerts[] = "Warning: Project '{$row['project_name']}' is nearing its budget limit.";
    }
}

// 4. Ongoing Projects
$sql_ongoing = "SELECT projects.project_id, projects.project_name, projects.start_date, projects.end_date, projects.image, clients.name 
                FROM projects 
                INNER JOIN clients ON projects.client_id = clients.client_id 
                WHERE projects.client_id = ? AND projects.status = 'In Progress'";
$stmt_ongoing = $conn->prepare($sql_ongoing);
$stmt_ongoing->bind_param("i", $client_id);
$stmt_ongoing->execute();
$result_ongoing = $stmt_ongoing->get_result();

// 5. Fetching uploaded project reports
$reports_sql = "SELECT project_name, start_date, end_date, description, pdf_path FROM project_reports WHERE client_id = ?";
$reports_stmt = $conn->prepare($reports_sql);
$reports_stmt->bind_param("i", $client_id);
$reports_stmt->execute();
$reports_result = $reports_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #4e73df;
            padding: 20px;
            color: white;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logout-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background-color: #343a40; /* Dark gray */
    color: white;
    padding: 6px 12px;
    font-size: 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.logout-btn:hover {
    background-color: #23272b; /* Slightly darker on hover */
}

        .container {
            padding: 20px;
        }
        .chart-container {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .alert {
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            margin: 10px 0;
            border-radius: 5px;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        td a {
            color: #007bff;
            text-decoration: none;
        }
        td a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="header">
    Client Dashboard
    <a href="client_logout.php" class="logout-btn">Logout</a>
</div>

<div class="container">

    <!-- Budget Alerts -->
    <?php foreach ($budget_alerts as $alert): ?>
        <div class="alert"><?php echo htmlspecialchars($alert); ?></div>
    <?php endforeach; ?>

    <!-- Project Overview (Bar Chart) -->
    <div class="chart-container">
        <h2>Project Overview (Number of Tasks per Project)</h2>
        <canvas id="projectBarChart"></canvas>
    </div>

    <!-- Task Overview (Bar Chart) -->
    <div class="chart-container">
        <h2>Task Overview (Task Status Counts)</h2>
        <canvas id="taskBarChart"></canvas>
    </div>

    <!-- Budget Overview (Bar Chart) -->
    <div class="chart-container">
        <h2>Budget Overview (Estimated vs Actual)</h2>
        <canvas id="budgetChart"></canvas>
    </div>

    <!-- Section to display uploaded project reports -->
    <h2>Uploaded Project Reports</h2>
    <table>
        <thead>
            <tr>
                <th>Project Name</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Description</th>
                <th>Download Link</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($reports_result->num_rows > 0): ?>
                <?php while($row = $reports_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($row['description'])); ?></td>
                        <td><a href="<?php echo htmlspecialchars($row['pdf_path']); ?>" target="_blank">View Report</a></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">No reports available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<script>
// Project Overview Chart
const ctx1 = document.getElementById('projectBarChart').getContext('2d');
const gradient1 = ctx1.createLinearGradient(0, 0, 0, 400);
gradient1.addColorStop(0, 'rgba(78, 115, 223, 0.6)');
gradient1.addColorStop(1, 'rgba(78, 115, 223, 1)');

const projectBarChart = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($project_names); ?>,
        datasets: [{
            label: 'Number of Tasks',
            data: <?php echo json_encode($task_counts); ?>,
            backgroundColor: gradient1,
            borderColor: 'rgba(78, 115, 223, 1)',
            borderWidth: 1,
            barThickness: 80
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Task Status Chart
const ctx2 = document.getElementById('taskBarChart').getContext('2d');
const gradient2 = ctx2.createLinearGradient(0, 0, 0, 400);
gradient2.addColorStop(0, 'rgba(255, 99, 132, 0.6)');
gradient2.addColorStop(1, 'rgba(255, 99, 132, 1)');

const taskBarChart = new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['Completed', 'In Progress', 'Not Started'],
        datasets: [{
            label: 'Task Status Count',
            data: [
                <?php echo $task_statuses['Completed']; ?>,
                <?php echo $task_statuses['In Progress']; ?>,
                <?php echo $task_statuses['Not Started']; ?>
            ],
            backgroundColor: gradient2,
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
            barThickness: 80
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});

// Budget Overview Chart
const ctx3 = document.getElementById('budgetChart').getContext('2d');
const gradient3 = ctx3.createLinearGradient(0, 0, 0, 400);
gradient3.addColorStop(0, 'rgba(54, 162, 235, 0.6)');
gradient3.addColorStop(1, 'rgba(54, 162, 235, 1)');

const budgetChart = new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($project_budgets, 'name')); ?>,
        datasets: [
            {
                label: 'Estimated Budget',
                data: <?php echo json_encode(array_column($project_budgets, 'estimated')); ?>,
                backgroundColor: gradient3,
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Actual Spent',
                data: <?php echo json_encode(array_column($project_budgets, 'spent')); ?>,
                backgroundColor: 'rgba(255, 99, 132, 0.6)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>

</body>
</html>
