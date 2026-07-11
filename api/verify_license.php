<?php
// ─── FEHLER ANZEIGEN (für Debug) ───
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// ─── DATENBANKVERBINDUNG ───
// Versuche verschiedene Pfade für db.php
$dbPaths = [
    __DIR__ . '/../db.php',          // website/db.php (eine Ebene höher)
    __DIR__ . '/../../db.php',       // zwei Ebenen höher
    __DIR__ . '/db.php',             // gleiches Verzeichnis
    $_SERVER['DOCUMENT_ROOT'] . '/website/db.php',
    $_SERVER['DOCUMENT_ROOT'] . '/db.php',
];

$dbPath = null;
foreach ($dbPaths as $path) {
    if (file_exists($path)) {
        $dbPath = $path;
        break;
    }
}

if (!$dbPath) {
    echo json_encode([
        'success' => false,
        'message' => 'db.php nicht gefunden. Geprüfte Pfade: ' . implode(', ', $dbPaths)
    ]);
    exit;
}

require_once $dbPath;

// ─── DATENBANKVERBINDUNG HERSTELLEN ───
try {
    $pdo = getDBConnection();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Datenbankverbindung fehlgeschlagen: ' . $e->getMessage()
    ]);
    exit;
}

// ─── JSON-DATEN EMPFANGEN ───
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Ungültige JSON-Daten empfangen'
    ]);
    exit;
}

$deviceId = trim($data['device_id'] ?? '');
$licenseKey = trim($data['license_key'] ?? '');

if (empty($deviceId) || empty($licenseKey)) {
    echo json_encode([
        'success' => false,
        'message' => 'Device ID und Lizenzschlüssel sind erforderlich'
    ]);
    exit;
}

// ─── LIZENZ IN DER DATENBANK PRÜFEN ───
try {
    $stmt = $pdo->prepare("
        SELECT id, license_key, device_id, is_active, expires_at 
        FROM licenses 
        WHERE license_key = :license_key
    ");
    $stmt->execute([':license_key' => $licenseKey]);
    $license = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$license) {
        echo json_encode([
            'success' => false,
            'message' => 'Lizenz nicht gefunden'
        ]);
        exit;
    }

    // ─── PRÜFEN: LIZENZ AKTIV? ───
    if ((int)$license['is_active'] !== 1) {
        echo json_encode([
            'success' => false,
            'message' => 'Lizenz ist deaktiviert'
        ]);
        exit;
    }

    // ─── PRÜFEN: ABGELAUFEN? ───
    if ($license['expires_at'] !== null && strtotime($license['expires_at']) < time()) {
        echo json_encode([
            'success' => false,
            'message' => 'Lizenz abgelaufen am ' . date('d.m.Y', strtotime($license['expires_at']))
        ]);
        exit;
    }

    // ─── PRÜFEN: DEVICE ID BEREITS VERGEBEN? ───
    if ($license['device_id'] !== null && $license['device_id'] !== $deviceId) {
        echo json_encode([
            'success' => false,
            'message' => 'Lizenz bereits an ein anderes Gerät gebunden'
        ]);
        exit;
    }

    // ─── FALLS NOCH KEINE DEVICE ID HINTERLEGT IST → JETZT SETZEN ───
    if ($license['device_id'] === null) {
        $update = $pdo->prepare("UPDATE licenses SET device_id = :device_id WHERE id = :id");
        $update->execute([
            ':device_id' => $deviceId,
            ':id' => $license['id']
        ]);
    }

    // ─── ERFOLGREICH ───
    echo json_encode([
        'success' => true,
        'message' => 'Lizenz gültig',
        'is_active' => true,
        'expires_at' => $license['expires_at']
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Datenbankfehler: ' . $e->getMessage()
    ]);
    exit;
}
