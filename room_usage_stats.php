<?php
include 'database.php';

//here
$stmt = $pdo->prepare("SELECT * FROM room_usage ORDER BY usage_count DESC");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stm2 = "INSERT INTO room_usage (room_no, usage_count, last_booked) VALUES ('ROOM 028', 1, '2024-12-08 17:56:10'), 
('ROOM 030', 1, '2024-12-08 17:58:29')" ;

$pdo->exec($stm2); 
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
                <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_no']) ?></td>
                    <td><?= htmlspecialchars($room['usage_count']) ?></td>
                    <td><?= htmlspecialchars($room['last_booked']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
