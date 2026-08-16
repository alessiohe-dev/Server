<?php
declare(strict_types=1);

function getDBConnection(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: '';
    $port = getenv('DB_PORT') ?: '4000';
    $database = getenv('DB_NAME') ?: '';
    $username = getenv('DB_USER') ?: '';
    $password = getenv('DB_PASSWORD') ?: '';

    if ($host === '' || $database === '' || $username === '' || $password === '') {
        throw new RuntimeException('Die Datenbankverbindung ist noch nicht vollständig konfiguriert.');
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $host,
        $port,
        $database
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
    ];

    $sslCa = getenv('DB_SSL_CA') ?: '/etc/ssl/certs/ca-certificates.crt';
    if (defined('PDO::MYSQL_ATTR_SSL_CA') && is_file($sslCa)) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCa;
    }
    if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
    }

    try {
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $exception) {
        error_log('[database] Connection failed: ' . $exception->getMessage());
        throw new RuntimeException('Datenbankverbindung fehlgeschlagen.', 0, $exception);
    }
}
