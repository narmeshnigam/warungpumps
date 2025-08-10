<?php
// App
define('APP_ENV', 'local'); // local|staging|production
define('APP_URL', 'http://localhost/warungpumps');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('INC_PATH', BASE_PATH . '/includes');

// DB (fill your creds)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'warungpumps');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Security
define('CSRF_SECRET', 'change_this_to_a_long_random_value');
