<?php
/*
 * Central DB connection (PDO)
 * Update DB_NAME/USER/PASS to match your local MySQL.
 */
define('DB_HOST', '127.0.0.1');   // or 'localhost'
define('DB_NAME', 'warungpumps'); // <-- change to your actual DB name
define('DB_USER', 'root');        // XAMPP default is 'root'
define('DB_PASS', '');            // XAMPP default is empty

$pdoOptions = [
  PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
];

try {
  $pdo = new PDO(
    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    $pdoOptions
  );
} catch (PDOException $e) {
  http_response_code(500);
  die("DB connection failed: " . htmlspecialchars($e->getMessage()));
}
