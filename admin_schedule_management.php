<?php
session_start();
include 'config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();

}
//fetch schedule from the database 
$stmt = $conn->prepare("SELECT * FROM schedule");
$stmt->execute();
$schedules= $stmt->get_result();
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h3>Manage Schedule</h3>
        <table class="table">
        <thead>
                <tr>
                    <th>Room</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($schedule=$schedules->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($schedule['room_no']) ?></td>
                        <td><?= htmlspecialchars($schedule['booking_date']) ?></td>
                        <td><?= htmlspecialchars($schedule['time_slot']) ?></td>
                        <td>
                            <a href="?delete_schedule=<?= $schedule['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this schedule?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</body>
</html>