<?php
session_start();
include 'config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle new admin creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    // Sanitize input to prevent XSS and other injection attacks
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];
    $role = 'admin';  // Fixed role as 'admin'

    // Validate username and password
    if (empty($username) || empty($password)) {
        echo "Username and password cannot be empty.";
        exit();
    }

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Insert the new admin into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hashed_password, $role]);

        // Redirect or show a success message
        header('Location: admin_dashboard.php?success=1');
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Admin accessing the dashboard
$admin_id = $_SESSION['user_id'];  // Assuming the admin's ID is stored in the session
$action = 'Accessed Admin Dashboard';
$action_details = 'Admin viewed the dashboard page.';
$stmt = $pdo->prepare("INSERT INTO admin_actions (admin_id, action, action_details) 
                       VALUES (?, ?, ?)");
$stmt->execute([$admin_id, $action, $action_details]);

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

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <p style="color: green;">New admin has been successfully added!</p>
        <?php endif; ?>
        
        <h2>Add New Admin</h2>
        <form action="admin_dashboard.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="add_admin" class="btn">Add Admin</button>
        </form>
    </div>
</body>
</html>
