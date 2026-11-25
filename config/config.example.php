<?php
// Copy to config/local.php and update for your XAMPP installation.
return [
    'db' => ['host' => '127.0.0.1', 'name' => 'veridesk', 'user' => 'root', 'password' => '', 'charset' => 'utf8mb4'],
    'app' => ['name' => 'VeriDesk', 'base_url' => '', 'upload_dir' => dirname(__DIR__) . '/storage/uploads', 'max_upload_bytes' => 8 * 1024 * 1024],
];
