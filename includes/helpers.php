<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function csrf_token(){
  if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
  return $_SESSION['csrf'];
}
function csrf_verify($t){ return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$t); }
