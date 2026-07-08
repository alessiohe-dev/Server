<?php
function getDBConnection() {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');

    // ─── Fallback für lokale Tests ───
    if (empty($host)) {
        $host = 'gateway01.eu-central-1.prod.aws.tidbcloud.com';
        $port = '4000';
        $dbname = 'dart_system_db';
        $user = '4MPtfFH9VXUnkZo.root';
        $password = ''; // ← HIER DEIN PASSWORT EINTRAGEN!
    }

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname",
            $user,
            $password,
            [
                PDO::MYSQL_ATTR_SSL_CA => false,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("[db.php] ❌ Verbindungsfehler: " . $e->getMessage());
        throw $e;
    }
}
?>
