<?php
session_start();
if (!isset($_SESSION['email'])) { header("Location: login.php"); exit(); }
$studentEmail = $_SESSION['email'];
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Contact Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container py-4">
  <h3>Contact the Admin / Guidance</h3>
  <form action="submit_message.php" method="POST">
    <div class="mb-3">
      <label class="form-label">Your Email</label>
      <input name="email" value="<?=htmlspecialchars($studentEmail)?>" class="form-control" readonly>
    </div>
    <div class="mb-3">
      <label class="form-label">Subject</label>
      <input name="subject" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Message</label>
      <textarea name="message" class="form-control" rows="6" required></textarea>
    </div>
    <button class="btn btn-primary">Send Message</button>
  </form>
</div>
</body>
</html>
