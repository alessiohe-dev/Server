<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('POST');
$input = api_input(false);
$username = api_authenticated_username($input);
$levelId = trim((string)($input['levelId'] ?? $input['level_id'] ?? ''));
$darts = filter_var($input['dartsThrown'] ?? $input['darts_thrown'] ?? null, FILTER_VALIDATE_INT);
$hits = filter_var($input['successfulHits'] ?? $input['successful_hits'] ?? null, FILTER_VALIDATE_INT);
$completed = filter_var($input['completed'] ?? false, FILTER_VALIDATE_BOOL);
if (!preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $levelId) || $darts === false || $hits === false || $darts < 0 || $hits < 0 || $hits > $darts) api_error('Ungültige Fortschrittsdaten.');
$pdo = getDBConnection();
$pdo->exec('CREATE TABLE IF NOT EXISTS level_rewards (level_id VARCHAR(64) NOT NULL, experience INT UNSIGNED NOT NULL DEFAULT 0, PRIMARY KEY (level_id))');
$pdo->exec("INSERT IGNORE INTO level_rewards (level_id, experience) VALUES ('0001', 1000), ('0002', 1000)");
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);
$userId = $stmt->fetchColumn();
if (!$userId) api_error('Benutzer nicht gefunden.', 404);
$rewardStmt = $pdo->prepare('SELECT experience FROM level_rewards WHERE level_id = :level_id');
$rewardStmt->execute(['level_id' => $levelId]);
$experience = (int)($rewardStmt->fetchColumn() ?: 0);
$pdo->beginTransaction();
try {
    $createStmt = $pdo->prepare('INSERT IGNORE INTO progress (user_id, level_id, darts_thrown, successful_hits, accuracy, attempts, completed, completed_at, last_updated) VALUES (:user_id, :level_id, 0, 0, 0, 0, 0, NULL, NOW())');
    $createStmt->execute(['user_id' => $userId, 'level_id' => $levelId]);
    $previousStmt = $pdo->prepare('SELECT completed FROM progress WHERE user_id = :user_id AND level_id = :level_id FOR UPDATE');
    $previousStmt->execute(['user_id' => $userId, 'level_id' => $levelId]);
    $wasCompleted = (int)($previousStmt->fetchColumn() ?: 0) === 1;
    $stmt = $pdo->prepare('UPDATE progress SET accuracy = ((successful_hits + :hits_for_accuracy) / NULLIF(darts_thrown + :darts_for_accuracy, 0)) * 100, successful_hits = successful_hits + :hits, darts_thrown = darts_thrown + :darts, attempts = attempts + 1, completed = GREATEST(completed, :completed), completed_at = CASE WHEN completed_at IS NULL AND :completed_at = 1 THEN NOW() ELSE completed_at END, last_updated = NOW() WHERE user_id = :user_id AND level_id = :level_id');
    $stmt->execute(['hits_for_accuracy' => $hits, 'darts_for_accuracy' => $darts, 'hits' => $hits, 'darts' => $darts, 'completed' => $completed ? 1 : 0, 'completed_at' => $completed ? 1 : 0, 'user_id' => $userId, 'level_id' => $levelId]);
    if ($completed && !$wasCompleted && $experience > 0) {
        $pdo->prepare('UPDATE users SET experience = experience + :experience, level = GREATEST(1, FLOOR((experience + :experience_again) / 1000) + 1) WHERE id = :id')->execute(['experience' => $experience, 'experience_again' => $experience, 'id' => $userId]);
    }
    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    throw $exception;
}
api_response(['success' => true, 'message' => 'Fortschritt gespeichert.']);
