<?php
session_start();
require 'config.php';
if (!isset($_SESSION['admin'])) { die("Access Denied"); }

$data = $pdo->query("SELECT * FROM messages ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Messages – Admin Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
<div class="container mt-4">

<h2>Student Messages</h2>

<table class="table table-bordered bg-white">
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Message</th>
    <th>Date</th>
</tr>

<?php foreach ($data as $row): ?>
<tr>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['message']) ?></td>
    <td><?= htmlspecialchars($row['date']) ?></td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
