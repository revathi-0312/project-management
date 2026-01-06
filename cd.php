<?php
// Assuming a session is started and client_id is available
session_start();
$client_id = $_SESSION['client_id']; // Get client ID from session (make sure client_id is set)

// Database connection (ensure this matches your connection details)
$conn = new mysqli('localhost', 'root', '', 'project_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch notifications for the client
$notifications = [];
$notification_query = $conn->query("SELECT * FROM notifications WHERE client_id = $client_id ORDER BY created_at DESC LIMIT 5");

while ($row = $notification_query->fetch_assoc()) {
    $notifications[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - Notifications</title>
    <style>
        /* Notification Styles */
        .notification-container {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .notification-header {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .notification {
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            border-left: 5px solid #4caf50;
            font-size: 14px;
        }

        .notification.new {
            background-color: #e7f3e7;
            border-left: 5px solid #388e3c;
        }

        .notification .date {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="notification-container">
        <div class="notification-header">
            Notifications
        </div>

        <?php if (count($notifications) > 0): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification <?php echo (strtotime($notification['created_at']) > strtotime('-1 day')) ? 'new' : ''; ?>">
                    <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    <div class="date"><?php echo date('F j, Y, g:i A', strtotime($notification['created_at'])); ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No new notifications</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
