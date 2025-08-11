<?php
// admin/auth/guard.php
declare(strict_types=1);
require_once __DIR__ . '/../lib/helpers.php';

start_session();

if (empty($_SESSION['admin'])) {
  redirect('/login.php');

}

// Optional: role check (expects $_SESSION['admin']['role'])
function require_role(string $role): void {
  $current = $_SESSION['admin']['role'] ?? 'editor';
  $roles = ['viewer'=>1,'editor'=>2,'admin'=>3];
  if (($roles[$current] ?? 0) < ($roles[$role] ?? 0)) {
    http_response_code(403);
    exit('Forbidden');
  }
}
