<?php
include 'config.php';

//here
$stmt = $conn->prepare("SELECT * FROM room_usage ORDER BY usage_count DESC");
$stmt->execute();
$rooms = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Usage Statistics</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <div class="container">
        <h3>Room Usage Statistics</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Usage Count</th>
                    <th>Last Booked</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($room = $rooms->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_no']) ?></td>
                    <td><?= htmlspecialchars($room['usage_count']) ?></td>
                    <td><?= htmlspecialchars($room['last_booked']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
