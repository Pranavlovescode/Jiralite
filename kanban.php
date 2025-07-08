<?php
session_start();
require "includes/db.php";

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}

$pdo = createConnection();

// Admins see all bugs, others only their assigned ones
if ($_SESSION['role'] === 'admin' || $_SESSION['role']==='qa') {
  $stmt = $pdo->query("SELECT * FROM bugs");
} else {
  $stmt = $pdo->prepare("SELECT * FROM bugs WHERE assignee_id = ?");
  $stmt->execute([$_SESSION['user_id']]);
}
$bugs = $stmt->fetchAll();
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>JiraLite Dashboard</title>
  <link rel="stylesheet" href="static/kanban.css">
  <script src="script.js"></script>

<body>
  <header>
    <h1>🐞 JiraLite</h1>
    <nav>
      <a href="dashboard.php">Dashboard</a>
      <a href="report_bug.php">Report Bug</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main>
    <section class="kanban">
      <?php
      $statuses = [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'done' => 'Done'
      ];
      $colors = [
        'todo' => 'red',
        'in_progress' => 'orange',
        'done' => 'green'
      ];
      foreach ($statuses as $statusKey => $statusLabel): ?>
        <div class="column" id="<?= $statusKey ?>" ondragover="allowDrop(event)"
          ondrop="drop(event, '<?= $statusKey ?>')">
          <h2 style="color:<?= $colors[$statusKey] ?>;"><?= $statusLabel ?></h2>
          <?php foreach ($bugs as $bug): ?>
            <?php if ($bug['status'] === $statusKey): ?>
              <div class="card" draggable="true" ondragstart="drag(event)" data-id="<?= $bug['id'] ?>">
                <?= htmlspecialchars($bug['title']) ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    </section>
  </main>


  <script>
    let draggedCard = null;

    function allowDrop(event) {
      event.preventDefault();
    }

    function drag(event) {
      draggedCard = event.target;
    }

    function drop(event, newStatus) {
      event.preventDefault();
      if (!draggedCard) return;

      const bugId = draggedCard.dataset.id;

      // Move card in DOM
      const column = document.getElementById(newStatus);
      column.appendChild(draggedCard);

      // Send AJAX request to update status
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_status.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send(`bug_id=${bugId}&new_status=${newStatus}`);

      xhr.onload = function () {
        if (xhr.status === 200) {
          console.log("Status updated successfully.");
        } else {
          alert("Error updating bug status.");
        }
      };
    }
  </script>


</body>

</html>