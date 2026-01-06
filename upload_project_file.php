<?php
// File upload handler
if (isset($_POST['upload']) && isset($_FILES['project_file'])) {
    $client_name = $_POST['client_name'];
    $project_name = $_POST['project_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];

    $file_name = $_FILES['project_file']['name'];
    $file_tmp = $_FILES['project_file']['tmp_name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

    // Allowed file types
    $allowed_ext = ['pdf', 'doc', 'docx', 'jpg', 'png'];
    if (!in_array(strtolower($file_ext), $allowed_ext)) {
        echo "Invalid file type. Only PDF, DOC, DOCX, JPG, and PNG are allowed.";
        exit();
    }

    // Move the uploaded file to the 'uploads/' directory
    $file_path = 'uploads/' . uniqid() . '.' . $file_ext;
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Save file details in the 'project_reports' table
        $conn = new mysqli("localhost", "root", "", "project_management");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("INSERT INTO project_reports (client_name, project_name, start_date, end_date, description, pdf_path) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $client_name, $project_name, $start_date, $end_date, $description, $file_path);

        if ($stmt->execute()) {
            echo "Project report uploaded and saved successfully!";
        } else {
            echo "Error saving project report to the database.";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "Error uploading file.";
    }
}
?>
