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
<title>FAQ – PLMun GuideBot</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="mb-3">Frequently Asked Questions</h2>

    <div class="accordion" id="faqAccordion">

        <div class="accordion-item">
            <h2 class="accordion-header" id="q1">
                <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#a1">How does PLMun GuideBot work?</button>
            </h2>
            <div id="a1" class="accordion-collapse collapse show">
                <div class="accordion-body">
                    The GuideBot searches the Student Handbook and provides short helpful answers using AI.
                </div>
            </div>
        </div>

        <div class="accordion-item">
            <h2 class="accordion-header" id="q2">
                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#a2">Is my data secure?</button>
            </h2>
            <div id="a2" class="accordion-collapse collapse">
                <div class="accordion-body">
                    Yes. Only your PLMUN email and search logs are stored.
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>