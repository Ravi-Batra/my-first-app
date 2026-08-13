<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $path = __DIR__ . '/config.local.php';
    if (!is_file($path)) {
        http_response_code(503);
        exit('The application is not configured yet. Please follow README.md.');
    }
    $config = require $path;
    try {
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4",
            $config['db_user'],
            $config['db_pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        http_response_code(503);
        exit('Service is temporarily unavailable. Please try again later.');
    }
}
