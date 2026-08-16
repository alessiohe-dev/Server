<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('GET');
$levelId = trim((string)($_GET['levelId'] ?? $_GET['level_id'] ?? ''));
if (!preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $levelId)) api_error('Gültige Level-ID erforderlich.');
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT u.username, h.score, h.created_at FROM highscores h JOIN users u ON h.user_id = u.id WHERE h.level_id = :level ORDER BY h.score DESC, h.created_at ASC LIMIT 100');
$stmt->execute(['level' => $levelId]);
api_response(['success' => true, 'data' => $stmt->fetchAll()]);
