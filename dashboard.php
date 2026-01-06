<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "project_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$projects = $conn->query("SELECT project_id, project_name, start_date, end_date, estimated_budget, actual_spent, budget_status FROM projects");


if ($projects === false) {
    die("Query failed: " . $conn->error);
}

// Prepare data for charts
$status_counts = ["Not Started" => 0, "In Progress" => 0, "Completed" => 0];
$project_list = [];

while ($row = $projects->fetch_assoc()) {
    $start = new DateTime($row['start_date']);
    $end = new DateTime($row['end_date']);
    $now = new DateTime();

    // Determine project status
    if ($now < $start) {
        $status = "Not Started";
    } elseif ($now >= $start && $now <= $end) {
        $status = "In Progress";
    } else {
        $status = "Completed";
    }

    $status_counts[$status]++;
    $row['status'] = $status;

    // Calculate time spent
    $interval = $start->diff($end);
    $days = $interval->days;
    $hours = $interval->h;
    $minutes = $interval->i;
    $row['time_spent'] = "{$days}d ";

    $project_list[] = $row;
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Project Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .projects-container {
            display: flex;
            flex-wrap: wrap;
        }
        .project-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            width: 200px;
        }
    </style>
</head>
<body>
    <h1>Project Dashboard</h1>

    <div class="projects-container">
    <?php foreach($project_list as $project): ?>
        <div class="project-box">
            <h3>
                <a href="task_dashboard.php?project_id=<?php echo $project['project_id']; ?>">
                    <?php echo htmlspecialchars($project['project_name']); ?>
                </a>
            </h3>
            <p><strong>Status:</strong> <?php echo $project['status']; ?></p>
            <p><strong>Time Spent:</strong> <?php echo $project['time_spent']; ?></p>
          <p><strong>Estimated Budget:</strong> ₹<?php echo number_format($project['estimated_budget'], 2); ?></p>
           <p><strong>Actual Spent:</strong> ₹<?php echo number_format($project['actual_spent'], 2); ?></p>
           <p><strong>Budget Status:</strong> <?php echo $project['budget_status']; ?></p>


        </div>
    <?php endforeach; ?>
    </div>

    <h2>Project Status Overview</h2>
    <canvas id="statusChart" width="400" height="200"></canvas>
    <script>
    const ctx = document.getElementById('statusChart').getContext('2d');

    // Create beautiful gradients
    const gradientYellow = ctx.createLinearGradient(0, 0, 0, 400);
    gradientYellow.addColorStop(0, 'rgba(255, 206, 86, 0.9)');
    gradientYellow.addColorStop(1, 'rgba(255, 206, 86, 0.3)');

    const gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
    gradientBlue.addColorStop(0, 'rgba(54, 162, 235, 0.9)');
    gradientBlue.addColorStop(1, 'rgba(54, 162, 235, 0.3)');

    const gradientGreen = ctx.createLinearGradient(0, 0, 0, 400);
    gradientGreen.addColorStop(0, 'rgba(75, 192, 192, 0.9)');
    gradientGreen.addColorStop(1, 'rgba(75, 192, 192, 0.3)');

    const statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Not Started', 'In Progress', 'Completed'],
            datasets: [{
                label: 'Number of Projects',
                data: [
                    <?php echo $status_counts['Not Started']; ?>,
                    <?php echo $status_counts['In Progress']; ?>,
                    <?php echo $status_counts['Completed']; ?>
                ],
                backgroundColor: [gradientYellow, gradientBlue, gradientGreen],
                borderColor: ['#ffc107', '#36a2eb', '#4bc0c0'],
                borderWidth: 2,
                borderRadius: 15,
                hoverBorderColor: '#000',
                hoverBorderWidth: 3
            }]
        },
        options: {
            animation: {
                duration: 2000,
                easing: 'easeOutElastic'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0,
                    ticks: {
                        color: '#333',
                        font: {
                            size: 14
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        color: '#333',
                        font: {
                            size: 14
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        color: '#444'
                    }
                },
                tooltip: {
                    backgroundColor: '#343a40',
                    titleColor: '#f8f9fa',
                    bodyColor: '#f8f9fa',
                    borderColor: '#6c757d',
                    borderWidth: 1
                }
            }
        }
    });
    </script>
</body>
</html>

