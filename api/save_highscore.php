<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('POST');
$input = api_input(false);
$username = api_authenticated_username($input);
$levelId = trim((string)($input['levelId'] ?? $input['level_id'] ?? ''));
$score = filter_var($input['score'] ?? null, FILTER_VALIDATE_INT);
if (!preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $levelId) || $score === false || $score < 0) api_error('Username, gültige Level-ID und Score erforderlich.');
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);
$userId = $stmt->fetchColumn();
if (!$userId) api_error('Benutzer nicht gefunden.', 404);
$stmt = $pdo->prepare('INSERT INTO highscores (user_id, level_id, score, created_at) VALUES (:user_id, :level_id, :score, NOW())');
$stmt->execute(['user_id' => $userId, 'level_id' => $levelId, 'score' => $score]);
api_response(['success' => true, 'message' => 'Highscore gespeichert.'], 201);
