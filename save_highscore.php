<?php
header('Content-Type: application/json');
require_once 'db.php';

$username = $_POST['username'] ?? '';
$levelId = $_POST['levelId'] ?? '';
$score = (int)($_POST['score'] ?? 0);

if (empty($username) || empty($levelId) || $score <= 0) {
    echo json_encode(['success' => false, 'error' => 'Username, Level-ID und Score erforderlich']);
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

// ─── Highscore speichern ───
$stmt = $pdo->prepare("
    INSERT INTO highscores (user_id, level_id, score, created_at) 
    VALUES (:user_id, :level_id, :score, NOW())
");
$stmt->execute([
    'user_id' => $user['id'],
    'level_id' => $levelId,
    'score' => $score
]);

echo json_encode(['success' => true, 'message' => 'Highscore gespeichert']);
?>
