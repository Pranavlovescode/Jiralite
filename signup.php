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
  <style>
    .errors {
      background-color: #fee2e2;
      color: #dc2626;
      border: 1px solid #fecaca;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
      line-height: 1.6;
    }

    .errors p {
      margin: 0.5rem 0;
    }

    .errors p:first-child {
      margin-top: 0;
    }

    .success {
      background-color: #ecfdf5;
      color: #059669;
      border: 1px solid #a7f3d0;
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 1.5rem;
      font-size: 0.95rem;
      text-align: center;
      font-weight: 600;
    }

    .success a {
      color: #3b82f6;
      text-decoration: none;
      font-weight: 700;
    }

    .success a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="card">
    <p class="title">Welcome to JiraLite 🐞</p>

    <?php if ($success): ?>
      <p class="success">✅ Signup successful! <a href="index.php">Login to your account →</a></p>
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
          <input type="text" name="name" id="name" placeholder="John Doe" required>
        </div>

        <div>
          <label for="email">Email</label>
          <input type="email" name="email" placeholder="user@example.com" required>
        </div>

        <div>
          <label for="password">Password</label>
          <input type="password" name="password" placeholder="At least 8 characters" id="password" required>
        </div>

        <div>
          <label for="confirm-password">Confirm Password</label>
          <input type="password" name="confirm-password" placeholder="Re-enter your password" id="confirm-password" required>
        </div>

        <div>
          <label for="role">Select Your Role</label>
          <select name="role" id="role" required>
            <option value="">-- Choose a role --</option>
            <option value="developer">👨‍💻 Developer</option>
            <option value="qa">🧪 QA Tester</option>
            <option value="admin">⚙️ Admin</option>
          </select>
        </div>

        <button type="submit">Create Account</button>

        <div class="links">
          <a href="index.php" class="signup">Already have an account? Login →</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
