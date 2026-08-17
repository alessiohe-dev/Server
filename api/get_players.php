<?php
declare(strict_types=1);
require __DIR__ . '/_bootstrap.php';
api_require_method('GET');
$user = current_user();
if (!$user) api_error('Anmeldung erforderlich.', 401);
api_response([(string)$user['username']]);
