<?php
session_start();
require 'config.php';
if (!isset($_SESSION['admin'])) { die("Access Denied"); }

$data = $pdo->query("SELECT * FROM chat_logs ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Chat Logs – Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
<h2>Chat Logs</h2>

<table class="table table-bordered bg-white">
<tr>
    <th>Email</th>
    <th>User Message</th>
    <th>Bot Response</th>
    <th>Date</th>
</tr>

<?php foreach ($data as $row): ?>
<tr>
    <td><?= $row['email'] ?></td>
    <td><?= htmlspecialchars($row['user_message']) ?></td>
    <td><?= htmlspecialchars($row['bot_response']) ?></td>
    <td><?= $row['date'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
