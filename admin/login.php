<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/helpers.php';
require_once __DIR__ . '/auth/csrf.php';
start_session();
$err = flash('error');
?><!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e(BRAND_NAME) ?> – Login</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(BASE_URL) ?>assets/css/admin.css">
</head>
<body class="login-body">
  <div class="login-card">
    <h1 class="brand">Warung Admin</h1>
    <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
    <form method="post" action="<?= e(BASE_URL) ?>/auth/handle-login.php" autocomplete="off">
      <?= csrf_field() ?>
      <label>Email</label>
      <input type="email" name="email" required autofocus>
      <label>Password</label>
      <input type="password" name="password" required>
      <button class="btn" type="submit">Sign in</button>
    </form>
    <p class="muted">© <?= date('Y') ?> Warung Pumps</p>
  </div>
</body>
</html>
