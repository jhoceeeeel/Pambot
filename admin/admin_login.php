<?php
session_start();
require 'C:\xampp\htdocs\PamBot\config.php';

// Security: Limit login attempts
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] >= 5) {
    die("Too many failed login attempts. Try again later.");
}

// Admin credentials (use environment variables in production)
$admin_email = "admin@plmun.edu.ph";
$stored_hashed_password = password_hash("admin123", PASSWORD_DEFAULT); // Hash this only ONCE
$required_code = "PAMBOT2025"; // Change this periodically for security

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $admin_code = trim($_POST['admin_code']); // Unique Code Field

    // Validate admin email, password, and unique code
    if ($email === $admin_email && password_verify($password, $stored_hashed_password) && $admin_code === $required_code) {
        session_regenerate_id(true); // Secure session handling

        $_SESSION['admin'] = $email;
        $_SESSION['login_attempts'] = 0; // Reset failed attempts

        // Insert login time
        $stmt = $pdo->prepare("INSERT INTO admin_logs (email) VALUES (?)");
        $stmt->execute([$email]);

        // Store login ID for logout tracking
        $_SESSION['login_id'] = $pdo->lastInsertId();

        header("Location: admin.php");
        exit();
    } else {
        $_SESSION['login_attempts']++;
        echo "Invalid credentials or security code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css">
</head>

<body>
    <form action="admin_login.php" method="POST">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="text" name="admin_code" placeholder="Enter Security Code" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
