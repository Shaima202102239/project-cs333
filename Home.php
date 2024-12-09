<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Room Booking System</title>
    <script src="js/main.js"></script>
    <script src="js/ajax.js"></script>
</head>
<body>
    <?php
    try {
        session_start();
        include("shared/header.php"); // Include header
        require("database/roomBookingDB.php"); // Database connection
    ?>
    
    <div class="container mt-5">
        <h3 class="text-center mb-4">Building Categories</h3>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php
            // Fetch building categories
            $stmt = $db->prepare("SELECT * FROM building_categories LIMIT 6");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($categories) {
                foreach ($categories as $category) {
                    $name = htmlspecialchars($category["name"]);
                    echo "
                    <div class='col'>
                        <div class='card h-100'>
                            <a href='rooms.php?category_id={$category['id']}' class='text-decoration-none'>
                                <img src='{$category['image_url']}' class='card-img-top' alt='{$name}' />
                                <div class='card-body'>
                                    <h5 class='card-title text-center'>{$name}</h5>
                                </div>
                            </a>
                        </div>
                    </div>";
                }
            } else {
                echo "<p class='text-center'>No categories found</p>";
            }
            ?>
        </div>

        <h3 class="text-center my-4">Available Rooms</h3>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php
            // Fetch available rooms
            $stmt = $db->prepare("SELECT * FROM rooms LIMIT 6");
            $stmt->execute();
            $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rooms) {
                foreach ($rooms as $room) {
                    $name = htmlspecialchars($room["name"]);
                    $capacity = htmlspecialchars($room["capacity"]);
                    $equipment = htmlspecialchars($room["equipment"]);
                    echo "
                    <div class='col'>
                        <div class='card h-100'>
                            <a href='room_details.php?room_id={$room['id']}' class='text-decoration-none'>
                                <img src='{$room['image_url']}' class='card-img-top' alt='{$name}' />
                                <div class='card-body'>
                                    <h5 class='card-title text-center'>{$name}</h5>
                                    <p class='card-text'>Capacity: {$capacity}</p>
                                    <p class='card-text'>Equipment: {$equipment}</p>
                                </div>
                            </a>
                        </div>
                    </div>";
                }
            } else {
                echo "<p class='text-center'>No rooms found</p>";
            }
            ?>
        </div>
    </div>

    <?php
        include("shared/footer.php"); // Include footer
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
    ?>
</body>
<style>
    body {
        background-color: #f8f9fa;
    }
    .card img {
        object-fit: cover;
        height: 200px;
    }
    .card-title {
        font-size: 18px;
        font-weight: bold;
        color: #343a40;
    }
</style>
</html>