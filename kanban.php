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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kanban Board | JiraLite</title>
  <link rel="stylesheet" href="static/kanban.css">
</head>

<body>
  <header>
    <h1>🐞 JiraLite Kanban Board</h1>
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
        'todo' => '📋 To Do',
        'in_progress' => '🔄 In Progress',
        'done' => '✅ Done'
      ];
      foreach ($statuses as $statusKey => $statusLabel): ?>
        <div class="column" id="<?= $statusKey ?>" ondragover="allowDrop(event)"
          ondrop="drop(event, '<?= $statusKey ?>')">
          <h2><?= $statusLabel ?></h2>
          <div class="cards-container">
            <?php foreach ($bugs as $bug): ?>
              <?php if ($bug['status'] === $statusKey): ?>
                <div class="card" draggable="true" ondragstart="drag(event)" data-id="<?= $bug['id'] ?>" title="<?= htmlspecialchars($bug['description']) ?>">
                  <span class="priority-badge priority-<?= htmlspecialchars($bug['priority']) ?>"></span>
                  <?= htmlspecialchars($bug['title']) ?>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
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
      event.target.classList.add('dragging');
    }

    function drop(event, newStatus) {
      event.preventDefault();
      if (!draggedCard) return;

      const bugId = draggedCard.dataset.id;
      const column = document.getElementById(newStatus);
      const cardsContainer = column.querySelector('.cards-container');
      cardsContainer.appendChild(draggedCard);
      draggedCard.classList.remove('dragging');

      // Send AJAX request to update status
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "update_status.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send(`bug_id=${bugId}&new_status=${newStatus}`);

      xhr.onload = function () {
        if (xhr.status === 200) {
          console.log("✅ Status updated successfully.");
        } else {
          alert("❌ Error updating bug status.");
          location.reload();
        }
      };

      draggedCard = null;
    }

    // Add visual feedback on drag end
    document.addEventListener('dragend', () => {
      draggedCard = null;
      document.querySelectorAll('.card').forEach(card => {
        card.classList.remove('dragging');
      });
    });
  </script>

</body>

</html>