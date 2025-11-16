<?php
session_start();
require 'config.php';
header('Content-Type: application/json');

$email = $_SESSION['email'] ?? null;
$query = trim($_POST['query'] ?? '');
$response = trim($_POST['response'] ?? '');
$rating = intval($_POST['rating'] ?? 0); // 1 or 5

if ($query === '') {
    echo json_encode(['ok'=>false,'msg'=>'empty query']);
    exit();
}

$stmt = $pdo->prepare("INSERT INTO feedback (email, query, response_text, rating) VALUES (?, ?, ?, ?)");
$stmt->execute([$email, $query, $response, $rating]);

echo json_encode(['ok'=>true]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Feedback – Admin Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
<div class="container mt-4">

<h2>User Feedback</h2>

<table class="table table-bordered mt-3 bg-white">
    <tr>
        <th>Email</th>
        <th>Feedback</th>
        <th>Rating</th>
        <th>Date</th>
    </tr>

    <?php foreach ($data as $row): ?>
    <tr>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td><?= htmlspecialchars($row['feedback']) ?></td>
        <td><?= $row['rating'] ?></td>
        <td><?= $row['date'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</div>
</body>
</html>