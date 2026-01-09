<?php
session_start();

require "includes/db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}


$pdo = createConnection();

$stmt = $pdo->prepare("SELECT * FROM bugs WHERE assignee_id=?");
$stmt->execute([$_SESSION['user_id']]);
$bugs = $stmt->fetchAll();
echo $bugs[0];

$todo_bugs = array_filter($bugs, function ($bug) {
  return $bug['status'] === 'todo';
});

$done_bugs = array_filter($bugs, function ($b) {
  return $b['status'] === "done";
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | JiraLite</title>
  <link rel="stylesheet" href="static/dashboard.css">
  <?php if ($_SESSION['role'] === 'admin'): ?>
    <link rel="stylesheet" href="static/admin.css">
  <?php endif; ?>
</head>

<body>

  <header>
    <h2>🐞 JiraLite</h2>
    <a href="logout.php">Logout</a>
  </header>

  <!-- <?php echo count($bugs) ?> -->

  <div class="container">
    <?php if ($_SESSION['role'] === 'admin'): ?>
      <?php
      $bugStmt = $pdo->prepare("SELECT * FROM bugs WHERE assignee_id IS NULL");
      $bugStmt->execute();
      $unassigned_bugs = $bugStmt->fetchAll();

      $devStmt = $pdo->prepare("SELECT * FROM users WHERE role='developer'");
      $devStmt->execute();
      $devs = $devStmt->fetchAll();
      ?>

      <div class="admin-panel">
        <h2>⚠️ Unassigned Bugs</h2>
        <?php if (count($unassigned_bugs) > 0): ?>
          <form method="POST" action="assign_bug.php">
            <?php foreach ($unassigned_bugs as $bug): ?>
              <div class="bug-card">
                <p><strong>Title:</strong> <?= htmlspecialchars($bug['title']) ?></p>
                <p><strong>Description:</strong> <?= htmlspecialchars($bug['description']) ?></p>
                <p><strong>Priority:</strong> <span class="bug-status"><?= htmlspecialchars($bug['priority']) ?></span></p>
                <label>Assign to:
                  <select name="assignee[<?= $bug['id'] ?>]">
                    <option value="">-- Select Developer --</option>
                    <?php foreach ($devs as $dev): ?>
                      <option value="<?= $dev['id'] ?>"><?= htmlspecialchars($dev['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </label>
              </div>
            <?php endforeach; ?>
            <button type="submit" class="assign-btn">Assign Selected Bugs</button>
          </form>
        <?php else: ?>
          <p style="color: #10b981; text-align: center; padding: 1rem;">✅ All bugs have been assigned!</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="dashboard-header">
      <h1>Project Dashboard</h1>
      <h2>Welcome, <?= htmlspecialchars($username) ?> 👋</h2>
    </div>

    <div class="cards">
      <div class="card">
        <h3>📊 Total Bugs</h3>
        <p><?= $total_bugs ?></p>
      </div>
      <div class="card">
        <h3>📝 Open Bugs</h3>
        <p><?= $open_bugs ?></p>
      </div>
      <div class="card">
        <h3>✅ Closed Bugs</h3>
        <p><?= $closed_bugs ?></p>
      </div>
    </div>

    <div class="links">
      <a href="kanban.php">📋 View Kanban Board</a>
      <?php if ($_SESSION['role'] === 'qa' || $_SESSION['role'] === 'admin'): ?>
        <a href="report_bug.php">🐛 Report New Bug</a>
      <?php endif; ?>
    </div>

    <div class="bug-list">
      <?php if (count($todo_bugs) > 0): ?>
        <h2>🎯 Your Assigned Bugs</h2>
        <?php foreach ($todo_bugs as $bug): ?>
          <div class="bug-item">
            <div class="bug-title"><?= htmlspecialchars($bug['title']) ?></div>
            <div class="bug-desc"><?= htmlspecialchars($bug['description']) ?></div>
            <span class="bug-status <?= $bug['status'] ?>">Status: <?= htmlspecialchars($bug['status']) ?></span>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <h2>🎉 Great Work!</h2>
        <p>No bugs assigned to you yet. Enjoy your free time!</p>
      <?php endif; ?>
    </div>
  </div>

</body>

</html>