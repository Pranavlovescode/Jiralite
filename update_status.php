<?php
session_start();
require "includes/db.php";

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit("Unauthorized");
}

$bug_id = $_POST['bug_id'] ?? null;
$new_status = $_POST['new_status'] ?? null;
$user_id = $_SESSION['user_id'];

$pdo = createConnection();

// Check if bug exists and is assigned to the user or user is admin
$stmt = $pdo->prepare("SELECT * FROM bugs WHERE id = ?");
$stmt->execute([$bug_id]);
$bug = $stmt->fetch();

if (!$bug || ($_SESSION['role'] !== 'admin' && $bug['assignee_id'] != $user_id)) {
    http_response_code(403);
    exit("Forbidden");
}

// Update the status
$updateStmt = $pdo->prepare("UPDATE bugs SET status = ? WHERE id = ?");
$updateStmt->execute([$new_status, $bug_id]);

echo "Success";