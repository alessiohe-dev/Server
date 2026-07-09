<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../db.php';

$username = $_POST['username'] ?? '';
$levelId = $_POST['levelId'] ?? '';
$dartsThrown = (int)($_POST['dartsThrown'] ?? 0);
$successfulHits = (int)($_POST['successfulHits'] ?? 0);

if (empty($username) || empty($levelId)) {
    echo json_encode(['success' => false, 'error' => 'Username und Level-ID erforderlich']);
    exit;
}

$pdo = getDBConnection();

// ─── User-ID holen ───
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Benutzer nicht gefunden']);
    exit;
}

$userId = $user['id'];
$accuracy = ($dartsThrown > 0) ? ($successfulHits / $dartsThrown) * 100 : 0;

$stmt = $pdo->prepare("
    INSERT INTO progress (user_id, level_id, darts_thrown, successful_hits, accuracy, attempts, last_updated)
    VALUES (:user_id, :level_id, :darts_thrown, :successful_hits, :accuracy, 1, NOW())
    ON DUPLICATE KEY UPDATE
        darts_thrown = darts_thrown + :darts_thrown,
        successful_hits = successful_hits + :successful_hits,
        accuracy = ((successful_hits + :successful_hits) / (darts_thrown + :darts_thrown)) * 100,
        attempts = attempts + 1,
        last_updated = NOW()
");

$stmt->execute([
    'user_id' => $userId,
    'level_id' => $levelId,
    'darts_thrown' => $dartsThrown,
    'successful_hits' => $successfulHits,
    'accuracy' => $accuracy
]);

echo json_encode(['success' => true, 'message' => 'Fortschritt gespeichert']);
?>
