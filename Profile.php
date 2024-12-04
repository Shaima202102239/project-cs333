<?php
include 'config.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = []; // Initialize message array

if (isset($_POST['update_profile'])) {
    // Sanitize and validate inputs
    $update_name = htmlspecialchars(trim($_POST['update_name']));
    $update_email = filter_var($_POST['update_email'], FILTER_VALIDATE_EMAIL);

    if ($update_email) {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("UPDATE `user_form` SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param('ssi', $update_name, $update_email, $user_id);
        $stmt->execute();
        $stmt->close();
        $message[] = 'Profile updated successfully!';
    } else {
        $message[] = 'Invalid email address!';
    }

    // Handle password update
    if (!empty($_POST['old_pass']) && !empty($_POST['new_pass']) && !empty($_POST['confirm_pass'])) {
        $old_pass = $_POST['old_pass'];
        $new_pass = $_POST['new_pass'];
        $confirm_pass = $_POST['confirm_pass'];

        // Fetch stored password
        $stmt = $conn->prepare("SELECT password FROM `user_form` WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $stmt->bind_result($stored_pass);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($old_pass, $stored_pass)) {
            if ($new_pass === $confirm_pass) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE `user_form` SET password = ? WHERE id = ?");
                $stmt->bind_param('si', $hashed_pass, $user_id);
                $stmt->execute();
                $stmt->close();
                $message[] = 'Password updated successfully!';
            } else {
                $message[] = 'New password and confirm password do not match!';
            }
        } else {
            $message[] = 'Old password is incorrect!';
        }
    }





