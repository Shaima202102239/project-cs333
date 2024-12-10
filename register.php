<?php
include 'config.php';
session_start();

$message = [];

if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, password_hash($_POST['password'], PASSWORD_BCRYPT)); // Secure password hash
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($confirm_password, $password)) {
        $check_user = mysqli_query($conn, "SELECT * FROM `user_form` WHERE email = '$email'") or die('Query failed');
        if (mysqli_num_rows($check_user) > 0) {
            $message[] = 'User with this email already exists!';
        } else {
            $insert_user = mysqli_query($conn, "INSERT INTO `user_form` (first_name, last_name, email, password) VALUES ('$first_name', '$last_name', '$email', '$password')") or die('Query failed');
            if ($insert_user) {
                $message[] = 'Registration successful! You can now log in.';
                header('location:login.php');
            } else {
                $message[] = 'Registration failed. Please try again.';
            }
        }
    } else {
        $message[] = 'Passwords do not match!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/style2.css">
</head>
<body>
<div class="form-container">
    <form action="" method="post">
        <h3>Register Now</h3>
        <?php
        if (!empty($message)) {
            foreach ($message as $msg) {
                echo '<div class="message">' . $msg . '</div>';
            }
        }
        ?>
        <input type="text" name="first_name" placeholder="Enter first name" class="box" required>
        <input type="text" name="last_name" placeholder="Enter last name" class="box" required>
        <input type="email" name="email" placeholder="Enter email" class="box" required>
        <input type="password" name="password" placeholder="Enter password" class="box" required>
        <input type="password" name="confirm_password" placeholder="Confirm password" class="box" required>
        <input type="submit" name="submit" value="Register Now" class="btn">
        <p>Already have an account? <a href="login.php">Login Now</a></p>
    </form>
</div>
</body>
</html>
