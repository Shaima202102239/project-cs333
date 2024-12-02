<?php
session_start();

// Include the database setup file and get the PDO instance
$pdo = require 'setup_database.php';

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
            <?php endif; ?>

            <form action="" method="POST" class="container">
                <label for="room">Select Room Number:</label>
                <select name="room" id="room" required>
                    <option value="">Choose a room</option>
                    <option value="021">Room 021</option>
                    <option value="023">Room 023</option>
                    <option value="028">Room 028</option>
                    <option value="029">Room 029</option>
                    <option value="030">Room 030</option>
                    <option value="032">Room 032</option>
                    <option value="049">Room 049</option>
                    <option value="051">Room 051</option>
                    <option value="056">Room 056</option>
                    <option value="057">Room 057</option>
                    <option value="058">Room 058</option>
                    <option value="060">Room 060</option>
                    <option value="077">Room 077</option>
                    <option value="079">Room 079</option>
                    <option value="084">Room 084</option>
                    <option value="1006">Room 1006</option>
                    <option value="1008">Room 1008</option>
                    <option value="1010">Room 1010</option>
                    <option value="1011">Room 1011</option>
                    <option value="1012">Room 1012</option>
                    <option value="1014">Room 1014</option>
                    <option value="1047">Room 1047</option>
                    <option value="1048">Room 1048</option>
                    <option value="1050">Room 1050</option>
                    <option value="1086">Room 1086</option>
                    <option value="2007">Room 2007</option>
                    <option value="2010">Room 2010</option>
                    <option value="2011">Room 2011</option>
                    <option value="2048">Room 2048</option>
                    <option value="2051">Room 2051</option>
                    <option value="2087">Room 2087</option>
                    <option value="2091">Room 2091</option>
                </select>

                <label for="date">Select Date:</label>
                <input type="date" name="date" id="date" required>
                <label for="time">Select Time Slot:</label>
                <select name="time" id="time" required>
                    <option value="">Choose the time</option>
                    <option value="08:00-9:00">08:00 AM - 09:00 AM</option>
                    <option value="09:00-10:00">09:00 AM - 10:00 AM</option>
                    <option value="10:00-11:00">10:00 AM - 11:00 AM</option>
                    <option value="11:00-12:00">11:00 AM - 12:00 PM </option>
                    <option value="12:00-01:00">12:00 PM - 01:00 PM </option>
                    <option value="01:00-02:00">01:00 PM - 02:00 PM</option>
                    <option value="02:00-03:00">02:00 PM - 03:00 PM</option>
                    <option value="03:00-04:00">03:00 PM - 04:00 PM </option>
                    <option value="04:00-05:00">04:00 PM - 05:00 PM </option>
                    <option value="05:00-06:00">05:00 PM - 06:00 PM </option>
                </select>

                <button type="submit" class="contrast">Click Here to Book it</button>
            </form>
        </div>
        </body>
        </html>
        <?php
    }
}

// Create an instance of Booking
$booking = new Booking($pdo);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    // Store the booking message in the session
    $_SESSION['booking_message'] = "You have booked a room ".htmlspecialchars($room_id). " on " . htmlspecialchars($date) . " at " . htmlspecialchars($time) . ".";
    
    // Redirect to the same page to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit; // Stop further script execution
}

// Display the booking form
$booking->displayBookingForm();
?>