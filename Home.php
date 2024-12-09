<?php
session_start(); 


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  
    exit();
}

// Fetch session data
$first_name = $_SESSION['first_name'];  
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>
   
   <link rel="stylesheet" href="css/style2.css">
</head>
<body>

<!-- Header Section -->
<header>
   <div class="header-container">
      <div class="logo">
        
      </div>
      <div class="nav-links">
         <ul>
            <li><a href="booking.php">Book a Room</a></li>  
            <li><a href="profile.php">Profile</a></li>      
            <li><a href="logout.php">Logout</a></li>        
         </ul>
      </div>
   </div>
</header>

<!-- Main Content Section -->
<main>
   <div class="welcome-message">
   <h1>Welcome to the Room Booking System</h1>
      <p>Easily book rooms, manage your profile, and access all features with just a few clicks.<?php echo htmlspecialchars($first_name); ?></p>
     
   </div>
</main>

<!-- Footer Section -->
<footer>
   <div class="footer-container">
      <p>&copy; 2024 Your Website Name. All Rights Reserved.</p>
   </div>
</footer>

</body>
</html>
