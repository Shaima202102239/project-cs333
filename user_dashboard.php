<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//here
session_start();
include 'config.php';


if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];


if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


$query = "SELECT * FROM bookings WHERE user_id = ? ORDER BY booking_date, time_slot";
$stmt = $conn->prepare($query);


if (!$stmt) {
    die("SQL error: " . $conn->error . "\nQuery: " . $query);
}


$stmt->bind_param('i', $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <div class="container">
        <h3>My Bookings</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['room_no']) ?></td>
                    <td><?= htmlspecialchars($booking['booking_date']) ?></td>
                    <td><?= htmlspecialchars($booking['time_slot']) ?></td>
                    <td>
                        <?= strtotime($booking['booking_date']) >= strtotime('now') ? 'Upcoming' : 'Past' ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
