<?php
session_start();
require 'config.php';

$error = ""; // Initialize error message variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);

    // Validate email format (@plmun.edu.ph required)
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);

    // Validate email format against allowed programs
// Validate email format and program suffix
if (!preg_match('/^[a-z0-9._]+_(bsit|bscj|beed|bsed|bap|bsba|bsa|bsitech|bac|bssw|bsp|bscrim|bscs|act|md|bpa)@plmun\.edu\.ph$/i', $email)) {
    $error = "Invalid email. Use your PLMun student email (e.g. yourname_bsit@plmun.edu.ph).";


    } elseif (empty($username)) {
        $error = "Username is required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM student WHERE email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch();

            $sign_in_time = date('Y-m-d H:i:s');

            if ($student) {
                $update_stmt = $pdo->prepare("UPDATE student SET sign_in_time = ?, sign_out_time = NULL WHERE email = ?");
                $update_stmt->execute([$sign_in_time, $email]);
            } else {
                $insert_stmt = $pdo->prepare("INSERT INTO student (email, username, sign_in_time) VALUES (?, ?, ?)");
                $insert_stmt->execute([$email, $username, $sign_in_time]);
            }

            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLMun GuideBot - Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body style="background-image: url('bg1.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; margin: 0;">
    <header>
        <div class="logo">
            <img src="pambotlogo.png" alt="Pambot Logo">
        </div>
    </header>

    <main>
        <img src="welcomepambot.png" alt="Welcome to Pambot" class="welcome-image"style="filter: brightness(1.5); margin-top: 20px;">

<div class="login-box">
    <form action="login.php" method="POST">
        <label for="email">PLMun Email</label>
        <input type="email" id="email" name="email" placeholder="example@plmun.edu.ph" required autocomplete="email">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="username">
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <p class="terms">By signing in, you agree to the <a href="#">Terms & Privacy Policy</a></p>
        <button type="submit" class="sign-in">Sign in</button>
    </form>
</div>
    </main>
</body>
</html>