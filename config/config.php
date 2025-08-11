<?php
// config.php
declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'warungpumps');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Base URL for links (adjust for your environment)
define('BASE_URL', '/admin');

// Security
define('SESSION_NAME', 'warung_admin');
define('SESSION_COOKIE_LIFETIME', 60 * 60 * 2); // 2 hours
define('PASSWORD_ALGO', PASSWORD_DEFAULT);

// File uploads (used later in downloads/products modules)
define('UPLOAD_DIR', __DIR__ . '/admin/uploads');
define('DOCS_DIR', UPLOAD_DIR . '/documents');
define('MEDIA_DIR', UPLOAD_DIR . '/media');

// Brand text (used in header)
define('BRAND_NAME', 'Warung Pumps Admin');

// Ensure upload dirs exist
foreach ([UPLOAD_DIR, DOCS_DIR, MEDIA_DIR] as $d) {
  if (!is_dir($d)) { @mkdir($d, 0775, true); }
}
