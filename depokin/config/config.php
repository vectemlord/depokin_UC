<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'depokin');
define('DB_USER', 'root');
define('DB_PASS', '');

$docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : '';
$appRoot = realpath(APP_ROOT);
$base = ($docRoot && str_starts_with($appRoot, $docRoot)) ? substr($appRoot, strlen($docRoot)) : '';
define('BASE_URL', rtrim(str_replace('\\', '/', $base), '/'));

define('UPLOAD_DIR', APP_ROOT . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

date_default_timezone_set('Asia/Jakarta');

require_once APP_ROOT . '/app/db.php';
require_once APP_ROOT . '/app/helpers.php';
require_once APP_ROOT . '/app/auth.php';
require_once APP_ROOT . '/app/articles.php';