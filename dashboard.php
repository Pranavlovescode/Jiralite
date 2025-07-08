<?php
session_start();

require "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}


$pdo=createConnection();

$stmt = $pdo->prepare("SELECT * FROM bugs WHERE assignee_id=?");
$stmt->execute([$_SESSION['user_id']]);
$bugs = $stmt->fetchAll();

$todo_bugs = array_filter($bugs, function ($bug) {
  return $bug['status'] === 'todo';
});

$done_bugs = array_filter($bugs,function($b){
  return $b['status'] ==="done";
});

$total_bugs = count($bugs);
$open_bugs = count($todo_bugs);
$closed_bugs = count($done_bugs);
$username = $_SESSION['name'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | JiraLite</title>
  <link rel="stylesheet" href="static/dashboard.css">
</head>
<body>

<header>
  <h2>🐞 JiraLite</h2>
  <a href="logout.php" style="color: white; text-decoration: underline;">Logout</a>
</header>

<div class="container">
  <div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Project Dashboard</h1>
    <h2>Welcome, <?= htmlspecialchars($username) ?>!</h2>
  </div>
  <div class="cards">
    <div class="card">
      <h3>Total Bugs</h3>
      <p><?= $total_bugs ?></p>
    </div>
    <div class="card">
      <h3>Open Bugs</h3>
      <p><?= $open_bugs ?></p>
    </div>
    <div class="card">
      <h3>Closed Bugs</h3>
      <p><?= $closed_bugs ?></p>
    </div>
  </div>

  <div class="links">
    <a href="kanban.php">View Kanban Board</a>
    <a href="report_bug.php">Report New Bug</a>
  </div>

  <?php if (count($todo_bugs) > 0): ?>
  <div class="bug-list">
    <h2>Your Assigned Bugs</h2>
    <?php foreach ($todo_bugs as $bug): ?>
      <div class="bug-item">
        <div class="bug-title"><?= htmlspecialchars($bug['title']) ?></div>
        <div class="bug-desc"><?= htmlspecialchars($bug['description']) ?></div>
        <div class="bug-status">Status: <?= htmlspecialchars($bug['status']) ?></div>
        <!-- <hr> -->
      </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?>
    <div class="bug-list">
      <p>No bugs assigned to you yet.</p>
    </div>
  <?php endif; ?>
</div>


</body>
</html>
