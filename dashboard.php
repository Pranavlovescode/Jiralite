<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if(!isset($_SESSION['user_id']) || !isset($_COOKIE['remember_token'])){
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