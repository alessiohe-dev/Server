<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('POST');
$input = api_input(false);
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($input['csrf_token'] ?? null);
if (!csrf_is_valid(is_string($token) ? $token : null)) api_error('Ungültiges CSRF-Token.', 403);
unset($_SESSION['user']);
session_regenerate_id(true);
api_response(['success' => true, 'message' => 'Abgemeldet.']);
