<?php
// admin/auth/handle-login.php
declare(strict_types=1);

require_once __DIR__ . '/../lib/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/csrf.php';

if (!method_is('POST')) { redirect(BASE_URL . '/login.php');
 }
csrf_verify();

$email = trim($_POST['email'] ?? '');
$pass  = (string)($_POST['password'] ?? '');

if ($email === '' || $pass === '') {
  flash('error', 'Email and password required.');
  redirect(BASE_URL . '/login.php');

}

$stmt = db()->prepare('SELECT id, name, email, password_hash, role, is_active FROM admin_users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !(bool)$user['is_active'] || !password_verify($pass, $user['password_hash'])) {
  flash('error', 'Invalid credentials.');
  redirect(BASE_URL . '/login.php');

}

start_session();
$_SESSION['admin'] = [
  'id'    => (int)$user['id'],
  'name'  => $user['name'],
  'email' => $user['email'],
  'role'  => $user['role'] ?: 'editor',
  'login_at' => date('c'),
];

redirect(BASE_URL . '/index.php');
