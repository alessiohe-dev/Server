<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('POST');
$input = api_input(false);
$deviceId = trim((string)($input['device_id'] ?? ''));
$licenseKey = strtoupper(trim((string)($input['license_key'] ?? '')));
if ($deviceId === '' || $licenseKey === '') api_error('Device ID und Lizenzschlüssel sind erforderlich.');
if (strlen($deviceId) > 255 || !preg_match('/^[A-F0-9-]{19}$/', $licenseKey)) api_error('Ungültige Lizenzdaten.');
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT * FROM licenses WHERE key_hash = :hash OR license_key = :key LIMIT 1');
$stmt->execute(['hash' => hash('sha256', $licenseKey), 'key' => $licenseKey]);
$license = $stmt->fetch();
if (!$license) api_error('Lizenz nicht gefunden.', 404);
if ((int)($license['is_active'] ?? 0) !== 1) api_error('Lizenz ist deaktiviert.', 403);
if (!empty($license['expires_at']) && strtotime((string)$license['expires_at']) < time()) api_error('Lizenz ist abgelaufen.', 403);
$pdo->exec('CREATE TABLE IF NOT EXISTS license_activations (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, license_id BIGINT UNSIGNED NOT NULL, device_id VARCHAR(255) NOT NULL, activated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, last_verified_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY license_device_unique (license_id, device_id), KEY license_activations_license_index (license_id))');
$pdo->beginTransaction();
try {
    $legacyDeviceId = trim((string)($license['device_id'] ?? ''));
    if ($legacyDeviceId !== '') {
        $seed = $pdo->prepare('INSERT IGNORE INTO license_activations (license_id, device_id) VALUES (:license, :device)');
        $seed->execute(['license' => $license['id'], 'device' => $legacyDeviceId]);
    }
    $activation = $pdo->prepare('SELECT id FROM license_activations WHERE license_id = :license AND device_id = :device LIMIT 1');
    $activation->execute(['license' => $license['id'], 'device' => $deviceId]);
    $activationId = $activation->fetchColumn();
    if ($activationId) {
        $pdo->prepare('UPDATE license_activations SET last_verified_at = NOW() WHERE id = :id')->execute(['id' => $activationId]);
    } else {
        $countStmt = $pdo->prepare('SELECT COUNT(*) FROM license_activations WHERE license_id = :license');
        $countStmt->execute(['license' => $license['id']]);
        $activationCount = (int)$countStmt->fetchColumn();
        if ($activationCount >= max(1, (int)($license['max_activations'] ?? 1))) {
            $pdo->rollBack();
            api_error('Maximale Anzahl an Geräteaktivierungen erreicht.', 409);
        }
        $pdo->prepare('INSERT INTO license_activations (license_id, device_id) VALUES (:license, :device)')->execute(['license' => $license['id'], 'device' => $deviceId]);
    }
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM license_activations WHERE license_id = :license');
    $countStmt->execute(['license' => $license['id']]);
    $activationCount = (int)$countStmt->fetchColumn();
    $pdo->prepare('UPDATE licenses SET activations = :count, device_id = COALESCE(NULLIF(device_id, \'\'), :device) WHERE id = :id')->execute(['count' => $activationCount, 'device' => $deviceId, 'id' => $license['id']]);
    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $exception;
}
api_response(['success' => true, 'message' => 'Lizenz gültig.', 'is_active' => true, 'license_type' => $license['license_type'] ?? 'full', 'expires_at' => $license['expires_at'] ?? null]);
