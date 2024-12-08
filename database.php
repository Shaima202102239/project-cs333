<?php
// Database configuration
$host = 'localhost'; 
$dbname = 'it_college_room_booking'; // Database name
$username = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");


    // rooms table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rooms (
            name VARCHAR(50) PRIMARY KEY
        )
    ");


   // reserved rooms table table 
$pdo->exec("
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_no VARCHAR(20) NOT NULL,
    booking_date DATE NOT NULL,
    time_slot VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");


// Inserting the rooms in the rooms table
$stmt = $pdo->query("SELECT COUNT(*) FROM rooms");
if ($stmt->fetchColumn() == 0) {
    $pdo->exec("
        INSERT INTO rooms (name) VALUES 
        ('Room 021'),
        ('Room 023'),
        ('Room 028'),
        ('Room 029'),
        ('Room 030'),
        ('Room 032'),
        ('Room 049'),
        ('Room 051'),
        ('Room 056'),
        ('Room 057'),
        ('Room 058'),
        ('Room 060'),       
        ('Room 077'),
        ('Room 079'),
        ('Room 084'),
        ('Room 1006'),
        ('Room 1008'),
        ('Room 1010'),
        ('Room 1011'),
        ('Room 1012'),
        ('Room 1014'),
        ('Room 1047'),
        ('Room 1048'),
        ('Room 1050'),
        ('Room 1086'),
        ('Room 2007'),
        ('Room 2010'),
        ('Room 2011'),
        ('Room 2048'),
        ('Room 2051'),   
        ('Room 2087'), 
        ('Room 2091')
    ");
}


} catch (PDOException $e) {
    // Handle connection error
    echo "Error: " . $e->getMessage();
    exit; // Stop further execution
}

return $pdo;
?>