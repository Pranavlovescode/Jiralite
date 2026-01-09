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

if ($_SESSION['role'] !== 'qa' && $_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $priority = $_POST['severity'];
    $status = $_POST['status'];
    // $assignee = $_POST['assignee'];
    $reporter = $_SESSION['user_id'];

    if ($title && $description) {

        try {
            $stmt = $pdo->prepare("INSERT INTO bugs (title, description, priority, status, reporter_id)
                                VALUES (?, ?, ?, ?, ?)");
            $success = $stmt->execute([$title, $description, $priority, $status, $reporter]);
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage(); // show error message
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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report a Bug | JiraLite</title>
  <link rel="stylesheet" href="static/dashboard.css">
  <style>
    .form-container {
      max-width: 700px;
      margin: 2rem auto;
      background: white;
      padding: 2.5rem;
      border-radius: 12px;
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
      color: var(--dark);
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.75rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin-bottom: 1.5rem;
    }

    .form-group label {
      font-weight: 600;
      color: var(--dark);
      font-size: 0.95rem;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
      padding: 0.875rem;
      border: 1.5px solid var(--border);
      border-radius: 8px;
      font-size: 1rem;
      font-family: inherit;
      transition: all 0.2s ease;
      background-color: white;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-group textarea {
      resize: vertical;
      min-height: 120px;
    }

    .form-container button {
      width: 100%;
      padding: 1rem;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 1rem;
    }

    .form-container button:hover {
      background-color: var(--primary-dark);
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .success-msg {
      background-color: #dcfce7;
      color: var(--success);
      border: 1px solid #a7f3d0;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 600;
    }

    .error-msg {
      background-color: #fee2e2;
      color: var(--danger);
      border: 1px solid #fecaca;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 600;
    }

    .back-link {
      display: inline-block;
      margin-bottom: 1.5rem;
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      transition: all 0.2s ease;
    }

    .back-link:hover {
      color: var(--primary-dark);
      text-decoration: underline;
    }
  </style>
</head>
<body>

<header>
  <h2>🐞 JiraLite</h2>
  <a href="dashboard.php">← Back to Dashboard</a>
</header>

<div class="container">
  <div class="form-container">
    <h2>🐛 Report a Bug</h2>

    <?php if (!empty($success)): ?>
      <div class="success-msg">✅ Bug reported successfully! <a href="dashboard.php" style="color: var(--primary); text-decoration: underline;">Back to Dashboard</a></div>
    <?php elseif (!empty($error)): ?>
      <div class="error-msg">❌ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="title">🎯 Bug Title</label>
        <input type="text" name="title" id="title" placeholder="Describe the bug in a few words" required>
      </div>

      <div class="form-group">
        <label for="description">📝 Description</label>
        <textarea name="description" id="description" placeholder="Provide detailed information about the bug..." required></textarea>
      </div>

      <div class="form-group">
        <label for="severity">⚠️ Severity</label>
        <select name="severity" id="severity">
          <option value="low">🟢 Low</option>
          <option value="medium" selected>🟡 Medium</option>
          <option value="high">🟠 High</option>
          <option value="critical">🔴 Critical</option>
        </select>
      </div>

      <div class="form-group">
        <label for="status">📌 Status</label>
        <select name="status" id="status">
          <option value="todo" selected>To Do</option>
          <option value="in_progress">In Progress</option>
          <option value="done">Done</option>
        </select>
      </div>

      <button type="submit">Submit Bug Report</button>
    </form>
  </div>
</div>

</body>
</html>
