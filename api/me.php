<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('GET');
$user = current_user();
if (!$user) api_error('Nicht angemeldet.', 401);
api_response(['success' => true, 'data' => $user, 'csrf_token' => csrf_token()]);
