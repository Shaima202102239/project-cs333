<?php
include 'config.php';


//here
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $stmt = $conn->prepare("SELECT room_no, COUNT(*) AS bookings FROM bookings WHERE booking_date BETWEEN ? AND ? GROUP BY room_no");
    $stmt->bind_param('ss', $start_date, $end_date);
    $stmt->execute();
    $reports = $stmt->get_result();
    $stmt->close();
} else {
    $reports = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <div class="container">
        <h3>Room Booking Reports</h3>
        <form method="POST">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            <button type="submit" class="btn">Generate Report</button>
        </form>
        <h4>Report Results</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Bookings</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($report = $reports->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($report['room_no']) ?></td>
                    <td><?= htmlspecialchars($report['bookings']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
