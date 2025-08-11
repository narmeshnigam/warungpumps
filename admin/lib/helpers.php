<?php
// admin/lib/helpers.php
declare(strict_types=1);

// 1) Load config early (path points to /config/config.php from your tree)
require_once __DIR__ . '/../../config/config.php';

function start_session(): void {
  if (session_status() === PHP_SESSION_NONE) {
    // 2) Safe fallbacks in case constants are missing
    $lifetime = defined('SESSION_COOKIE_LIFETIME') ? (int)SESSION_COOKIE_LIFETIME : 7200; // 2h
    $samesite = 'Lax';
    $name     = defined('SESSION_NAME') ? SESSION_NAME : 'warung_admin';
    $secure   = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

    session_set_cookie_params([
      'lifetime' => $lifetime,
      'path'     => '/',
      'secure'   => $secure,
      'httponly' => true,
      'samesite' => $samesite,
    ]);
    session_name($name);
    session_start();
  }
}

function e(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function redirect(string $path): never {
  $base = defined('BASE_URL') ? BASE_URL : '/admin';
  header('Location: ' . $base . $path);
  exit;
}

function flash(string $key, ?string $msg = null) {
  if (!isset($_SESSION['_flash'])) $_SESSION['_flash'] = [];
  if ($msg === null) {
    $val = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $val;
  }
  $_SESSION['_flash'][$key] = $msg;
}

function method_is(string $verb): bool {
  return strtoupper($_SERVER['REQUEST_METHOD'] ?? '') === strtoupper($verb);
}
