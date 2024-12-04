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

    // Handle image upload
    if (!empty($_FILES['update_image']['name'])) {
        $allowed_types = ['image/jpg', 'image/jpeg', 'image/png'];
        $update_image = $_FILES['update_image'];
        $image_type = mime_content_type($update_image['tmp_name']);
        $image_size = $update_image['size'];
        $image_name = uniqid() . '-' . basename($update_image['name']);
        $upload_dir = 'uploaded_img/' . $image_name;

        if (in_array($image_type, $allowed_types)) {
            if ($image_size <= 2000000) {
                if (move_uploaded_file($update_image['tmp_name'], $upload_dir)) {
                    $stmt = $conn->prepare("UPDATE `user_form` SET image = ? WHERE id = ?");
                    $stmt->bind_param('si', $image_name, $user_id);
                    $stmt->execute();
                    $stmt->close();
                    $message[] = 'Image updated successfully!';
                } else {
                    $message[] = 'Failed to upload image.';
                }
            } else {
                $message[] = 'Image is too large. Maximum size is 2MB.';
            }
        } else {
            $message[] = 'Invalid image format. Only JPG, JPEG, and PNG are allowed.';
        }
    }
}






