<?php
function getDBConnection() {
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');

    // ─── Pfad zum CA-Zertifikat in Render ───
    $caCert = '/etc/secrets/ca.pem';

    try {
        $pdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$dbname",
            $user,
            $password,
            [
                PDO::MYSQL_ATTR_SSL_CA => $caCert,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
    }
}
?>
