<?php
session_start();

$pdo = require 'database.php';

class Booking {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getRooms() {
        // Fetch available rooms from the database and then we'll use them in the form 
        $stmt = $this->pdo->query("SELECT name FROM rooms");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function displayBookingForm() {
        $rooms = $this->getRooms();
        ?>

        <!-- The booking Form -->
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
                    unset($_SESSION['booking_message']);
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
        // Prepare and execute the SQL statement to insert the reservation into the booking table
        $stmt = $this->pdo->prepare("INSERT INTO bookings (room_no, booking_date, time_slot) VALUES (:room_no, :booking_date, :time_slot)");
        
        $stmt->bindParam(':room_no', $room_id, PDO::PARAM_STR); // Assuming room_no is stored as a string
        $stmt->bindParam(':booking_date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':time_slot', $time, PDO::PARAM_STR);

        $stmt->execute();
        
        $_SESSION['room_id'] = $room_id;
        $_SESSION['date'] = $date;
        $_SESSION['time'] = $time;
    }

    public function checkForConflict($room_id, $time, $date) {
        // Prepare the query to check for time conflicts
        $sql = "SELECT * FROM bookings 
                WHERE room_no = :room_id 
                AND booking_date = :date 
                AND time_slot = :time";

        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_STR); 
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();
    
        // Check if any rows are returned
        return $stmt->rowCount() === 0; // if there rowCount =0 it means it's available for booking
    }

    public function cancelBooking ($room_id, $date, $time){
        $sql = "DELETE FROM bookings WHERE room_no = :room_id 
                AND booking_date = :date 
                AND time_slot = :time";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->bindParam(':room_id', $room_id, PDO::PARAM_STR); 
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->bindParam(':time', $time, PDO::PARAM_STR);
        $stmt->execute();

    }

}

$booking = new Booking($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['book'])) {
        $room_id = $_POST['room'];
        $date = $_POST['date'];
        $time = $_POST['time'];
    
        // Check for availability
        if ($booking->checkForConflict($room_id, $time, $date)) {
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
        // Retrieve booking details from session
        if (isset($_SESSION['room_id'], $_SESSION['date'], $_SESSION['time'])) {
            $room_id = $_SESSION['room_id'];
            $date = $_SESSION['date'];
            $time = $_SESSION['time'];
            
            // Cancel the booking
            $booking->cancelBooking($room_id, $date, $time);
            unset($_SESSION['room_id'], $_SESSION['date'], $_SESSION['time']); 
            $_SESSION['booking_message'] = "Your booking has been canceled.";
        } else {
            $_SESSION['booking_message'] = "No booking to cancel.";
        }
        
        // Redirect to the same page 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit; 
    }
}

// Display the booking form
$booking->displayBookingForm();
?>