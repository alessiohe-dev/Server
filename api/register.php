<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('POST');
$input = api_input();
$username = trim((string)($input['username'] ?? ''));
$password = (string)($input['password'] ?? '');
if (!preg_match('/^[a-zA-Z0-9_-]{3,32}$/', $username)) api_error('Benutzername muss 3–32 Zeichen lang sein und darf Buchstaben, Zahlen, _ und - enthalten.');
if (strlen($password) < 8) api_error('Passwort muss mindestens 8 Zeichen lang sein.');
if (strlen($password) > 4096) api_error('Passwort ist zu lang.');
$pdo = getDBConnection();
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
$stmt->execute(['username' => $username]);
if ($stmt->fetch()) api_error('Benutzername bereits vergeben.', 409);
$stmt = $pdo->prepare('INSERT INTO users (username, password, created_at) VALUES (:username, :password, NOW())');
$stmt->execute(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
api_response(['success' => true, 'message' => 'Benutzer erfolgreich registriert.'], 201);
