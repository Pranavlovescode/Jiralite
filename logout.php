<?php
// Enable all errors
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

session_start();

require 'includes/db.php';

$pdo = createConnection();

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
}

session_unset();
session_destroy();

setcookie("remember_token", "", time() - 3600, "/");

header("Location: index.php");
exit;
?>