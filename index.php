<?php
// Start the session
session_start();

// Database connection parameters
$servername = 'localhost'; // Change if your database is hosted elsewhere
$dbname = 'project_management';
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }
        header {
            background-color: rgba(0, 123, 255, 0.8); /* Semi-transparent header */
            color: white;
            padding: 20px;
            text-align: center;
            position: fixed; /* Fixed position for header */
            width: 100%;
            z-index: 1000; /* Ensure header is above other content */
        }
        .hero {
            height: 100vh; /* Full viewport height */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden; /* Hide overflow for scrolling effect */
        }
        .hero h1 {
            font-size: 3em; /* Increased font size for better visibility */
            margin: 0;
        }
        .hero p {
            font-size: 1.5em; /* Increased font size for better visibility */
            margin: 20px 0;
        }
        .login-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
            position: relative;
            z-index: 2; /* Bring buttons above the overlay */
        }
        .login-button {
            margin: 0 10px;
            padding: 15px 30px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 1.1em; /* Increased font size for buttons */
        }
        .login-button:hover {
            background-color: #0056b3;
        }
        
        /* Overlay for better text visibility */
        .hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* Dark overlay */
            z-index: 1;
        }
        .hero h1, .hero p {
            position: relative;
            z-index: 2; /* Bring text above the overlay */
        }
        .image-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            animation: scroll 10s linear infinite; /* Animation for scrolling */
        }
        .image-slider img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Cover the entire area */
        }
        @keyframes scroll {
            0% { transform: translateY(0); }
            25% { transform: translateY(-100%); }
            50% { transform: translateY(-100%); }
            75% { transform: translateY(-200%); }
            100% { transform: translateY(-200%); }
        }
        .content {
            padding: 20px;
            text-align: center;
            background-color: #fff; /* White background for content */
            margin-top: 20px; /* Space below the hero section */
            border-radius: 5px; /* Rounded corners */
            box-shadow: 0 0 10px rgba(            0, 0, 0, 0.1); /* Subtle shadow */
        }
    </style>
</head>
<body>

<header>
    <h1>Project Management System</h1>
</header>

<div class="hero">
    <div class="image-slider">
        <img src="image1.jpg" alt="Image 1"> <!-- Replace with your image URLs -->
        <img src="image2.jpg" alt="Image 2">
        <img src="image3.jpg" alt="Image 3">
        <img src="image4.jpg" alt="Image 4">
        <img src="image5.jpg" alt="Image 5">
    </div>
    <h1>Welcome</h1>
    <p>Manage your projects efficiently and effectively.</p>
    <div class="login-container">
        <a href="client_login.php" class="login-button">Login as Client</a>
        <a href="admin_login.php" class="login-button">Login as Admin</a>
    </div>
</div>

<div class="content">
    <h2>About Us</h2>
    <p>Our Project Management System helps teams collaborate and manage their projects effectively. With features like task assignment, progress tracking, and reporting, we ensure that your projects are completed on time and within budget.</p>
</div>

</body>
</html>