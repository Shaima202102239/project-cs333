<?php

include 'config.php';
session_start();  // Start the session

if (isset($_POST['submit'])) {

   // Sanitize user inputs to prevent SQL injection
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));  // Encrypt password using MD5

   // Query to check if the user exists with the given email and password
   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   // If user exists, create session and redirect to homepage
   if (mysqli_num_rows($select) > 0) {
      $row = mysqli_fetch_assoc($select);
      $_SESSION['user_id'] = $row['id'];  // Store user ID in session
      $_SESSION['first_name'] = $row['first_name'];  // Store first name in session
      $_SESSION['last_name'] = $row['last_name'];    // Store last name in session (optional)
      $_SESSION['email'] = $row['email'];            // Store email in session (optional)

      header('location: home.php');  // Redirect to homepage upon successful login
   } else {
      $message[] = 'Incorrect email or password!';  // Display error message if credentials don't match
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- Custom CSS file link -->
   <link rel="stylesheet" href="css/style2.css">
</head>
<body>

<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>Login Now</h3>
      
      <?php
      // Display any error messages if they exist
      if (isset($message)) {
         foreach ($message as $message) {
            echo '<div class="message">'.$message.'</div>';
         }
      }
      ?>

      <input type="email" name="email" placeholder="Enter email" class="box" required>
      <input type="password" name="password" placeholder="Enter password" class="box" required>
      <input type="submit" name="submit" value="Login Now" class="btn">
      <p>Don't have an account? <a href="register.php">Register Now</a></p>
   </form>

</div>

</body>
</html>
