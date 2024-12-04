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

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM `user_form` WHERE id = ?");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$fetch = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <!-- Include Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title text-center">Update Profile</h3>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info">
                        <?php foreach ($message as $msg): ?>
                            <p><?= htmlspecialchars($msg) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="update_name" class="form-label">Username:</label>
                        <input type="text" name="update_name" id="update_name" value="<?= htmlspecialchars($fetch['name']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_email" class="form-label">Email:</label>
                        <input type="email" name="update_email" id="update_email" value="<?= htmlspecialchars($fetch['email']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="update_image" class="form-label">Update Your Picture:</label>
                        <input type="file" name="update_image" id="update_image" accept="image/jpg, image/jpeg, image/png" class="form-control">
                        <img src="<?= $fetch['image'] ? 'uploaded_img/' . htmlspecialchars($fetch['image']) : 'images/default-avatar.png' ?>" alt="Profile Image" class="img-thumbnail mt-3" width="150">
                    </div>
                    <div class="mb-3">
                        <label for="old_pass" class="form-label">Old Password:</label>
                        <input type="password" name="old_pass" id="old_pass" class="form-control" placeholder="Enter current password">
                    </div>
                    <div class="mb-3">
                        <label for="new_pass" class="form-label">New Password:</label>
                        <input type="password" name="new_pass" id="new_pass" class="form-control" placeholder="Enter new password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_pass" class="form-label">Confirm Password:</label>
                        <input type="password" name="confirm_pass" id="confirm_pass" class="form-control" placeholder="Confirm new password">
                    </div>
                    <div class="text-center">
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                        <a href="home.php" class="btn btn-secondary">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






