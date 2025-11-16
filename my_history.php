<?php
session_start();
require 'config.php';

if (!isset($_SESSION['email'])) {
    echo json_encode([]);
    exit();
}

$email = $_SESSION['email'];

$stmt = $pdo->prepare("SELECT search_query, created_at 
                       FROM search_history 
                       WHERE email = ? 
                       ORDER BY created_at DESC LIMIT 50");
$stmt->execute([$email]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($rows);
