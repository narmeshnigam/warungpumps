<?php
// admin/lib/db.php
declare(strict_types=1);

// Was: require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../config/config.php';

function db(): PDO {
  static $pdo = null;
  if ($pdo) return $pdo;
  $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
  $opts = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
  return $pdo;
}
