<?php
session_start();

$pdo = require 'new_data.php';

class Booking {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getRooms() {
        // Fetch available rooms from the database
        $stmt = $this->pdo->query("SELECT id, name FROM rooms");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function displayBookingForm() {
        $rooms = $this->getRooms();
        ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Booking Form</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
        </head>
        <body>

        <div class="container" style="max-width: 400px; margin: auto; padding: 20px;">
            <h2>Reserve a Room</h2>

            <?php if (isset($_SESSION['booking_message'])): ?>
                <div class="alert alert-success">
                    <?php
                    echo htmlspecialchars($_SESSION['booking_message']);
                    unset($_SESSION['booking_message']); // Clear the message after displaying
                    ?>
                </div>
                <form action="" method="POST" class="container" style="margin-top: 20px;">
                    <input type="hidden" name="cancel" value="true">
                    <button type="submit" class="alert">Cancel the Booking!</button>
                </form>
            <?php endif; ?>

            <form action="" method="POST" class="container">
                <label for="room">Select Room Number:</label>
                <select name="room" id="room" required>
                    <option value="">Choose a room</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?php echo htmlspecialchars($room['name']); ?>">
                            <?php echo htmlspecialchars($room['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="date">Select Date:</label>
                <input type="date" name="date" id="date" required>
                <label for="time">Select Time Slot:</label>
                <select name="time" id="time" required>
                    <option value="">Choose the time</option>
                    <option value="08:00-09:00">08:00 AM - 09:00 AM</option>
                    <option value="09:00-10:00">09:00 AM - 10:00 AM</option>
                    <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                    <option value="11:00-12:00">11:00 AM - 12:00 PM</option>
                    <option value="12:00-01:00">12:00 PM - 01:00 PM</option>
                    <option value="01:00-02:00">01:00 PM - 02:00 PM</option>
                    <option value="02:00-03:00">02:00 PM - 03:00 PM</option>
                    <option value="03:00-04:00">03:00 PM - 04:00 PM</option>
                    <option value="04:00-05:00">04:00 PM - 05:00 PM</option>
                    <option value="05:00-06:00">05:00 PM - 06:00 PM</option>
                </select>

                <button type="submit" name="book" class="contrast">Click Here to Book it</button>
            </form>
        </div>
        </body>
        </html>
        <?php
    }

    public function bookRoom($room_id, $date, $time) {
        // the SQL statement to insert into the booking table
        $stmt = $this->pdo->prepare("INSERT INTO bookings VALUES ($room_id, $date, $time)");

        // Store the booking details in the session for cancellation
        $_SESSION['room_id'] = $room_id;
        $_SESSION['date'] = $date;
        $_SESSION['time'] = $time;
    }


    public function checkForConflict($room_id, $start_time, $end_time, $date) {
        // Prepare the query
        $sql = "SELECT * FROM bookings 
                WHERE room_id = :room_id 
                AND date = :date 
                AND (time_slot LIKE :time_slot)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindValue(':time_slot', $start_time . '%', PDO::PARAM_STR);
        
        // Check if any rows are returned
        if ($stmt->rowCount() > 0) {
            return false; // It means the room is not available
        }
        return true; // it will mean the room is available
    }

}

$booking = new Booking($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['book'])) {
        $room_id = $_POST['room'];
        $date = $_POST['date'];
        $time = $_POST['time'];
            
        // Split time into start and end
        list($start_time, $end_time) = explode('-', $time);
    
        // Check if there is any conflict 
        if ($booking->checkForConflict($room_id, $start_time, $end_time, $date)) {
            // Call the method to book the room
            $booking->bookRoom($room_id, $date, $time);
            $_SESSION['booking_message'] = "You have booked room " . htmlspecialchars($room_id) . " on " . htmlspecialchars($date) . " at " . htmlspecialchars($time) . ".";
        } else {
            $_SESSION['booking_message'] = "Conflict detected: The room is already booked for this time!";
            }
            
        // Redirect to the same page 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; 
        } 
    if (isset($_POST['cancel'])) {
        unset($_SESSION['booking_message']); // Clear the booking message
        $_SESSION['booking_message'] = "Your booking has been canceled.";
        
        // Redirect to the same page 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; 
    }

}

// Display the booking form
$booking->displayBookingForm();
?>