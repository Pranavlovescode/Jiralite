<?php
require 'includes/db.php';
$pdo = createConnection();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm-password'] ?? '';
    $role = $_POST['role'] ?? '';

    if (!$name || !$email || !$password || !$confirm || !$role) {
        $errors[] = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = "Email already registered.";
        } else {
            try {
              $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
              $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
              $success = $stmt->execute([$name, $email, $hashedPassword, $role]);
            } catch (PDOException $e) {
              $errors[] = "Error: " . $e->getMessage(); // show error message
            }

            if (!$success) {
                $errors[] = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="static/login.css" />
  <title>JiraLite Signup</title>
</head>
<style>
  .errors{
    background-color: #f8d7da;
      color: #a94442;
      border: 1px solid #a94442;
  }
  .success{
      margin-top: 1rem;
      padding: 1rem;
      background-color: #e1f5e1;
      color: green;
      border: 1px solid green;
      border-radius: 5px;
  }
</style>
<body>
  <div class="card">
    <p class="title">Welcome to JiraLite</p>

    <?php if ($success): ?>
      <p class="success">✅ Signup successful! <a href="/jiralite/">Login</a></p>
    <?php else: ?>
      <?php if (!empty($errors)): ?>
        <div class="errors">
          <?php foreach ($errors as $error): ?>
            <p>❌ <?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" action="signup.php" class="login-form">
        <div>
          <label for="name">Name</label>
          <input type="text" name="name" id="name" placeholder="John Doe">
        </div>
        <div class="email">
          <label for="email">Email</label>
          <input type="email" name="email" placeholder="user@example.com">
        </div>
        <div class="password">
          <label for="pass">Password</label>
          <input type="password" name="password" placeholder="********" id="pass">
        </div>
        <div class="password">
          <label for="confirm-pass">Confirm Password</label>
          <input type="password" name="confirm-password" placeholder="********" id="confirm-pass">
        </div>
        <div>
          <label for="role">Roles</label>
          <select name="role" id="role">
            <option value="">Select option</option>
            <option value="developer">Developer</option>
            <option value="admin">Admin</option>
            <option value="qa">Quality Assurance</option>
          </select>
        </div>

        <div class="links">
          <a href="/jiralite/" class="signup">Login</a>
        </div>
        <button type="submit">Signup</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
