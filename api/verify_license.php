<?php
header('Content-Type: application/json');

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

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['device_id']) || !isset($data['license_key'])) {
    echo json_encode(['success' => false, 'message' => 'Fehlende Parameter']);
    exit;
}

$deviceId = $data['device_id'];
$licenseKey = $data['license_key'];

// Lizenz prüfen
$stmt = $pdo->prepare("
    SELECT id, is_active, expires_at, device_id
    FROM licenses 
    WHERE license_key = :license_key
");

$stmt->execute([':license_key' => $licenseKey]);
$license = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$license) {
    echo json_encode(['success' => false, 'message' => 'Lizenz nicht gefunden']);
    exit;
}

if ((int)$license['is_active'] !== 1) {
    echo json_encode(['success' => false, 'message' => 'Lizenz deaktiviert']);
    exit;
}

if ($license['expires_at'] !== null && strtotime($license['expires_at']) < time()) {
    echo json_encode(['success' => false, 'message' => 'Lizenz abgelaufen']);
    exit;
}

// Prüfen: Ist eine Device ID bereits zugewiesen?
if ($license['device_id'] !== null && $license['device_id'] !== $deviceId) {
    // Diese Lizenz ist bereits an eine andere Device ID gebunden!
    echo json_encode(['success' => false, 'message' => 'Lizenz bereits an anderes Gerät gebunden']);
    exit;
}

// Falls noch keine Device ID hinterlegt ist → jetzt setzen
if ($license['device_id'] === null) {
    $update = $pdo->prepare("UPDATE licenses SET device_id = :device_id WHERE id = :id");
    $update->execute([
        ':device_id' => $deviceId,
        ':id' => $license['id']
    ]);
}

echo json_encode([
    'success' => true,
    'message' => 'Lizenz gültig',
    'is_active' => true,
    'expires_at' => $license['expires_at']
]);
