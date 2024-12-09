<?php
// Database configuration
$host = 'localhost';
$dbname = 'it_college_rooms3'; // Database name
$username = 'root';
$password = '';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $pdo->exec("USE `$dbname`");

    // Create tables
    // 1. Rooms Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS rooms (
            name VARCHAR(50) PRIMARY KEY
        )
    ");

    // 2. Users Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('admin', 'user') DEFAULT 'user',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
   

    // 3. Admin Actions Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_actions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT NOT NULL,  -- Foreign key to users
            action VARCHAR(255) NOT NULL,
            action_details TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    
    // 4. Bookings Table 
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS bookings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            room_no VARCHAR(20) NOT NULL,
            booking_date DATE NOT NULL,
            time_slot VARCHAR(20) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
        ");

    // 5. Events Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS events (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_name VARCHAR(255) NOT NULL,
            event_date DATE NOT NULL,
            created_by INT NOT NULL, -- Foreign key to users
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
    ");
    

    // 6. Room Usage Table
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS room_usage (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_no VARCHAR(50) DEFAULT NULL,
        usage_count INT DEFAULT 0,
        last_booked TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (room_no) REFERENCES rooms(name) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
");

    

    // Insert default room data
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
    // Handle exceptions
    echo "Database error: " . $e->getMessage();
    exit;
}
return $pdo;
?>