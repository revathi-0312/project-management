<?php
// Database connection parameters
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'project_management';

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $company = $_POST['company'];
        $deployed_date = $_POST['deployed_date'];
        $image = $_POST['image'];
        $details = $_POST['details'];

        // Insert finished project into the database
        $stmt = $pdo->prepare("INSERT INTO finished_projects (name, company, deployed_date, image, details) VALUES (:name, :company, :deployed_date, :image, :details)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':company', $company);
        $stmt->bindParam(':deployed_date', $deployed_date);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':details', $details);
        $stmt->execute();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$pdo = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Finished Project</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('finish.png'); /* Background image */
            background-size: cover; /* Cover the entire page */
            background-position: center; /* Center the image */
            color: #333; /* Default text color */
        }
        .container {
            max-width: 600px; /* Max width for the container */
            margin: 0 auto; /* Center the container */
            padding: 20px; /* Padding around the container */
            background-color: rgba(255, 255, 255, 0.9); /* White background with transparency */
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2); /* Shadow effect */
        }
        h2 {
            text-align: center; /* Center the heading */
            color: #007BFF; /* Heading color */
        }
        form {
            margin-bottom: 20px; /* Space below the form */
        }
        input, textarea, button {
            width: 100%; /* Full width */
            padding: 10px; /* Padding inside inputs */
            margin: 10px 0; /* Margin around inputs */
            border: 1px solid #ccc; /* Border color */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Font size */
        }
        button {
            background-color: #007BFF; /* Button color */
            color: white; /* Button text color */
            border: none; /* No border */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Transition effect */
        }
        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .projects-container {
            display: flex; /* Flexbox for layout */
            flex-wrap: wrap; /* Wrap items */
            gap: 20px; /* Space between project boxes */
            justify-content: center; /* Center the project boxes */
        }
        .project-box {
            border: 1px solid #ccc; /* Border color */
            border-radius: 5px; /* Rounded corners */
            padding: 10px; /* Padding inside project box */
            width: calc(33.333% - 20px); /* Responsive width */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Shadow effect */
            background-color: #fff; /* White background */
            transition: transform 0.2s; /* Transition effect */
        }
        .project-box:hover {
            transform: scale(1.02); /* Scale effect on hover */
        }
        .project-box img {
            max-width: 100            max-width: 100%; /* Ensure the image fits within the box */
            border-radius: 5px; /* Rounded corners for images */
        }
        .project-details {
            margin-top: 10px; /* Space above project details */
            padding: 10px; /* Padding inside project details */
            border: 1px solid #eaeaea; /* Border color */
            border-radius: 5px; /* Rounded corners */
            background-color: #f9f9f9; /* Light background for details */
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add Finished Project</h2>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Project Name" required>
        <input type="text" name="company" placeholder="Company Name" required>
        <input type="date" name="deployed_date" required>
        <input type="text" name="image" placeholder="Image URL" required>
        <textarea name="details" placeholder="Project Details" required></textarea>
        <button type="submit">Add Finished Project</button>
    </form>

    

<script>
    function showDetails(projectId) {
        const projectDetails = document.getElementById(projectId);
        projectDetails.style.display = projectDetails.style.display === 'block' ? 'none' : 'block';
    }
</script>

</body>
</html>