<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../db.php';

// ─── Nur POST erlaubt ───
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Nur POST erlaubt']);
    exit;
}

// ─── JSON-Daten einlesen ───
$input = json_decode(file_get_contents('php://input'), true);

$customerName = $input['customer_name'] ?? '';
$customerEmail = $input['customer_email'] ?? '';
$licenseType = $input['license_type'] ?? 'full';
$maxActivations = (int)($input['max_activations'] ?? 1);
$expiresIn = (int)($input['expires_in'] ?? 365);

if (empty($customerName)) {
    echo json_encode(['success' => false, 'error' => 'Kundenname erforderlich']);
    exit;
}

// ─── Lizenzschlüssel generieren ───
function generateLicenseKey() {
    $segments = [];
    for ($i = 0; $i < 4; $i++) {
        $segments[] = strtoupper(bin2hex(random_bytes(2)));
    }
    return implode('-', $segments);
}

$pdo = getDBConnection();

// ─── Sicherstellen, dass der Schlüssel eindeutig ist ───
do {
    $licenseKey = generateLicenseKey();
    $keyHash = hash('sha256', $licenseKey);
    $stmt = $pdo->prepare("SELECT id FROM licenses WHERE key_hash = :hash");
    $stmt->execute(['hash' => $keyHash]);
    $exists = $stmt->fetch();
} while ($exists);

// ─── Ablaufdatum berechnen ───
$expiresAt = null;
if ($expiresIn > 0) {
    $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiresIn} days"));
}

// ─── Lizenz speichern ───
$stmt = $pdo->prepare("
    INSERT INTO licenses (
        license_key, key_hash, customer_name, customer_email, 
        license_type, max_activations, expires_at, is_active
    ) VALUES (
        :key, :hash, :customer, :email, 
        :type, :max, :expires, 1
    )
");

$stmt->execute([
    'key' => $licenseKey,
    'hash' => $keyHash,
    'customer' => $customerName,
    'email' => $customerEmail,
    'type' => $licenseType,
    'max' => $maxActivations,
    'expires' => $expiresAt
]);

echo json_encode([
    'success' => true,
    'license_key' => $licenseKey,
    'customer_name' => $customerName,
    'license_type' => $licenseType,
    'expires_at' => $expiresAt
]);
