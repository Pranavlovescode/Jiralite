<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location:index.php");
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard - JiraLite</title>
</head>
<body>
  <h1>Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>
  <p>Your role is: <?= htmlspecialchars($_SESSION['role']) ?></p>
  <a href="logout.php">Logout</a>
</body>
</html>