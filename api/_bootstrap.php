<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$configuredOrigins = array_filter(array_map('trim', explode(',', getenv('CORS_ALLOWED_ORIGINS') ?: 'https://dartsystem.alessiohennecke.de,http://localhost:3000,http://localhost:8080')));
if ($origin !== '' && in_array($origin, $configuredOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Vary: Origin');
    header('Access-Control-Allow-Credentials: true');
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function api_input(bool $includeQuery = true): array
{
    $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
    $json = [];
    if (str_contains($contentType, 'application/json')) {
        $decoded = json_decode((string)file_get_contents('php://input'), true);
        if (is_array($decoded)) $json = $decoded;
    }
    $input = array_merge($includeQuery ? $_GET : [], $_POST, $json);
    return is_array($input) ? $input : [];
}

function api_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_error(string $message, int $status = 400): never
{
    api_response(['success' => false, 'error' => $message, 'message' => $message], $status);
}

function api_require_method(string ...$methods): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', $methods, true)) {
        header('Allow: ' . implode(', ', $methods));
        api_error('Methode nicht erlaubt.', 405);
    }
}

function api_username(array $input): string
{
    $username = trim((string)($input['username'] ?? current_user()['username'] ?? ''));
    if (!preg_match('/^[a-zA-Z0-9_-]{3,32}$/', $username)) api_error('Gültiger Benutzername erforderlich.');
    return $username;
}

set_exception_handler(static function (Throwable $exception): never {
    error_log('[api] ' . $exception->getMessage());
    api_error('Interner Serverfehler.', 500);
});
