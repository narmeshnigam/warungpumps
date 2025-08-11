<?php
declare(strict_types=1);
require_once __DIR__ . '/lib/helpers.php';
start_session();
$_SESSION = [];
session_destroy();
redirect(BASE_URL . '/login.php');

