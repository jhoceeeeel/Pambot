<?php
require '../config.php';
header('Content-Type: application/json');

// Top searched keywords (simple)
$stmt = $pdo->query("SELECT search_query, COUNT(*) AS c FROM search_history GROUP BY search_query ORDER BY c DESC LIMIT 10");
$top = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Searches per day (last 14 days)
$stmt = $pdo->prepare("SELECT DATE(created_at) as d, COUNT(*) as c FROM search_history WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 14 DAY) GROUP BY d ORDER BY d ASC");
$stmt->execute();
$byday = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Feedback summary
$stmt = $pdo->query("SELECT rating, COUNT(*) as c FROM feedback GROUP BY rating");
$feedback = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['top_searches'=>$top, 'search_by_day'=>$byday, 'feedback'=>$feedback]);
?>
<!DOCTYPE html>
<html>
<head>
<title>Analytics – Admin Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">
<div class="container mt-4">

<h2>Analytics Dashboard</h2>

<div class="card mt-3 p-3">
<h4>Total Searches: <?= $searchCount ?></h4>
</div>

<h4 class="mt-4">Top 10 Most Searched Keywords</h4>
<table class="table table-bordered bg-white">
    <tr><th>Keyword</th><th>Hits</th></tr>
    <?php foreach ($topKeywords as $k): ?>
    <tr>
        <td><?= htmlspecialchars($k['search_query']) ?></td>
        <td><?= $k['hits'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</div>
</body>
</html> 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<canvas id="searchDay"></canvas>
<script>
fetch('analytics.php').then(r=>r.json()).then(data=>{
  const days = data.search_by_day.map(x=>x.d);
  const counts = data.search_by_day.map(x=>x.c);
  new Chart(document.getElementById('searchDay'), {
    type: 'line',
    data: { labels: days, datasets: [{ label:'Searches', data: counts }] },
  });
});
</script>
