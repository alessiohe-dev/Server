<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('GET', 'POST');
$input = api_input();
$username = trim((string)($input['username'] ?? ''));
$password = (string)($input['password'] ?? '');
if ($username === '' || $password === '') api_error('Benutzername und Passwort erforderlich.');
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT id, username, password, level, experience, created_at FROM users WHERE username = :username LIMIT 1');
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, (string)$user['password'])) api_error('Benutzer nicht gefunden oder falsches Passwort.', 401);
unset($user['password']);
session_regenerate_id(true);
$_SESSION['user'] = $user;
try { $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id')->execute(['id' => $user['id']]); } catch (Throwable) {}
api_response(['success' => true, 'data' => $user]);
