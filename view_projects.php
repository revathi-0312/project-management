<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Projects</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Projects</h1>
    <canvas id="projectChart"></canvas>
    <script>
        const ctx = document.getElementById('projectChart').getContext('2d');
        const projects = <?php
            $stmt = $pdo->query("SELECT * FROM projects");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        ?>;

        const labels = projects.map(p => p.project_name);
        const budgets = projects.map(p => p.budget);
        const deadlines = projects.map(p => new Date(p.deadline));
        const currentDate = new Date();

        const backgroundColors = deadlines.map(date => date < currentDate ? 'red' : 'green');

        const projectChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Budget',
                    data: budgets,
                    backgroundColor: backgroundColors,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>