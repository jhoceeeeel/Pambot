<?php
session_start();
require 'config.php';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $sign_out_time = date('Y-m-d H:i:s');

    try {
        // Always record sign-out time
        $stmt = $pdo->prepare("UPDATE student SET sign_out_time = ? WHERE email = ?");
        $stmt->execute([$sign_out_time, $email]);
    } catch (PDOException $e) {
        error_log("Logout failed for $email: " . $e->getMessage());
    }
}


$isManual = isset($_GET['manual']);
if ($isManual) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

?>
