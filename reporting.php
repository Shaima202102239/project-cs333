<?php
// Include the database connection (which uses PDO)
include 'database.php';

// Initialize reports as null to prevent errors outside of POST request
$reports = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    
    // Prepare the query with placeholders
    $stmt = $pdo->prepare("SELECT room_no, COUNT(*) AS bookings FROM bookings WHERE booking_date BETWEEN :start_date AND :end_date GROUP BY room_no");
    
    // Bind the parameters to prevent SQL injection
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    
    // Execute the statement
    $stmt->execute();
    
    // Fetch the result
    $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                <?php if ($reports && count($reports) > 0): ?>
                    <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?= htmlspecialchars($report['room_no']) ?></td>
                        <td><?= htmlspecialchars($report['bookings']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2">No bookings found for the selected dates.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
