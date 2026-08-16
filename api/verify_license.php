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
$storedDevice = trim((string)($license['device_id'] ?? $license['device_fingerprint'] ?? ''));
if ($storedDevice !== '' && !hash_equals($storedDevice, $deviceId)) api_error('Lizenz ist bereits an ein anderes Gerät gebunden.', 409);
if ($storedDevice === '') {
    $pdo->prepare('UPDATE licenses SET device_id = :device WHERE id = :id AND (device_id IS NULL OR device_id = \'\')')->execute(['device' => $deviceId, 'id' => $license['id']]);
}
api_response(['success' => true, 'message' => 'Lizenz gültig.', 'is_active' => true, 'license_type' => $license['license_type'] ?? 'full', 'expires_at' => $license['expires_at'] ?? null]);
