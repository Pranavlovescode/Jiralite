<?php
session_start();
require "includes/db.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$pdo = createConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assignee'])) {
    $assignments = $_POST['assignee'];
    foreach ($assignments as $bugId => $devId) {
        if (!empty($devId)) {
            $stmt = $pdo->prepare("UPDATE bugs SET assignee_id = ? WHERE id = ?");
            $stmt->execute([$devId, $bugId]);

            // OPTIONAL: You can fetch email and send notification here
        }
    }
}

header("Location: dashboard.php");
exit();
