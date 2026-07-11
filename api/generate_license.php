<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db.php'; // oder finde db.php dynamisch

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Ungültige JSON-Daten']);
    exit;
}

$customerName   = trim($data['customer_name'] ?? '');
$customerEmail  = trim($data['customer_email'] ?? '');
$deviceId       = trim($data['device_id'] ?? '');
$licenseType    = $data['license_type'] ?? 'full';
$maxActivations = (int)($data['max_activations'] ?? 1);
$expiresIn      = (int)($data['expires_in'] ?? 365);

if (empty($customerName)) {
    echo json_encode(['success' => false, 'error' => 'Kundenname ist erforderlich']);
    exit;
}

$licenseKey = sprintf('%04X-%04X-%04X-%04X', random_int(0,65535), random_int(0,65535), random_int(0,65535), random_int(0,65535));
$keyHash = hash('sha256', $licenseKey);
$expiresAt = $expiresIn > 0 ? date('Y-m-d H:i:s', strtotime("+{$expiresIn} days")) : null;

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        INSERT INTO licenses (
            license_key,
            key_hash,
            customer_name,
            customer_email,
            device_id,           -- ← Spalte muss in der Tabelle existieren!
            license_type,
            max_activations,
            expires_at,
            is_active
        ) VALUES (
            :license_key,
            :key_hash,
            :customer_name,
            :customer_email,
            :device_id,
            :license_type,
            :max_activations,
            :expires_at,
            1
        )
    ");
    $stmt->execute([
        ':license_key'    => $licenseKey,
        ':key_hash'       => $keyHash,
        ':customer_name'  => $customerName,
        ':customer_email' => $customerEmail,
        ':device_id'      => $deviceId ?: null,
        ':license_type'   => $licenseType,
        ':max_activations'=> $maxActivations,
        ':expires_at'     => $expiresAt
    ]);

    echo json_encode([
        'success'     => true,
        'license_key' => $licenseKey,
        'id'          => $pdo->lastInsertId(),
        'expires_at'  => $expiresAt
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
