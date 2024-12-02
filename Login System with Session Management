<?php
// login.php - Login script for user authentication

include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Password is correct, start a session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        echo "Login successful!";
    } else {
        echo "Invalid email or password.";
    }
}
?>
