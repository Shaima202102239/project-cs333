<?php
session_start();
include 'config.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Add Room Functionality
if (isset($_POST['add_room'])) {
    $room_name = htmlspecialchars(trim($_POST['room_name']));
    $capacity = htmlspecialchars(trim($_POST['capacity']));
    $equipment = htmlspecialchars(trim($_POST['equipment']));

    $stmt = $conn->prepare("INSERT INTO rooms (room_name, capacity, equipment) VALUES (?, ?, ?)");
    $stmt->bind_param('sis', $room_name, $capacity, $equipment);
    $stmt->execute();
    $stmt->close();
}

// Edit Room Functionality
if (isset($_POST['edit_room'])) {
    $room_id = $_POST['room_id'];
    $room_name = htmlspecialchars(trim($_POST['room_name']));
    $capacity = htmlspecialchars(trim($_POST['capacity']));
    $equipment = htmlspecialchars(trim($_POST['equipment']));

    $stmt = $conn->prepare("UPDATE rooms SET room_name = ?, capacity = ?, equipment = ? WHERE id = ?");
    $stmt->bind_param('sisi', $room_name, $capacity, $equipment, $room_id);
    $stmt->execute();
    $stmt->close();
}

// Delete Room Functionality
if (isset($_GET['delete_room'])) {
    $room_id = $_GET['delete_room'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    $stmt->bind_param('i', $room_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Rooms for Display
$stmt = $conn->prepare("SELECT * FROM rooms");
$stmt->execute();
$rooms = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h3 class="text-center">Manage Rooms</h3>
        <form method="post">
            <div class="mb-3">
                <label for="room_name" class="form-label">Room Name</label>
                <input type="text" name="room_name" id="room_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity</label>
                <input type="number" name="capacity" id="capacity" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="equipment" class="form-label">Equipment</label>
                <input type="text" name="equipment" id="equipment" class="form-control" required>
            </div>
            <button type="submit" name="add_room" class="btn">Add Room</button>
        </form>

        <h4 >Existing Rooms</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($room = $rooms->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                    <td><?= htmlspecialchars($room['capacity']) ?></td>
                    <td><?= htmlspecialchars($room['equipment']) ?></td>
                    <td>
                        <!-- Edit and Delete buttons-->
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $room['id'] ?>">Edit</button>
                        <a href="?delete_room=<?= $room['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this room?')">Delete</a>
                    </td>
                </tr>

                <!-- Edit Room Modal -->
                <div class="modal fade" id="editModal<?= $room['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Room</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="post">
                                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                    <div class="mb-3">
                                        <label for="room_name" class="form-label">Room Name</label>
                                        <input type="text" name="room_name" value="<?= htmlspecialchars($room['room_name']) ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="capacity" class="form-label">Capacity</label>
                                        <input type="number" name="capacity" value="<?= htmlspecialchars($room['capacity']) ?>" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="equipment" class="form-label">Equipment</label>
                                        <input type="text" name="equipment" value="<?= htmlspecialchars($room['equipment']) ?>" class="form-control" required>
                                    </div>
                                    <button type="submit" name="edit_room" class="btn">Update Room</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
