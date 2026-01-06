<?php
// Include the database connection file
$host = 'localhost';
$dbname = 'project_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}

// Fetch client names from the clients table
$query = "SELECT client_id, name FROM clients";
$stmt = $pdo->prepare($query);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST['name'];
    $project_name = $_POST['project_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $image = $_FILES['project_image'];
    $estimated_budget = $_POST['estimated_budget'];

    $query = "SELECT client_id FROM clients WHERE name = :client_name";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['client_name' => $client_name]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client) {
        $client_id = $client['client_id'];

        $image_path = '';
        if ($image['error'] === UPLOAD_ERR_OK) {
            $target_dir = 'uploads/';
            if (!is_dir($target_dir)) mkdir($target_dir);
            $image_path = $target_dir . time() . "_" . basename($image['name']);
            move_uploaded_file($image['tmp_name'], $image_path);
        }

        $insert_query = "INSERT INTO projects (client_id, name, project_name, description, start_date, end_date, image, estimated_budget) 
                         VALUES (:client_id, :name, :project_name, :description, :start_date, :end_date, :image, :estimated_budget)";
        $stmt = $pdo->prepare($insert_query);
        $stmt->execute([
            'client_id' => $client_id,
            'name' => $client_name,
            'project_name' => $project_name,
            'description' => $description,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'image' => $image_path,
            'estimated_budget' => $estimated_budget
        ]);

        echo "<script>alert('Project added successfully!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Project</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('addproject.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.95);
            max-width: 650px;
            width: 90%;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: 600;
            color: #444;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            background-color: #f9f9f9;
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 14px;
            font-size: 18px;
            border-radius: 8px;
            margin-top: 30px;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }

            h1 {
                font-size: 24px;
            }

            button {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Project</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Client Company</label>
            <select id="name" name="name" required>
                <option value="">-- Select Client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= htmlspecialchars($client['name']) ?>"><?= htmlspecialchars($client['name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="project_name">Project Name</label>
            <input type="text" id="project_name" name="project_name" placeholder="Enter project name" required>

            <label for="description">Project Description</label>
            <textarea id="description" name="description" rows="4" placeholder="Brief description about the project..." required></textarea>

            <label for="start_date">Start Date</label>
            <input type="date" id="start_date" name="start_date" required>

            <label for="end_date">End Date</label>
            <input type="date" id="end_date" name="end_date" required>

            <label for="estimated_budget">Estimated Budget (₹)</label>
            <input type="number" id="estimated_budget" name="estimated_budget" placeholder="e.g., 50000" required>

            <label for="project_image">Upload Project Image</label>
            <input type="file" id="project_image" name="project_image" accept="image/*" required>

            <button type="submit">Add Project</button>
        </form>
    </div>
</body>
</html>
