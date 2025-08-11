<?php
// admin/auth/csrf.php
declare(strict_types=1);
require_once __DIR__ . '/../lib/helpers.php';

function csrf_token(): string {
  start_session();
  if (empty($_SESSION['_csrf'])) {
    $_SESSION['_csrf'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['_csrf'];
}

function csrf_field(): string {
  return '<input type="hidden" name="_csrf" value="'.e(csrf_token()).'">';
}

function csrf_verify(): void {
  start_session();
  $ok = isset($_POST['_csrf'], $_SESSION['_csrf']) && hash_equals($_SESSION['_csrf'], $_POST['_csrf']);
  if (!$ok) {
    http_response_code(419);
    exit('CSRF validation failed.');
  }
}
