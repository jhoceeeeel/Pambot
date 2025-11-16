<?php
session_start();
require 'config.php';
if (!isset($_SESSION['admin'])) { die("Access Denied"); }

$data = $pdo->query("SELECT * FROM search_history ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Search History – Admin</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
<h2>Student Search History</h2>

<table class="table table-bordered table-striped">
<tr>
    <th>Email</th>
    <th>Search Query</th>
    <th>Date</th>
</tr>

<?php foreach ($data as $row): ?>
<tr>
    <td><?= $row['email'] ?></td>
    <td><?= htmlspecialchars($row['search_query']) ?></td>
    <td><?= $row['date'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</div>
</body>
</html>
