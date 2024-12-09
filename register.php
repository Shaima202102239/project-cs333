<?php

include 'config.php';

//Checks if the "register now" button is clicked. If true, the code processes the form input.
if (isset($_POST['submit'])) {

   //Retrieve and Sanitize Inputs:
   //Retrieves form inputs (name, email, password, confirm password) and escapes special characters using mysqli_real_escape_string() to prevent SQL injection.
   //The md5() function hashes the password and confirm password for security.

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

  //Handle Image Upload:
  //Fetches details about the uploaded image:
//$image: The image file name.
//$image_size: The size of the image in bytes.
//$image_tmp_name: The temporary storage path for the uploaded file.
//$image_folder: The final destination folder for the uploaded image.
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;

   // Validate Email Format:
   //Defines a regular expression to validate email format (must start with 2019 or later years).
//If the email doesn't match the pattern, adds an error message to $message.
   $email_regex = "/^(2019|20[2-9][0-9]).*@.*$/";

//Validate Password and Image:
//Verifies that the password matches the confirmation password.
//Ensures the uploaded image size does not exceed 2 MB.
   if (!preg_match($email_regex, $email)) {
      $message[] = 'Email format is invalid! It should start with 2019 or a year above.';
   } else {
      $select = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email' AND password = '$pass'") or die('query failed');

      if (mysqli_num_rows($select) > 0) {
         $message[] = 'User already exists';
      } else {
         if ($pass != $cpass) {
            $message[] = 'Confirm password not matched!';
         } elseif ($image_size > 2000000) {
            $message[] = 'Image size is too large!';
         } 
         
         //Insert User into Database:
         //Inserts the user data into the user_form table.
//If successful, moves the image to the specified folder and redirects to the login page. If not, displays an error message.

         else {

            $insert = mysqli_query($conn, "INSERT INTO `user_form`(name, email, password, image) VALUES('$name', '$email', '$pass', '$image')") or die('query failed');

            if ($insert) {
               move_uploaded_file($image_tmp_name, $image_folder);
               $message[] = 'Registered successfully!';
               header('location:login.php');
            } else {
               $message[] = 'Registration failed!';
            }
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style2.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post" enctype="multipart/form-data">
      <h3>register now</h3>
      <?php

      //Displays error messages dynamically if the $message array is set.
      if(isset($message)){
         foreach($message as $message){
            echo '<div class="message">'.$message.'</div>';
         }
      }
      ?>
      <!---- Collects user details:
Username, email, password, confirm password, and an optional profile picture.
Ensures input fields are mandatory with the required attribute.--> 
      <input type="text" name="name" placeholder="enter username" class="box" required>
      <input type="email" name="email" placeholder="e.g., 2019abcd@example.com" class="box" required>
      <input type="password" name="password" placeholder="enter password" class="box" required>
      <input type="password" name="cpassword" placeholder="confirm password" class="box" required>
      <input type="file" name="image" class="box" accept="image/jpg, image/jpeg, image/png">
      <!---- Submits the form, triggering the PHP script.-->
      <input type="submit" name="submit" value="register now" class="btn">

      <!--- redirect to login :Provides a link for existing users to go to the login page.-->

      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</div>

</body>
</html>
