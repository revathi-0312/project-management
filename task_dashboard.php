<?php
// Database connection (Ensure this is included)
$conn = new mysqli('localhost', 'root', '', 'project_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all projects
$projects = $conn->query("SELECT * FROM projects");

$selected_project_id = isset($_GET['project_id']) ? $_GET['project_id'] : null;

// Fetch tasks based on selected project
$tasks = [];
if ($selected_project_id) {
    $tasks_result = $conn->query("SELECT * FROM tasks WHERE project_id = $selected_project_id ORDER BY created_at DESC");
} else {
    $tasks_result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
}

// Process tasks
$status_counts = ["Not Started" => 0, "In Progress" => 0, "Completed" => 0];
$task_list = [];
$today = date('Y-m-d');

while ($row = $tasks_result->fetch_assoc()) {
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];

    // Auto calculate status based on the current date
    if ($today < $start_date) {
        $row['status'] = "Not Started";
    } elseif ($today >= $start_date && $today <= $end_date) {
        $row['status'] = "In Progress";
    } else {
        $row['status'] = "Completed";
    }

    // Update status in the database if needed
    $conn->query("UPDATE tasks SET status = '{$row['status']}' WHERE task_id = {$row['task_id']}");

    // Calculate progress (based on status for demo purposes)
    $progress = 0;
    if ($row['status'] == 'In Progress') {
        $progress = 50;
    } elseif ($row['status'] == 'Completed') {
        $progress = 100;
    }

    // Format time for better display
    $created_at = new DateTime($row['created_at']);
    $row['random_time'] = $created_at->format('h:i A');

    $status_counts[$row['status']]++;
    $task_list[] = ['task' => $row, 'progress' => $progress];

// Inside your task loop
$start_time = new DateTime($row['start_date']);
$end_time = new DateTime($row['end_date']);
$interval = $start_time->diff($end_time);

// Calculate total time spent in hours
$time_spent = ($interval->days * 24) + $interval->h + ($interval->i / 60);

// Update the task's time_spent in the database
$conn->query("UPDATE tasks SET time_spent = $time_spent WHERE task_id = {$row['task_id']}");


}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.5.1/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Add Chart.js Library -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 40px;
        }

        /* Project List */
        .project-list {
            list-style: none;
            padding: 0;
            text-align: center;
        }

        .project-list li {
            display: inline-block;
            margin-right: 20px;
        }

        .project-list li a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #4caf50;
            color: #fff;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .project-list li a:hover {
            background-color: #45a049;
        }

        /* Task Cards Styles */
        .task-card {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .task-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .task-header {
            font-size: 20px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #333;
        }

        .task-body {
            margin-top: 15px;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #e0e0e0;
            border-radius: 10px;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 10px;
            text-align: center;
            color: white;
            line-height: 20px;
            transition: width 1s ease-in-out;
        }

        .completed { background-color: #4caf50; }
        .in-progress { background-color: #ff9800; }
        .not-started { background-color: #f44336; }

        .chart-container {
            width: 100%;
            height: 350px;
            margin-top: 50px;
        }

        .status {
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Task Dashboard</h1>

        <!-- Project List -->
        <ul class="project-list">
        

        <!-- Task Overview Chart -->
        <div class="chart-container">
            <canvas id="taskOverviewChart"></canvas>
        </div>

        <!-- Task Cards -->
        <div class="task-cards">
            <?php foreach ($task_list as $task): ?>
                <?php $task = $task['task']; ?>

                <div class="task-card">
                    <div class="task-header">
                        <span><?php echo htmlspecialchars($task['task_name']); ?></span>
                        <span class="status"><?php echo $task['status']; ?></span>
                    </div>
                    <div class="task-body">
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($task['description']); ?></p>
                        <p><strong>Assigned To:</strong> <?php echo htmlspecialchars($task['assigned_to']); ?></p>
                        <p><strong>Due Date:</strong> <?php echo $task['end_date']; ?></p>
                         <p><strong>Time Spent:</strong> <?php echo $task['time_spent']; ?> hours</p>


                        <!-- Progress Bar -->
                        <div class="progress-bar">
                            <div class="progress-bar-fill <?php echo strtolower(str_replace(' ', '-', $task['status'])); ?>" style="width: <?php echo $task['progress']; ?>%;">
                                <?php echo $task['progress']; ?>%
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        // Task Overview Chart (using Chart.js)
        var ctx = document.getElementById('taskOverviewChart').getContext('2d');
        var taskOverviewChart = new Chart(ctx, {
            type: 'bar', // Changed to bar chart
            data: {
                labels: ['Not Started', 'In Progress', 'Completed'],
                datasets: [{
                    label: 'Task Status Overview',
                    data: [<?php echo $status_counts['Not Started']; ?>, <?php echo $status_counts['In Progress']; ?>, <?php echo $status_counts['Completed']; ?>],
                    backgroundColor: ['#f44336', '#ff9800', '#4caf50'],
                    borderColor: ['#fff', '#fff', '#fff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                animation: {
                    duration: 1500, // Animation duration in milliseconds
                    easing: 'easeInOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ' + tooltipItem.raw + ' tasks';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: <?php echo max($status_counts['Not Started'], $status_counts['In Progress'], $status_counts['Completed']) + 1; ?>
                    }
                }
            }
        });

        // SweetAlert for task completed notification
        <?php if ($status_counts['Completed'] > 0): ?>
            Swal.fire({
                title: 'Task Completed!',
                text: 'You have completed some tasks.',
                icon: 'success',
                confirmButtonText: 'Okay'
            });
        <?php endif; ?>
    </script>
</body>
</html>
