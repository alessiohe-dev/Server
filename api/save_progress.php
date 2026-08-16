<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('POST');
$input = api_input(false);
$username = api_username($input);
$levelId = trim((string)($input['levelId'] ?? $input['level_id'] ?? ''));
$darts = filter_var($input['dartsThrown'] ?? $input['darts_thrown'] ?? null, FILTER_VALIDATE_INT);
$hits = filter_var($input['successfulHits'] ?? $input['successful_hits'] ?? null, FILTER_VALIDATE_INT);
if (!preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $levelId) || $darts === false || $hits === false || $darts < 0 || $hits < 0 || $hits > $darts) api_error('Ungültige Fortschrittsdaten.');
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);
$userId = $stmt->fetchColumn();
if (!$userId) api_error('Benutzer nicht gefunden.', 404);
$accuracy = $darts > 0 ? ($hits / $darts) * 100 : 0;
$stmt = $pdo->prepare('INSERT INTO progress (user_id, level_id, darts_thrown, successful_hits, accuracy, attempts, last_updated) VALUES (:user_id, :level_id, :darts, :hits, :accuracy, 1, NOW()) ON DUPLICATE KEY UPDATE successful_hits = successful_hits + VALUES(successful_hits), darts_thrown = darts_thrown + VALUES(darts_thrown), accuracy = (successful_hits / NULLIF(darts_thrown, 0)) * 100, attempts = attempts + 1, last_updated = NOW()');
$stmt->execute(['user_id' => $userId, 'level_id' => $levelId, 'darts' => $darts, 'hits' => $hits, 'accuracy' => $accuracy]);
api_response(['success' => true, 'message' => 'Fortschritt gespeichert.']);
