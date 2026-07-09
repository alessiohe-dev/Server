<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '/../db.php';

$levelId = $_GET['levelId'] ?? '';

if (empty($levelId)) {
    echo json_encode(['success' => false, 'error' => 'Level-ID erforderlich']);
    exit;
}

$pdo = getDBConnection();

// ─── Rangliste für Level abrufen ───
$stmt = $pdo->prepare("
    SELECT u.username, h.score, h.created_at 
    FROM highscores h
    JOIN users u ON h.user_id = u.id
    WHERE h.level_id = :level_id
    ORDER BY h.score DESC
    LIMIT 100
");
$stmt->execute(['level_id' => $levelId]);
$highscores = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'data' => $highscores]);
?>
