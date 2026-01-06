<?php
// Database connection parameters
$servername = 'localhost'; // Change if your database is hosted elsewhere
$username = 'root'; // Replace with your database username
$password = ''; // Replace with your database password
$dbname = "project_management";

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$servername", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    // Create the database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname ");
    echo "Database created successfully.<br>";

    // Use the created database
    $pdo->exec("USE project_management");
     
      $pdo->exec("CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)");
       

      $pdo->exec("CREATE TABLE finished_projects (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    details TEXT NOT NULL
)");


    // Create the clients table
    $pdo->exec("CREATE TABLE IF NOT EXISTS clients (
        client_id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(15),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'clients' created successfully.<br>";

    // Create the projects table
    $pdo->exec("CREATE TABLE IF NOT EXISTS projects (
        project_id INT AUTO_INCREMENT PRIMARY KEY,
        client_id INT NOT NULL,
        project_name VARCHAR(100) NOT NULL,
        description TEXT,
        start_date DATE,
        end_date DATE,
        FOREIGN KEY (client_id) REFERENCES clients(client_id) ON DELETE CASCADE
    )");
    echo "Table 'projects' created successfully.<br>";

    // Create the tasks table
    $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
        task_id INT AUTO_INCREMENT PRIMARY KEY,
        project_id INT NOT NULL,
        task_name VARCHAR(100) NOT NULL,
        description TEXT,
        assigned_to VARCHAR(100),
        due_date DATE,
        status ENUM('Not Started', 'In Progress', 'Completed') DEFAULT 'Not Started',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
    )");
    echo "Table 'tasks' created successfully.<br>";

    

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the connection
$pdo = null;
?>