<?php
// Database connection parameters
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

// Fetch clients
$clients = [];
$clientResult = $conn->query("SELECT * FROM clients");
if ($clientResult->num_rows > 0) {
    while ($row = $clientResult->fetch_assoc()) {
        $clients[] = $row;
    }
}

// Fetch ongoing projects
$ongoing_projects = [];
$ongoingResult = $conn->query("
    SELECT p.*, c.name AS client_name
    FROM projects p
    JOIN clients c ON p.client_id = c.client_id
    WHERE p.end_date > CURDATE()
");
if ($ongoingResult->num_rows > 0) {
    while ($row = $ongoingResult->fetch_assoc()) {
        $ongoing_projects[] = $row;
    }
}

// Fetch finished projects
$finished_projects = [];
$finishedResult = $conn->query("
    SELECT p.*, c.name AS client_name
    FROM projects p
    JOIN clients c ON p.client_id = c.client_id
    WHERE p.end_date <= CURDATE()
");
if ($finishedResult->num_rows > 0) {
    while ($row = $finishedResult->fetch_assoc()) {
        $finished_projects[] = $row;
    }
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client and Project Information</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 20px;
            background-color: #eef2f3;
        }
        .client-section {
            background-color: #ffffff;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            transition: transform 0.3s;
        }
        .client-section:hover {
            transform: scale(1.02);
        }
        .client-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .company-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 50%;
            margin-right: 20px;
            border: 2px solid #007bff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .company-name {
            font-size: 24px;
            color: #007bff;
            margin: 0;
        }
        .client-details p {
            margin: 5px 0;
            color: #555;
        }
        .project-list {
            margin-top: 20px;
        }
        .project-list h3 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 5px;
        }
        .project-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .project-item h4 {
            margin: 0 0 10px 0;
            color: #007bff;
        }
        .project-item p {
            margin: 5px 0;
            color: #333;
        }
    </style>
</head>
<body>

<?php foreach ($clients as $client): ?>
    <div class="client-section">
        <div class="client-header">
            <img src="<?php echo htmlspecialchars($client['logo']); ?>" alt="Company Logo" class="company-logo">
            <h2 class="company-name"><?php echo htmlspecialchars($client['name']); ?></h2>
        </div>
        <div class="client-details">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($client['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($client['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($client['address']); ?></p>
                    </div>

        <!-- Ongoing Projects -->
        <div class="project-list">
            <h3>Ongoing Projects</h3>
            <?php
            $hasOngoing = false;
            foreach ($ongoing_projects as $project) {
                if ($project['client_id'] == $client['client_id']) {
                    $hasOngoing = true;
                    ?>
                    <div class="project-item">
                        <h4><?= htmlspecialchars($project['project_name']) ?></h4>
                        <p><strong>Start Date:</strong> <?= htmlspecialchars($project['start_date']) ?></p>
                        <p><strong>End Date:</strong> <?= htmlspecialchars($project['end_date']) ?></p>
                        <p><?= htmlspecialchars($project['description']) ?></p>
                    </div>
                    <?php
                }
            }
            if (!$hasOngoing) {
                echo "<p>No ongoing projects.</p>";
            }
            ?>
        </div>

        <!-- Finished Projects -->
        <div class="project-list">
            <h3>Finished Projects</h3>
            <?php
            $hasFinished = false;
            foreach ($finished_projects as $project) {
                if ($project['client_id'] == $client['client_id']) {
                    $hasFinished = true;
                    ?>
                    <div class="project-item">
                        <h4><?= htmlspecialchars($project['project_name']) ?></h4>
                        <p><strong>End Date:</strong> <?= htmlspecialchars($project['end_date']) ?></p>
                        <p><?= htmlspecialchars($project['description']) ?></p>
                    </div>
                    <?php
                }
            }
            if (!$hasFinished) {
                echo "<p>No finished projects.</p>";
            }
            ?>
        </div>
    </div>
<?php endforeach; ?>

</body>
</html>
