<?php
// Start the session and connect to database
session_start();
$conn = new mysqli("localhost", "root", "", "project_management");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload Project Files</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('https://images.unsplash.com/photo-1519389950473-47ba0277781c') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        .upload-container {
            background: rgba(255, 255, 255, 0.95);
            max-width: 500px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-top: 15px;
            color: #333;
            font-weight: 500;
        }
        input[type="text"],
        input[type="date"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }
        button {
            background-color: #4e73df;
            color: white;
            padding: 10px 15px;
            margin-top: 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #3758c5;
        }
    </style>
</head>
<body>

<div class="upload-container">
    <h2>Upload Project Files</h2>
    <form action="upload_project_file.php" method="post" enctype="multipart/form-data">
        <label>Client Name:</label>
        <input type="text" name="client_name" required>

        <label>Project Name:</label>
        <input type="text" name="project_name" required>

        <label>Start Date:</label>
        <input type="date" name="start_date" required>

        <label>End Date:</label>
        <input type="date" name="end_date" required>

        <label>Description:</label>
        <textarea name="description" rows="4" placeholder="Brief description of the project..."></textarea>

        <label>Upload File:</label>
        <input type="file" name="project_file" required>

        <button type="submit" name="upload">Upload Report</button>
    </form>
</div>

</body>
</html>
