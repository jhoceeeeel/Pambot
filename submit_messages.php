<?php
session_start();
require 'config.php';
if (!isset($_SESSION['email'])) { echo "Not logged in"; exit(); }

$email = $_SESSION['email'];
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($message === '') { header("Location: contact.php?err=1"); exit(); }

$stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
$stmt->execute([ $email, $email, "Subject: $subject\n\n$message" ]);

header("Location: contact.php?success=1");
