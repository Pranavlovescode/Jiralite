<?php
session_start();
require "includes/db.php";
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';
require 'includes/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$pdo = createConnection();

if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit();
}

$statement = $pdo->prepare("SELECT * FROM users where role='developer'");
$statement->execute();
$devs = $statement->fetchAll();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['severity'];
    $status = $_POST['status'];
    $assignee = $_POST['assignee'];
    $reporter = $_SESSION['user_id'];

    if ($title && $description) {

        try {
            $stmt = $pdo->prepare("INSERT INTO bugs (title, description, priority, status, reporter_id, assignee_id)
                                VALUES (?, ?, ?, ?, ?, ?)");
            $success = $stmt->execute([$title, $description, $priority, $status, $reporter, $assignee]);
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage(); // show error message
        }

        // code for sending email to assignee
        try {
                $stmt_1 = $pdo->prepare("SELECT * FROM users WHERE id=?");
                $stmt_1->execute([$assignee]);
                $assigned_user = $stmt_1->fetch();
                $assigned_user_email = $assigned_user['email'];
                $devEmail=$assigned_user['email'];
                $devName=$assigned_user['name'];
                $assigned_by=$_SESSION['name'];

                $mail = new PHPMailer(true);
                $mail->isSMTP();

                $mail->isSMTP();
                $mail->Host = 'live.smtp.mailtrap.io';     
                $mail->SMTPAuth = true;
                $mail->Username = 'api'; 
                $mail->Password = '0ec71dc97bc8f143d4f205251eb87f74';       
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Sender & recipient
                $mail->setFrom('jiralitebot@pranavtitambe.in', 'JiraLite Bot');
                $mail->addAddress($devEmail, $devName);

                // Email content
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

                $mail->send();
                $success .= " Email sent to $devEmail.";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report a Bug | JiraLite</title>
  <style>
    *{
        margin:0px;
        padding: 0px;
        box-sizing: border-box;
    }
    body {
      font-family: "Segoe UI", sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      color: #333;
    }

    header {
      background-color: #007bff;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .container {
      max-width: 600px;
      margin: 2rem auto;
      background: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    h2 {
      color:white;
    }

    form label {
      display: block;
      margin-top: 1rem;
      font-weight: bold;
      color:#0056b3;
    }

    form input[type="text"],
    form textarea,
    form select {
      width: 100%;
      padding: 0.7rem;
      margin-top: 0.3rem;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    form button {
      background-color: #007bff;
      color: white;
      padding: 0.7rem 1.5rem;
      border: none;
      border-radius: 4px;
      margin-top: 1.5rem;
      cursor: pointer;
    }

    form button:hover {
      background-color: #0056b3;
    }

    .msg {
      margin-top: 1rem;
      padding: 1rem;
      background-color: #e1f5e1;
      color: green;
      border: 1px solid green;
      border-radius: 5px;
    }

    .error {
      background-color: #f8d7da;
      color: #a94442;
      border: 1px solid #a94442;
    }
  </style>
</head>
<body>

<header>
  <h2>🐞 JiraLite</h2>
  <a href="dashboard.php" style="color: white; text-decoration: underline;">Back to Dashboard</a>
</header>

<div class="container">
  <?php if (!empty($success)): ?>
    <div class="msg">Bug reported successfully!</div>
  <?php elseif (!empty($error)): ?>
    <div class="msg error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label for="title">Bug Title</label>
    <input type="text" name="title" id="title" required>

    <label for="description">Description</label>
    <textarea name="description" id="description" rows="5" required></textarea>

    <label for="severity">Severity</label>
    <select name="severity" id="severity">
      <option value="low">Low</option>
      <option value="medium" selected>Medium</option>
      <option value="high">High</option>
      <option value="critical">Critical</option>
    </select>

    <label for="status">Status</label>
    <select name="status" id="status">
      <option value="todo" selected>To Do</option>
      <option value="in_progress">In Progress</option>
      <option value="done">Done</option>
    </select>

    <label for="assignTo">Assign To</label>
    <select name="assignee" id="assignee">
        <option value="">-- Select Developer --</option>
        <?php
        foreach($devs as $dev){
            echo'<option value="'.htmlspecialchars($dev['id']) . '">' .htmlspecialchars($dev['name']).'</option>';
        }
        ?>
    </select>
    

    <button type="submit">Submit Bug</button>
  </form>
</div>

</body>
</html>
