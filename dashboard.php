<?php
session_start();

// Simulate login (you can replace this with actual session logic)
if (!isset($_SESSION['user_id'])) {
    // redirect if not logged in
    header("Location: index.php");
    exit();
}

// Simulated data – in real usage, you'd pull this from a database
$total_bugs = 42;
$open_bugs = 18;
$closed_bugs = 24;
$username = $_SESSION['name'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | JiraLite</title>
  <style>
    body {
      margin: 0;
      font-family: "Segoe UI", sans-serif;
      background-color: #f0f2f5;
      color: #333;
    }

    header {
      background-color:#007bff;
      color: white;
      padding: 0rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .container {
      padding: 2rem;
    }

    .cards {
      display: flex;
      gap: 1.5rem;
      margin-top: 2rem;
      flex-wrap: wrap;
    }

    .card {
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 1.5rem;
      flex: 1 1 200px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .card p{
      font-size: 25px;
      font-weight:700;
    }

    .card h3 {
      color: #007bff;
      margin-bottom: 0.5rem;
    }

    .links {
      margin-top: 2rem;
    }

    .links a {
      margin-right: 1rem;
      text-decoration: none;
      color: white;
      background-color: #007bff;
      padding: 0.6rem 1rem;
      border-radius: 4px;
      transition: background 0.3s;
    }

    .links a:hover {
      background-color: #0056b3;
    }
  </style>
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
</div>

</body>
</html>
