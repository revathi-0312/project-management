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

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $companyName = htmlspecialchars($_POST['company_name']);
    $email = htmlspecialchars($_POST['email']);
    $address = htmlspecialchars($_POST['address']);
    $phoneNumber = isset($_POST['phone_number']) && !empty($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : NULL;
    $plainPassword = $_POST['password'];

    // Validate email and password
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<h2 style='text-align:center; color:red;'>Invalid email format.</h2>";
    } elseif (strlen($plainPassword) < 6) {
        $message = "<h2 style='text-align:center; color:red;'>Password must be at least 6 characters long.</h2>";
    } else {
        // Handle logo upload
        $logoPath = "";
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $targetDir = "uploads/logos/";
            // Create directory if it doesn't exist
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $fileName = basename($_FILES["logo"]["name"]);
            $targetFilePath = $targetDir . uniqid() . "_" . $fileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            // Allow certain file formats
            $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFilePath)) {
                    $logoPath = $targetFilePath;
                } else {
                    $message = "<h2 style='text-align:center; color:red;'>Sorry, there was an error uploading your file.</h2>";
                }
            } else {
                $message = "<h2 style='text-align:center; color:red;'>Only JPG, JPEG, PNG & GIF files are allowed.</h2>";
            }
        } else {
            $message = "<h2 style='text-align:center; color:red;'>Please upload a logo.</h2>";
        }

        if (empty($message)) {
            // Hash the password
            $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

            // Prepare the SQL query to insert into the clients table
            $stmt = $conn->prepare("INSERT INTO clients (name, email, phone, password, address, logo) VALUES (?, ?, ?, ?, ?, ?)");

            if ($stmt === false) {
                $message = "<h2 style='text-align:center; color:red;'>SQL Error: " . $conn->error . "</h2>";
            } else {
                $stmt->bind_param("ssssss", $companyName, $email, $phoneNumber, $hashedPassword, $address, $logoPath);

                if ($stmt->execute()) {
                    $message = "<h2 style='text-align:center; color:green;'>Client added successfully!</h2>";
                } else {
                    $message = "<h2 style='text-align:center; color:red;'>Error: " . $stmt->error . "</h2>";
                }

                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Client</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Your same CSS here (no changes needed except below) */

        body {
            font-family: 'Arial', sans-serif;
            background-image: url('addclientbg2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            max-width: 600px;
            width: 100%;
            padding: 40px 30px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }

        button {
            width: 100%;
            padding: 12px;
            margin: 20px 0;
            border-radius: 8px;
            border: none;
            background-color: #007bff;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }

            input[type="text"],
            input[type="email"],
            input[type="tel"],
            input[type="password"],
            button {
                padding: 10px;
            }
        }

        .message {
            text-align: center;
            margin-top: 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add Client</h1>
        <form method="POST" action="">
            <label for="company_name">Company Name:</label>
            <input type="text" id="company_name" name="company_name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
         
             <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="tel" id="phone_number" name="phone_number">

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
  
             <label for="logo">Company Logo:</label>
              <input type="file" id="logo" name="logo" accept="image/*" required>

            <button type="submit">Add Client</button>
        </form>

        <div class="message">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>
