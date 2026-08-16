<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once dirname(__DIR__) . '/db.php';

const APP_NAME = 'DartSystem';
const APP_DOMAIN = 'dartsystem.alessiohennecke.de';
const DEFAULT_MEGA_URL = 'https://mega.nz/folder/j8YGkayJ#CzyaHuJY68BtI0vFQcSFrw';

function env_value(string $key, ?string $default = null): ?string
{
    $value = getenv($key);
    return ($value === false || $value === '') ? $default : $value;
}

function app_url(string $path = ''): string
{
    $base = rtrim(env_value('APP_URL', 'https://' . APP_DOMAIN) ?? '', '/');
    return $base . '/' . ltrim($path, '/');
}

function mega_download_url(): string
{
    return env_value('MEGA_DOWNLOAD_URL', DEFAULT_MEGA_URL) ?? DEFAULT_MEGA_URL;
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string)$_SESSION['csrf_token'];
}

function csrf_is_valid(?string $token): bool
{
    return is_string($token) && $token !== '' && hash_equals(csrf_token(), $token);
}

function require_csrf(): void
{
    $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    $formToken = $_POST['csrf_token'] ?? null;
    if (!csrf_is_valid(is_string($headerToken) ? $headerToken : (is_string($formToken) ? $formToken : null))) {
        http_response_code(403);
        exit('Ungültige Anfrage. Bitte lade die Seite neu.');
    }
}

function current_user(): ?array
{
    return isset($_SESSION['user']) && is_array($_SESSION['user']) ? $_SESSION['user'] : null;
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_authenticated']);
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function require_user(): array
{
    $user = current_user();
    if (!$user) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'] ?? '/account.php';
        redirect('/login.php');
    }
    return $user;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function consume_flash(): ?array
{
    $value = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($value) ? $value : null;
}

function safe_return_path(?string $path, string $fallback = '/account.php'): string
{
    if (!$path || !str_starts_with($path, '/') || str_starts_with($path, '//')) {
        return $fallback;
    }
    return $path;
}
