<?php
session_start();
require 'C:\xampp\htdocs\PamBot\config.php'; // Adjust to your relative path if possible

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch students
$students = $pdo->query("SELECT * FROM student ORDER BY sign_in_time DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
session_start();
require '../config.php';
if (!isset($_SESSION['admin'])) { header("Location: ../admin_login.php"); exit(); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - PLMun GuideBot</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f5f7fb; }
    .card-quick { border-radius:12px; box-shadow:0 8px 24px rgba(17,24,39,0.06); }
    .sidebar { min-height:100vh; background:#fff; border-right:1px solid #e6eef8; }
    .nav-item a.active{ background:#eef6ff; border-radius:8px; color:#0b5ed7; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <aside class="col-md-2 sidebar p-3">
      <h5 class="mb-3">Admin</h5>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="analytics.php">Analytics</a></li>
        <li class="nav-item"><a class="nav-link" href="search_history.php">Search History</a></li>
        <li class="nav-item"><a class="nav-link" href="chat_logs.php">Chat Logs</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_feedback.php">Feedback</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_messages.php">Messages</a></li>
        <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
      </ul>
    </aside>

    <!-- Main -->
    <main class="col-md-10 p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Dashboard</h3>
        <small>Welcome, Admin</small>
      </div>

      <!-- Quick stats -->
      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
          <div class="p-3 card-quick bg-white">
            <h6>Total Searches</h6>
            <h3 id="totalSearches">—</h3>
            <small class="text-muted">All-time</small>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="p-3 card-quick bg-white">
            <h6>Feedback Submitted</h6>
            <h3 id="totalFeedback">—</h3>
            <small class="text-muted">All-time</small>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="p-3 card-quick bg-white">
            <h6>Messages</h6>
            <h3 id="totalMessages">—</h3>
            <small class="text-muted">Contact Us</small>
          </div>
        </div>
        <div class="col-sm-6 col-lg-3">
          <div class="p-3 card-quick bg-white">
            <h6>Active Students</h6>
            <h3 id="activeStudents">—</h3>
            <small class="text-muted">Last 24h</small>
          </div>
        </div>
      </div>

      <!-- Charts -->
      <div class="row g-3 mb-4">
        <div class="col-md-7">
          <div class="card p-3">
            <h6>Searches — Last 14 Days</h6>
            <canvas id="searchDaysChart" height="120"></canvas>
          </div>
        </div>
        <div class="col-md-5">
          <div class="card p-3">
            <h6>Top Keywords</h6>
            <canvas id="topKeywordsChart" height="120"></canvas>
          </div>
        </div>
      </div>

      <!-- Recent tables -->
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card p-3">
            <h6>Recent Searches</h6>
            <div id="recentSearches" style="max-height:240px; overflow:auto"></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-3">
            <h6>Recent Feedback</h6>
            <div id="recentFeedback" style="max-height:240px; overflow:auto"></div>
          </div>
        </div>
      </div>

    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
async function loadDashboard() {
  const res = await fetch('analytics.php');
  if (!res.ok) return console.error('Analytics load failed');
  const data = await res.json();

  // Quick numbers
  document.getElementById('totalSearches').innerText = data.total_searches ?? 0;
  document.getElementById('totalFeedback').innerText = data.total_feedback ?? 0;
  document.getElementById('totalMessages').innerText = data.total_messages ?? 0;
  document.getElementById('activeStudents').innerText = data.active_students ?? 0;

  // Searches by day chart
  const days = data.search_by_day.map(x=>x.d);
  const counts = data.search_by_day.map(x=>x.c);
  new Chart(document.getElementById('searchDaysChart'),{
    type:'line',
    data:{ labels: days, datasets:[{label:'Searches', data:counts, fill:true, tension:0.3}]},
    options:{ plugins:{legend:{display:false}}}
  });

  // Top keywords
  const kwLabels = data.top_searches.map(x=>x.search_query);
  const kwCounts = data.top_searches.map(x=>x.hits);
  new Chart(document.getElementById('topKeywordsChart'),{
    type:'bar',
    data:{ labels: kwLabels, datasets:[{label:'Hits', data:kwCounts}]},
    options:{ indexAxis:'y', plugins:{legend:{display:false}}}
  });

  // Recent searches list
  const rSearchDiv = document.getElementById('recentSearches');
  rSearchDiv.innerHTML = data.recent_searches.map(s=>`<div class="p-2 border-bottom">${s.email || 'anon'} — <strong>${s.search_query}</strong><br><small class="text-muted">${s.created_at}</small></div>`).join('');

  // Recent feedback
  const rFeedDiv = document.getElementById('recentFeedback');
  rFeedDiv.innerHTML = data.recent_feedback.map(f=>`<div class="p-2 border-bottom">${f.email||'anon'} — <strong>${f.rating||'-'}</strong><br>${f.feedback}<br><small class="text-muted">${f.created_at}</small></div>`).join('');
}

loadDashboard().catch(e=>console.error(e));
</script>
</body>
</html>
