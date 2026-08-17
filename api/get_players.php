<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('GET');
$user = current_user();
if (!$user) api_error('Anmeldung erforderlich.', 401);
$pdo = getDBConnection();
$users = $pdo->query('SELECT username FROM users ORDER BY username')->fetchAll(PDO::FETCH_COLUMN);
api_response(array_values(array_map('strval', $users)));
