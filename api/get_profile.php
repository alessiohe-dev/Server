<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../db.php';

$username = $_GET['username'] ?? '';

if (empty($username)) {
    echo json_encode(['success' => false, 'error' => 'Benutzername erforderlich']);
    exit;
}

$pdo = getDBConnection();

// ─── Benutzer laden ───
$stmt = $pdo->prepare("
    SELECT id, username, level, experience, created_at, last_login 
    FROM users 
    WHERE username = :username
");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Benutzer nicht gefunden']);
    exit;
}

// ─── Fortschritt laden ───
$stmt = $pdo->prepare("
    SELECT level_id, darts_thrown, successful_hits, accuracy, attempts, completed, completed_at
    FROM progress 
    WHERE user_id = :user_id
");
$stmt->execute(['user_id' => $user['id']]);
$progress = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user['progress'] = $progress;

echo json_encode(['success' => true, 'data' => $user]);
?>
