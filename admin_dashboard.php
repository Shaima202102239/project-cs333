<?php
session_start();
include 'config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link  rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h1 >Admin Dashboard</h1>
        <div class="dashboard-options">
            <a href="admin_room_management.php" class="btn">Manage Rooms</a>
            <a href="admin_schedule_management.php" class="btn">Manage Schedule</a>
        </div>
    </div>
</body>
</html>
