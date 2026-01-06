<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_name = $_POST['project_name'];
    $company_name = $_POST['company_name'];
    $description = $_POST['description'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $pdo->prepare("INSERT INTO projects (project_name, company_name, description, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$project_name, $company_name, $description, $start_date, $end_date]);
}

header("Location: view_projects.php");
exit();
?>