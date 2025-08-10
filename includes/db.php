<?php
require_once __DIR__ . '/../config/config.php';

$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
  error_log('MySQL Connect Error: '.$mysqli->connect_error);
  http_response_code(500);
  exit('Database connection failed.');
}
$mysqli->set_charset(DB_CHARSET);
