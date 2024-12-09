<?php

include 'config.php';
session_start();

// check if the form is submitted 
if(isset($_POST['submit'])){

   //This retrieves the email entered by the user in the form and sanitizes it to prevent SQL injection & same goes for the password, it use md5()function to hash the password (for security).
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

   $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');
   
//checks if the email and password match a user in the database 
   if(mysqli_num_rows($select) > 0){
      $row = mysqli_fetch_assoc($select);
      // it stores the user's ID in the session and redirects to the homepage.
      $_SESSION['user_id'] = $row['id'];
      header('location:homepage.html');
      // if no user is found
   }else{
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style2.css">

</head>
<body>
   
<div class="form-container">
<!--collects the user's email and password and submits via POST---> 
   <form action="" method="post" enctype="multipart/form-data">
      <h3>login now</h3>
      <?PHP
//If no user is found, an error message (incorrect email or password!) is displayed
      if(isset($message)){
         foreach($message as $message){
            echo '<div class="message">'.$message.'</div>';
         }
      }
      ?>
      <input type="email" name="email" placeholder="enter email" class="box" required>
      <input type="password" name="password" placeholder="enter password" class="box" required>
      
      <input type="submit" name="submit" value="login now" class="btn">
      <p> Don't have an account? <a href="register.php">regiser now</a></p>
   </form>

</div>

</body>
</html>
