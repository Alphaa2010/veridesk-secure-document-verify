<?php
declare(strict_types=1);
$configFile = __DIR__ . '/local.php';
$config = require is_file($configFile) ? $configFile : __DIR__ . '/config.example.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('veridesk_session');
    session_set_cookie_params(['httponly'=>true, 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite'=>'Lax']);
    session_start();
}
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/db.php';
