<?php
session_start();
require "includes/db.php";

// PHPMailer includes
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';
require 'includes/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$pdo = createConnection();
$success = '';
$error = '';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assignee'])) {
    $assignments = $_POST['assignee'];

    foreach ($assignments as $bugId => $devId) {
        if (!empty($devId)) {
            // 1. Update bug's assignee
            $stmt = $pdo->prepare("UPDATE bugs SET assignee_id = ? WHERE id = ?");
            $stmt->execute([$devId, $bugId]);

            // 2. Fetch bug info for email
            $bugStmt = $pdo->prepare("SELECT * FROM bugs WHERE id = ?");
            $bugStmt->execute([$bugId]);
            $bug = $bugStmt->fetch();

            $title = $bug['title'];
            $description = $bug['description'];
            $priority = $bug['priority'];
            $status = $bug['status'];

            // 3. Fetch developer info
            $userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $userStmt->execute([$devId]);
            $assigned_user = $userStmt->fetch();

            $devEmail = $assigned_user['email'];
            $devName = $assigned_user['name'];
            $assigned_by = $_SESSION['name'];

            // 4. Send email (Optional - won't block if it fails)
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.mailgun.org';
                $mail->SMTPAuth = true;
                $mail->Username = 'admin@pranavtitambe.in';
                $mail->Password = '263b83ef9a738273585d27418eeac839-f6d80573-072526d9';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->Timeout = 5; // 5 second timeout
                
                $mail->setFrom('admin@pranavtitambe.in', 'JiraLite Bug Tracker');
                $mail->addAddress($devEmail, $devName);

                $mail->isHTML(true);
                $mail->Subject = 'New Bug Assigned to You';
                $mail->Body = "
                    <h3>Hello $devName,</h3>
                    <p>A new bug has been assigned to you by <strong>$assigned_by</strong>.</p>
                    <p><strong>Title:</strong> $title</p>
                    <p><strong>Description:</strong> $description</p>
                    <p><strong>Severity:</strong> $priority</p>
                    <p><strong>Status:</strong> $status</p>
                    <br>
                    <p>Please log in to JiraLite to manage the task.</p>
                ";

                // Send email (won't block page if it fails)
                if ($mail->send()) {
                    // Email sent successfully

                }
            } catch (Exception $e) {
                // Log error but don't show it - just continue
                error_log("Email failed for $devEmail: " . $e->getMessage());
            }
        }
    }
}

header("Location: dashboard.php");
exit();
