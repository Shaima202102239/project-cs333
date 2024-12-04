<?php
session_start();
include 'config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Admin Dashboard content
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h3 class="text-center">Admin Dashboard</h3>

        <!-- Admin Navigation -->
        <nav class="nav justify-content-center">
            <a class="nav-link" href="admin_room_management.php">Manage Rooms</a>
            <a class="nav-link" href="admin_schedule_management.php">Manage Schedules</a>
            <a class="nav-link" href="logout.php">Logout</a>
        </nav>

        <!-- Admin Statistics (Example) -->
        <div class="mt-4">
            <h4>Room Usage Statistics</h4>
            <!-- Example of a stats summary, you can modify it based on your needs -->
            <ul>
                <li>Total Rooms: <?php echo getTotalRooms($conn); ?></li>
                <li>Total Bookings: <?php echo getTotalBookings($conn); ?></li>
            </ul>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Example functions for getting statistics
function getTotalRooms($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM rooms");
    $stmt->execute();
    return $stmt->get_result()->fetch_row()[0];
}

function getTotalBookings($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM bookings");
    $stmt->execute();
    return $stmt->get_result()->fetch_row()[0];
}
?>
