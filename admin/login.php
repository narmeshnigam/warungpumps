<?php
// File: /admin/login.php
session_start();
require_once '../includes/db.php';
require_once '../includes/functions/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $result = handle_login($pdo, $username, $password);

  if ($result['success']) {
    header('Location: dashboard.php');
    exit;
  } else {
    $error = $result['error'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <style>
    body { font-family: 'Open Sans', sans-serif; background: #F9F9F9; display: flex; align-items: center; justify-content: center; height: 100vh; }
    .login-card { background: #fff; padding: 40px; border-radius: 16px; box-shadow: 0 8px 24px rgba(10, 61, 98, 0.15); width: 100%; max-width: 480px; text-align: center; }
    h2 { font-family: 'Poppins', sans-serif; font-size: 36px; margin-bottom: 12px; }
    .form-group { text-align: left; margin-bottom: 20px; }
    label { font-weight: 600; display: block; margin-bottom: 6px; }
    input[type="text"], input[type="password"] { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #0A3D62; }
    .btn { background: #F76C1C; color: #fff; padding: 14px 28px; border-radius: 10px; font-weight: 600; cursor: pointer; width: 100%; border: none; transition: all 0.3s ease; }
    .btn:hover { filter: brightness(110%); transform: scale(1.05); }
    .error { color: red; font-size: 14px; margin-top: 8px; }
  </style>
</head>
<body>
  <div class="login-card">
    <img src="https://img.icons8.com/ios/64/lock--v1.png" alt="lock icon" style="margin-bottom: 16px;">
    <h2>Admin Login</h2>
    <p>Enter your credentials to continue.</p>
    <?php if ($error): ?>
      <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
  </div>
</body>
</html>
