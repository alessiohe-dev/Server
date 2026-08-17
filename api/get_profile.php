<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('GET');
$username = api_username($_GET);
$sessionUser = current_user();
if (!is_admin() && (!$sessionUser || !hash_equals((string)$sessionUser['username'], $username))) api_error('Zugriff auf dieses Profil ist nicht erlaubt.', 403);
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT id, username, level, experience, created_at, last_login FROM users WHERE username = :username LIMIT 1');
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();
if (!$user) api_error('Benutzer nicht gefunden.', 404);
$stmt = $pdo->prepare('SELECT level_id, darts_thrown, successful_hits, accuracy, attempts, completed, completed_at FROM progress WHERE user_id = :id ORDER BY last_updated DESC');
$stmt->execute(['id' => $user['id']]);
$user['progress'] = $stmt->fetchAll();
api_response(['success' => true, 'data' => $user]);
