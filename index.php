<?php
session_start();
require "includes/db.php";

$pdo = createConnection();

$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    
    if(!$email || !$pass){
        $error = '❌ Email and password are required.';
    }
    else{
        $stmt = $pdo->prepare("select * from users where email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if($user && password_verify($pass,$user['password'])){
            // valid login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']=$user['name'];
            $_SESSION['role']=$user['role'];
            $_SESSION['email']=$user['email'];
            header("Location: dashboard.php");
            exit;
        }else{
            $error = '❌ Invalid email or password.';
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="static/login.css">
    <title>JiraLite</title>
</head>
<body>
    <div class='card'>
        <p class="title">Login to JiraLite</p>
        <?php if ($error): ?>
            <div class="errors"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form action="index.php" method="post" class="login-form">

            <div class="email">
                <label for="email">Email</label>
                <input type="email" name='email' placeholder="user@example.com">
            </div>
            <div class="password">
                <label for="pass">Password</label>
                <input type="password" name="password" placeholder="********">
            </div>
            <p class='links'>
                <a href="forget_password.php" class='forget-password'>Forget Password ?</a>
                <a href="signup.php" class='signup'>Signup</a>
            </p>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html> 