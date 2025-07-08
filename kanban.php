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
      <div class="column" id="todo" ondragover="allowDrop(event)" ondrop="drop(event, 'todo')">
        <h2 style="color:red;">To Do</h2>
        <div class="card" draggable="true" ondragstart="drag(event)" data-id="1">Bug: Login form not working</div>
      </div>

      <div class="column" id="in_progress" ondragover="allowDrop(event)" ondrop="drop(event, 'in_progress')">
        <h2 style="color:orange;">In Progress</h2>
        <div class="card" draggable="true" ondragstart="drag(event)" data-id="2">Bug: CSS not loading</div>
      </div>

      <div class="column" id="done" ondragover="allowDrop(event)" ondrop="drop(event, 'done')">
        <h2 style="color:green;">Done</h2>
        <div class="card" draggable="true" ondragstart="drag(event)" data-id="3">Bug: Footer fixed</div>
      </div>
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