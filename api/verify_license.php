<?php
header('Content-Type: application/json');

// Datenbankverbindung
$host = 'localhost';
$dbname = 'dart_system_db';
$user = 'dein_user';
$pass = 'dein_passwort';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Datenbankfehler']);
    exit;
}

// JSON-Daten aus Unity empfangen
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['device_id']) || !isset($data['license_key'])) {
    echo json_encode(['success' => false, 'message' => 'Fehlende Parameter']);
    exit;
}

$deviceId = $data['device_id'];
$licenseKey = $data['license_key'];

// Lizenz in der Datenbank prüfen
$stmt = $pdo->prepare("
    SELECT id, is_active, expires_at 
    FROM licenses 
    WHERE license_key = :license_key 
    AND (device_fingerprint = :device_id OR device_fingerprint IS NULL)
");

$stmt->execute([
    ':license_key' => $licenseKey,
    ':device_id' => $deviceId
]);

$license = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$license) {
    echo json_encode(['success' => false, 'message' => 'Lizenz nicht gefunden']);
    exit;
}

// Prüfen, ob Lizenz aktiv ist
if ((int)$license['is_active'] !== 1) {
    echo json_encode(['success' => false, 'message' => 'Lizenz ist deaktiviert']);
    exit;
}

// Prüfen, ob Lizenz abgelaufen ist
if ($license['expires_at'] !== null && strtotime($license['expires_at']) < time()) {
    echo json_encode(['success' => false, 'message' => 'Lizenz abgelaufen']);
    exit;
}

// Falls device_fingerprint noch NULL ist, jetzt setzen
if ($license['device_fingerprint'] === null) {
    $update = $pdo->prepare("UPDATE licenses SET device_fingerprint = :device_id WHERE id = :id");
    $update->execute([
        ':device_id' => $deviceId,
        ':id' => $license['id']
    ]);
}

// Erfolgreich
echo json_encode([
    'success' => true,
    'message' => 'Lizenz gültig',
    'is_active' => true,
    'expires_at' => $license['expires_at']
]);
