<?php
declare(strict_types=1);

// ─────────────────────────────────────────────
// ENTWICKLUNG / DEBUG
// In Produktion besser auf 0 setzen.
// ─────────────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', '1');

// ─────────────────────────────────────────────
// ADMIN KONFIGURATION
// Empfehlung: Später auf password_hash/password_verify umstellen.
// ─────────────────────────────────────────────
$dashboardUser = 'admin';
$dashboardPass = '#58DS579!';

// ─────────────────────────────────────────────
// SESSION STARTEN
// ─────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();
}

// ─────────────────────────────────────────────
// CSRF TOKEN
// ─────────────────────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string
{
    return $_SESSION['csrf_token'] ?? '';
}

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function check_csrf(): void
{
    if (
        $_SERVER['REQUEST_METHOD'] !== 'POST' ||
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'] ?? '', (string)$_POST['csrf_token'])
    ) {
        http_response_code(403);
        die('Ungültige Anfrage.');
    }
}

// ─────────────────────────────────────────────
// LOGIN VERARBEITEN
// ─────────────────────────────────────────────
$loginError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_action'])) {
    check_csrf();

    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === $dashboardUser && hash_equals($dashboardPass, $password)) {
        session_regenerate_id(true);
        $_SESSION['dashboard_logged_in'] = true;

        header('Location: index.php');
        exit;
    }

    $loginError = 'Falscher Benutzername oder Passwort!';
}

// ─────────────────────────────────────────────
// LOGOUT
// ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout_action'])) {
    check_csrf();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'] ?? '',
            (bool)$params['secure'],
            (bool)$params['httponly']
        );
    }

    session_destroy();

    header('Location: index.php');
    exit;
}

// ─────────────────────────────────────────────
// LOGIN STATUS
// ─────────────────────────────────────────────
$isLoggedIn = !empty($_SESSION['dashboard_logged_in']);

// ─────────────────────────────────────────────
// LOGIN SEITE
// ─────────────────────────────────────────────
if (!$isLoggedIn) {
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Login | DartSystem</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background:
                radial-gradient(circle at 20% 20%, rgba(59, 130, 246, 0.28), transparent 32%),
                radial-gradient(circle at 80% 80%, rgba(239, 68, 68, 0.18), transparent 34%),
                #070b16;
            color: #e2e8f0;
            min-height: 100vh;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .login-box {
            width: 100%;
            max-width: 430px;
            padding: 42px 36px;
            background: rgba(17, 26, 51, 0.88);
            border: 1px solid rgba(96, 165, 250, 0.18);
            border-radius: 24px;
            box-shadow: 0 24px 80px rgba(0, 0, 0, 0.48);
            backdrop-filter: blur(18px);
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .logo i {
            color: #3b82f6;
        }

        .highlight {
            color: #ef4444;
        }

        .sub {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #cbd5e1;
            margin-bottom: 7px;
        }

        input {
            width: 100%;
            padding: 13px 14px;
            background: rgba(11, 19, 41, 0.96);
            border: 1px solid #223150;
            border-radius: 12px;
            color: #e2e8f0;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.18);
        }

        input::placeholder {
            color: #475569;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.28);
        }

        .error {
            background: rgba(239, 68, 68, 0.14);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.28);
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 14px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #60a5fa;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .security-note {
            margin-top: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.72);
            border: 1px solid rgba(148, 163, 184, 0.12);
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-box">
            <div class="logo">
                <i class="fas fa-bullseye"></i>
                <span>Dart<span class="highlight">System</span></span>
            </div>

            <p class="sub">Admin-Dashboard Login</p>

            <?php if ($loginError): ?>
                <div class="error">❌ <?php echo e($loginError); ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="login_action" value="1">

                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value=""
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Benutzername eingeben"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        value=""
                        required
                        autocomplete="current-password"
                        placeholder="Passwort eingeben"
                    >
                </div>

                <button type="submit" class="btn-login">Einloggen</button>
            </form>

            <a href="/" class="back-link">← Zurück zur Hauptseite</a>

            <div class="security-note">
                Passwort wird nicht vorausgefüllt. Session- und Debug-Informationen werden nicht öffentlich angezeigt.
            </div>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// ─────────────────────────────────────────────
// DB.PHP SUCHEN
// ─────────────────────────────────────────────
function findDbFile(): string|false
{
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';

    $possiblePaths = [
        __DIR__ . '/../db.php',
        __DIR__ . '/../../db.php',
        __DIR__ . '/db.php',
        $documentRoot . '/website/db.php',
        $documentRoot . '/db.php',
        '/var/www/html/website/db.php',
        '/var/www/website/db.php',
    ];

    foreach ($possiblePaths as $path) {
        if ($path && file_exists($path)) {
            return $path;
        }
    }

    return false;
}

$dbPath = findDbFile();

if (!$dbPath) {
    die('❌ db.php konnte nicht gefunden werden. Bitte überprüfe den Pfad.');
}

require_once $dbPath;

// ─────────────────────────────────────────────
// AKTIONEN VERARBEITEN
// ─────────────────────────────────────────────
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    try {
        $pdo = getDBConnection();

        if (isset($_POST['delete_user'])) {
            $username = trim((string)$_POST['delete_user']);

            if ($username === '') {
                throw new RuntimeException('Ungültiger Benutzername.');
            }

            $stmt = $pdo->prepare('DELETE FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);

            $message = '✅ Spieler wurde gelöscht.';
            $messageType = 'success';
        }

        if (isset($_POST['activate_license'])) {
            $id = (int)$_POST['activate_license'];

            $stmt = $pdo->prepare('UPDATE licenses SET is_active = 1 WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $message = '✅ Lizenz wurde aktiviert.';
            $messageType = 'success';
        }

        if (isset($_POST['deactivate_license'])) {
            $id = (int)$_POST['deactivate_license'];

            $stmt = $pdo->prepare('UPDATE licenses SET is_active = 0 WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $message = '⚠️ Lizenz wurde deaktiviert.';
            $messageType = 'success';
        }

        if (isset($_POST['delete_license'])) {
            $id = (int)$_POST['delete_license'];

            $stmt = $pdo->prepare('DELETE FROM licenses WHERE id = :id');
            $stmt->execute(['id' => $id]);

            $message = '🗑️ Lizenz wurde gelöscht.';
            $messageType = 'success';
        }
    } catch (Throwable $e) {
        $message = '❌ Fehler: ' . $e->getMessage();
        $messageType = 'error';
    }
}

// ─────────────────────────────────────────────
// DATEN LADEN
// ─────────────────────────────────────────────
try {
    $pdo = getDBConnection();

    $stmt = $pdo->query('SELECT id, username, level, experience, created_at FROM users ORDER BY id DESC');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->query('SELECT * FROM licenses ORDER BY id DESC');
    $licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    die('❌ Datenbankfehler: ' . e($e->getMessage()));
}

$totalUsers = count($users);
$totalLicenses = count($licenses);

$activeLicenses = array_reduce($licenses, static function (int $carry, array $item): int {
    return $carry + (!empty($item['is_active']) ? 1 : 0);
}, 0);

$expiredLicenses = array_reduce($licenses, static function (int $carry, array $item): int {
    return $carry + (!empty($item['expires_at']) && strtotime((string)$item['expires_at']) < time() ? 1 : 0);
}, 0);

$currentTab = $_GET['tab'] ?? 'users';

if (!in_array($currentTab, ['users', 'licenses'], true)) {
    $currentTab = 'users';
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DartSystem – Admin Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg: #070b16;
            --panel: rgba(17, 26, 51, 0.88);
            --panel-solid: #111a33;
            --panel-dark: #0b1329;
            --border: #1e2d4a;
            --border-soft: rgba(96, 165, 250, 0.16);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --muted-dark: #64748b;
            --blue: #3b82f6;
            --blue-light: #60a5fa;
            --red: #ef4444;
            --red-light: #f87171;
            --green: #22c55e;
            --green-light: #6ee7b7;
            --yellow: #eab308;
            --yellow-light: #fcd34d;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 32%),
                radial-gradient(circle at bottom right, rgba(239, 68, 68, 0.12), transparent 30%),
                var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        header {
            background: rgba(7, 11, 22, 0.82);
            border-bottom: 1px solid rgba(96, 165, 250, 0.12);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(18px);
        }

        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 74px;
            gap: 18px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 800;
            white-space: nowrap;
        }

        .logo i {
            color: var(--blue);
            font-size: 26px;
        }

        .highlight {
            color: var(--red);
        }

        .badge-admin {
            background: linear-gradient(135deg, var(--red), #b91c1c);
            color: #ffffff;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 10px;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-left: 6px;
        }

        nav {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        nav a,
        .nav-button {
            color: var(--muted);
            text-decoration: none;
            padding: 9px 15px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s, color 0.2s, transform 0.2s;
            background: transparent;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        nav a:hover,
        .nav-button:hover {
            background: rgba(30, 45, 74, 0.78);
            color: #f8fafc;
        }

        nav a.active {
            background: rgba(59, 130, 246, 0.16);
            color: var(--blue-light);
            border: 1px solid rgba(96, 165, 250, 0.2);
        }

        .nav-button.logout {
            color: var(--red-light);
        }

        .nav-button.logout:hover {
            background: rgba(239, 68, 68, 0.12);
            color: #fecaca;
        }

        main {
            padding: 38px 0 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-bottom: 24px;
            margin-bottom: 26px;
            border-bottom: 1px solid rgba(96, 165, 250, 0.12);
            gap: 18px;
            flex-wrap: wrap;
        }

        .page-header h1 {
            font-size: clamp(26px, 4vw, 38px);
            font-weight: 800;
            letter-spacing: -0.03em;
            margin-bottom: 6px;
        }

        .page-header p {
            color: var(--muted);
            font-size: 15px;
        }

        .quick-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-main {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--blue), #2563eb);
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-main:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 28px rgba(37, 99, 235, 0.24);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 26px;
        }

        .stat-card {
            background: var(--panel);
            border: 1px solid var(--border-soft);
            border-radius: 18px;
            padding: 18px 20px;
            box-shadow: 0 18px 44px rgba(0, 0, 0, 0.18);
        }

        .stat-card .label {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            font-weight: 700;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 800;
            margin-top: 6px;
            letter-spacing: -0.03em;
        }

        .stat-card .value.small {
            font-size: 16px;
            font-weight: 700;
            color: var(--blue-light);
            letter-spacing: 0;
        }

        .message {
            padding: 14px 18px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message.success {
            background: rgba(6, 78, 59, 0.7);
            color: var(--green-light);
            border: 1px solid rgba(34, 197, 94, 0.25);
        }

        .message.error {
            background: rgba(127, 29, 29, 0.72);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.28);
        }

        .table-wrapper {
            background: var(--panel);
            border: 1px solid var(--border-soft);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 18px 44px rgba(0, 0, 0, 0.18);
        }

        .table-header {
            padding: 18px 22px;
            border-bottom: 1px solid rgba(96, 165, 250, 0.12);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .table-header h3 {
            font-size: 18px;
            font-weight: 800;
        }

        .table-header span {
            color: var(--muted);
            font-size: 14px;
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            min-width: 840px;
        }

        th {
            text-align: left;
            padding: 13px 16px;
            color: var(--muted);
            font-weight: 700;
            border-bottom: 1px solid rgba(96, 165, 250, 0.12);
            background: rgba(11, 19, 41, 0.72);
            white-space: nowrap;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(11, 19, 41, 0.86);
            vertical-align: middle;
        }

        tr:hover td {
            background: rgba(30, 45, 74, 0.42);
        }

        tr:last-child td {
            border-bottom: none;
        }

        .empty-state {
            text-align: center;
            padding: 52px 20px;
            color: var(--muted-dark);
        }

        .empty-state i {
            font-size: 44px;
            display: block;
            margin-bottom: 12px;
        }

        .actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            align-items: center;
        }

        .btn-sm {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 7px 11px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            transition: background 0.2s, color 0.2s, transform 0.2s;
            border: none;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-sm:hover {
            transform: translateY(-1px);
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.14);
            color: var(--red-light);
        }

        .btn-danger:hover {
            background: var(--red);
            color: #ffffff;
        }

        .btn-primary {
            background: rgba(59, 130, 246, 0.14);
            color: var(--blue-light);
        }

        .btn-primary:hover {
            background: var(--blue);
            color: #ffffff;
        }

        .btn-success {
            background: rgba(34, 197, 94, 0.14);
            color: var(--green-light);
        }

        .btn-success:hover {
            background: var(--green);
            color: #ffffff;
        }

        .btn-warning {
            background: rgba(234, 179, 8, 0.14);
            color: var(--yellow-light);
        }

        .btn-warning:hover {
            background: var(--yellow);
            color: #111827;
        }

        .badge-license {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            background: rgba(30, 45, 74, 0.8);
            padding: 4px 8px;
            border-radius: 7px;
            font-size: 12px;
            color: var(--blue-light);
            white-space: nowrap;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .badge-status.active {
            background: rgba(34, 197, 94, 0.18);
            color: var(--green-light);
        }

        .badge-status.inactive {
            background: rgba(239, 68, 68, 0.18);
            color: var(--red-light);
        }

        .badge-status.expired {
            background: rgba(234, 179, 8, 0.18);
            color: var(--yellow-light);
        }

        .muted {
            color: var(--muted);
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 12px;
            color: var(--muted);
            word-break: break-all;
        }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.72);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal {
            background: #111a33;
            border: 1px solid rgba(96, 165, 250, 0.18);
            border-radius: 22px;
            padding: 30px;
            max-width: 540px;
            width: 100%;
            max-height: 84vh;
            overflow-y: auto;
            box-shadow: 0 30px 100px rgba(0, 0, 0, 0.55);
        }

        .modal h2 {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 18px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .modal label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #cbd5e1;
            margin-bottom: 7px;
        }

        .modal input,
        .modal select {
            width: 100%;
            padding: 12px 14px;
            background: var(--panel-dark);
            border: 1px solid #223150;
            border-radius: 11px;
            color: var(--text);
            font-size: 14px;
        }

        .modal input:focus,
        .modal select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.18);
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 11px 18px;
            border-radius: 10px;
            border: none;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.2s, transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-close {
            background: #1e2d4a;
            color: #cbd5e1;
        }

        .btn-close:hover {
            background: #2a3a5c;
        }

        .btn-modal-primary {
            background: var(--blue);
            color: #ffffff;
        }

        .btn-modal-primary:hover {
            background: #2563eb;
        }

        .btn-modal-success {
            background: var(--green);
            color: #ffffff;
        }

        .btn-modal-success:hover {
            background: #16a34a;
        }

        .license-result {
            background: #0b1329;
            padding: 14px 16px;
            border-radius: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 18px;
            color: var(--green-light);
            text-align: center;
            margin: 16px 0 12px;
            word-break: break-all;
            border: 1px solid rgba(34, 197, 94, 0.22);
        }

        #toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            background: #111a33;
            border: 1px solid #1e2d4a;
            border-radius: 14px;
            padding: 15px 18px;
            color: var(--text);
            display: none;
            z-index: 2000;
            max-width: 420px;
            box-shadow: 0 18px 60px rgba(0, 0, 0, 0.55);
            font-size: 14px;
            font-weight: 600;
        }

        footer {
            padding: 28px 0 36px;
            color: var(--muted-dark);
            font-size: 13px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            border-top: 1px solid rgba(96, 165, 250, 0.1);
            padding-top: 22px;
        }

        .footer-content a {
            color: var(--blue-light);
            text-decoration: none;
        }

        .footer-content a:hover {
            text-decoration: underline;
        }

        @media (max-width: 980px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(160px, 1fr));
            }
        }

        @media (max-width: 720px) {
            .container {
                padding: 0 16px;
            }

            .header-inner {
                align-items: flex-start;
                flex-direction: column;
                padding: 14px 0;
            }

            nav {
                justify-content: flex-start;
            }

            main {
                padding-top: 26px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .modal {
                padding: 24px;
            }

            .footer-content {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="container header-inner">
        <div class="logo">
            <i class="fas fa-bullseye"></i>
            <span>Dart<span class="highlight">System</span></span>
            <span class="badge-admin">Admin</span>
        </div>

        <nav>
            <a href="?tab=users" class="<?php echo $currentTab === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Spieler
            </a>

            <a href="?tab=licenses" class="<?php echo $currentTab === 'licenses' ? 'active' : ''; ?>">
                <i class="fas fa-key"></i> Lizenzen
            </a>

            <form method="post" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" name="logout_action" value="1">
                <button type="submit" class="nav-button logout">
                    <i class="fas fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Spieler, Lizenzen und Aktivierungen verwalten.</p>
            </div>

            <div class="quick-actions">
                <button class="btn-main" id="openLicenseModal">
                    <i class="fas fa-plus"></i> Neue Lizenz
                </button>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo e($messageType); ?>">
                <?php echo e($message); ?>
            </div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Registrierte Spieler</div>
                <div class="value"><?php echo e($totalUsers); ?></div>
            </div>

            <div class="stat-card">
                <div class="label">Lizenzen gesamt</div>
                <div class="value"><?php echo e($totalLicenses); ?></div>
            </div>

            <div class="stat-card">
                <div class="label">Aktive Lizenzen</div>
                <div class="value"><?php echo e($activeLicenses); ?></div>
            </div>

            <div class="stat-card">
                <div class="label">Abgelaufen</div>
                <div class="value"><?php echo e($expiredLicenses); ?></div>
            </div>
        </div>

        <?php if ($currentTab === 'users'): ?>
            <section class="table-wrapper">
                <div class="table-header">
                    <h3><i class="fas fa-users"></i> Spielerliste</h3>
                    <span><?php echo e(count($users)); ?> Einträge</span>
                </div>

                <div class="table-scroll">
                    <?php if (empty($users)): ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Noch keine registrierten Spieler.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Benutzername</th>
                                    <th>Level</th>
                                    <th>Experience</th>
                                    <th>Registriert</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>#<?php echo e($user['id']); ?></td>
                                        <td><strong><?php echo e($user['username']); ?></strong></td>
                                        <td><?php echo e($user['level']); ?></td>
                                        <td><?php echo e($user['experience']); ?></td>
                                        <td>
                                            <?php
                                                $createdAt = !empty($user['created_at'])
                                                    ? date('d.m.Y H:i', strtotime((string)$user['created_at']))
                                                    : '—';
                                                echo e($createdAt);
                                            ?>
                                        </td>
                                        <td>
                                            <div class="actions">
                                                <a
                                                    href="?tab=users&profile=<?php echo urlencode((string)$user['username']); ?>"
                                                    class="btn-sm btn-primary js-profile-link"
                                                >
                                                    <i class="fas fa-user"></i> Profil
                                                </a>

                                                <form method="post" onsubmit="return confirm('Spieler wirklich löschen?')" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                                    <input type="hidden" name="delete_user" value="<?php echo e($user['username']); ?>">
                                                    <button type="submit" class="btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Löschen
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ($currentTab === 'licenses'): ?>
            <section class="table-wrapper">
                <div class="table-header">
                    <h3><i class="fas fa-key"></i> Lizenzverwaltung</h3>
                    <span><?php echo e(count($licenses)); ?> Lizenzen</span>
                </div>

                <div class="table-scroll">
                    <?php if (empty($licenses)): ?>
                        <div class="empty-state">
                            <i class="fas fa-key"></i>
                            <p>Noch keine Lizenzen erstellt.</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lizenzschlüssel</th>
                                    <th>Kunde</th>
                                    <th>Device ID</th>
                                    <th>Typ</th>
                                    <th>Status</th>
                                    <th>Ablauf</th>
                                    <th>Aktivierungen</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($licenses as $license): ?>
                                    <?php
                                        $isActive = !empty($license['is_active']);
                                        $isExpired = !empty($license['expires_at']) && strtotime((string)$license['expires_at']) < time();

                                        $status = 'active';

                                        if (!$isActive) {
                                            $status = 'inactive';
                                        } elseif ($isExpired) {
                                            $status = 'expired';
                                        }

                                        $statusLabel = [
                                            'active' => 'Aktiv',
                                            'inactive' => 'Inaktiv',
                                            'expired' => 'Abgelaufen',
                                        ][$status];

                                        $deviceId = $license['device_fingerprint'] ?? $license['device_id'] ?? '—';
                                        $licenseType = $license['license_type'] ?? '—';

                                        $expiresAt = !empty($license['expires_at'])
                                            ? date('d.m.Y', strtotime((string)$license['expires_at']))
                                            : '∞';

                                        $activations = $license['activations'] ?? 0;
                                        $maxActivations = $license['max_activations'] ?? 1;
                                    ?>
                                    <tr>
                                        <td>#<?php echo e($license['id'] ?? ''); ?></td>

                                        <td>
                                            <span class="badge-license">
                                                <?php echo e($license['license_key'] ?? '—'); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <strong><?php echo e($license['customer_name'] ?? '—'); ?></strong>
                                            <?php if (!empty($license['customer_email'])): ?>
                                                <br>
                                                <small class="muted"><?php echo e($license['customer_email']); ?></small>
                                            <?php endif; ?>
                                        </td>

                                        <td class="mono"><?php echo e($deviceId); ?></td>

                                        <td>
                                            <span class="badge-status active">
                                                <?php echo e($licenseType); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge-status <?php echo e($status); ?>">
                                                <?php echo e($statusLabel); ?>
                                            </span>
                                        </td>

                                        <td><?php echo e($expiresAt); ?></td>

                                        <td><?php echo e($activations); ?>/<?php echo e($maxActivations); ?></td>

                                        <td>
                                            <div class="actions">
                                                <?php if ($isActive): ?>
                                                    <form method="post" onsubmit="return confirm('Lizenz wirklich deaktivieren?')" style="display:inline;">
                                                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                                        <input type="hidden" name="deactivate_license" value="<?php echo e($license['id'] ?? 0); ?>">
                                                        <button type="submit" class="btn-sm btn-warning">
                                                            <i class="fas fa-pause"></i> Deaktivieren
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="post" onsubmit="return confirm('Lizenz wirklich aktivieren?')" style="display:inline;">
                                                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                                        <input type="hidden" name="activate_license" value="<?php echo e($license['id'] ?? 0); ?>">
                                                        <button type="submit" class="btn-sm btn-success">
                                                            <i class="fas fa-play"></i> Aktivieren
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <form method="post" onsubmit="return confirm('Lizenz wirklich löschen?')" style="display:inline;">
                                                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>">
                                                    <input type="hidden" name="delete_license" value="<?php echo e($license['id'] ?? 0); ?>">
                                                    <button type="submit" class="btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Löschen
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>

<!-- ─────────────────────────────────────────────
     MODAL: LIZENZ GENERIEREN
───────────────────────────────────────────── -->
<div class="modal-overlay" id="licenseModal">
    <div class="modal">
        <h2><i class="fas fa-key"></i> Lizenz generieren</h2>

        <form id="licenseForm">
            <input type="hidden" id="ajaxCsrfToken" value="<?php echo e(csrf_token()); ?>">

            <div class="form-group">
                <label for="customer_name">Kundenname</label>
                <input type="text" id="customer_name" placeholder="z. B. Max Mustermann" required>
            </div>

            <div class="form-group">
                <label for="customer_email">E-Mail optional</label>
                <input type="email" id="customer_email" placeholder="max@beispiel.de">
            </div>

            <div class="form-group">
                <label for="device_id">Device ID optional</label>
                <input type="text" id="device_id" placeholder="Leer lassen, wenn Kunde selbst registrieren soll">
            </div>

            <div class="form-group">
                <label for="license_type">Lizenztyp</label>
                <select id="license_type">
                    <option value="full">Full</option>
                    <option value="trial">Trial</option>
                    <option value="subscription">Subscription</option>
                </select>
            </div>

            <div class="form-group">
                <label for="max_activations">Maximale Aktivierungen</label>
                <input type="number" id="max_activations" value="1" min="1" max="999">
            </div>

            <div class="form-group">
                <label for="expires_in">Gültigkeitsdauer</label>
                <select id="expires_in">
                    <option value="30">30 Tage</option>
                    <option value="90">90 Tage</option>
                    <option value="180">180 Tage</option>
                    <option value="365" selected>1 Jahr</option>
                    <option value="0">Unbegrenzt</option>
                </select>
            </div>

            <div id="licenseResult" style="display:none;">
                <div class="license-result" id="generatedLicense">XXXX-XXXX-XXXX-XXXX</div>
                <button type="button" class="btn btn-modal-success" id="copyLicenseBtn">
                    <i class="fas fa-copy"></i> Kopieren
                </button>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-close" id="modalClose">Schließen</button>
                <button type="submit" class="btn btn-modal-primary" id="generateBtn">
                    <i class="fas fa-key"></i> Generieren
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ─────────────────────────────────────────────
     MODAL: PROFIL
───────────────────────────────────────────── -->
<div class="modal-overlay" id="profileModal">
    <div class="modal">
        <h2><i class="fas fa-user"></i> Spieler-Profil</h2>
        <div id="profileContent">
            <p class="muted">Lade Daten...</p>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-close" id="profileClose">Schließen</button>
        </div>
    </div>
</div>

<div id="toast">
    <span id="toastMessage"></span>
</div>

<footer>
    <div class="container footer-content">
        <span>&copy; <?php echo date('Y'); ?> DartSystem – Admin-Dashboard</span>
        <span><a href="/">← Zurück zur Hauptseite</a></span>
    </div>
</footer>

<script>
'use strict';

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');

    toastMessage.textContent = message;
    toast.style.borderColor = type === 'success' ? '#22c55e' : '#ef4444';
    toast.style.display = 'block';

    clearTimeout(toast._timeout);

    toast._timeout = setTimeout(() => {
        toast.style.display = 'none';
    }, 4200);
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function formatDateTime(value) {
    if (!value) return '—';

    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
        return escapeHtml(value);
    }

    return date.toLocaleString('de-DE');
}

// ─────────────────────────────────────────────
// LIZENZ MODAL
// ─────────────────────────────────────────────
const licenseModal = document.getElementById('licenseModal');
const openLicenseModal = document.getElementById('openLicenseModal');
const modalClose = document.getElementById('modalClose');
const licenseResult = document.getElementById('licenseResult');
const generatedLicense = document.getElementById('generatedLicense');
const copyLicenseBtn = document.getElementById('copyLicenseBtn');

openLicenseModal?.addEventListener('click', () => {
    licenseModal.classList.add('open');
    licenseResult.style.display = 'none';

    const customerNameInput = document.getElementById('customer_name');
    customerNameInput.value = '';
    document.getElementById('customer_email').value = '';
    document.getElementById('device_id').value = '';
    document.getElementById('license_type').value = 'full';
    document.getElementById('max_activations').value = '1';
    document.getElementById('expires_in').value = '365';

    setTimeout(() => customerNameInput.focus(), 50);
});

modalClose?.addEventListener('click', () => {
    licenseModal.classList.remove('open');
});

licenseModal?.addEventListener('click', (event) => {
    if (event.target === licenseModal) {
        licenseModal.classList.remove('open');
    }
});

// ─────────────────────────────────────────────
// LIZENZ GENERIEREN
// Voraussetzung: /api/generate_license.php existiert.
// Muss JSON zurückgeben: { success: true, license_key: "..." }
// ─────────────────────────────────────────────
document.getElementById('licenseForm')?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const customerName = document.getElementById('customer_name').value.trim();
    const customerEmail = document.getElementById('customer_email').value.trim();
    const deviceId = document.getElementById('device_id').value.trim();
    const licenseType = document.getElementById('license_type').value;
    const maxActivations = Number.parseInt(document.getElementById('max_activations').value, 10);
    const expiresIn = Number.parseInt(document.getElementById('expires_in').value, 10);
    const csrfToken = document.getElementById('ajaxCsrfToken').value;

    if (!customerName) {
        showToast('❌ Bitte Kundenname eingeben.', 'error');
        return;
    }

    if (!Number.isInteger(maxActivations) || maxActivations < 1) {
        showToast('❌ Maximale Aktivierungen muss mindestens 1 sein.', 'error');
        return;
    }

    const generateBtn = document.getElementById('generateBtn');
    const originalText = generateBtn.innerHTML;

    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird generiert...';

    try {
        const response = await fetch('/api/generate_license.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify({
                customer_name: customerName,
                customer_email: customerEmail,
                device_id: deviceId || null,
                license_type: licenseType,
                max_activations: maxActivations,
                expires_in: expiresIn
            })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Serverfehler beim Generieren.');
        }

        if (data.success) {
            generatedLicense.textContent = data.license_key || '';
            licenseResult.style.display = 'block';
            showToast('✅ Lizenz erfolgreich generiert.');

            setTimeout(() => {
                window.location.href = 'index.php?tab=licenses';
            }, 1300);
        } else {
            showToast('❌ ' + (data.error || 'Fehler beim Generieren.'), 'error');
        }
    } catch (error) {
        showToast('❌ Fehler: ' + error.message, 'error');
    } finally {
        generateBtn.disabled = false;
        generateBtn.innerHTML = originalText;
    }
});

// ─────────────────────────────────────────────
// LIZENZ KOPIEREN
// ─────────────────────────────────────────────
copyLicenseBtn?.addEventListener('click', async () => {
    const text = generatedLicense.textContent.trim();

    if (!text) {
        showToast('❌ Keine Lizenz zum Kopieren vorhanden.', 'error');
        return;
    }

    try {
        await navigator.clipboard.writeText(text);
        showToast('✅ Lizenz wurde kopiert.');
    } catch {
        const range = document.createRange();
        range.selectNode(generatedLicense);

        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);

        document.execCommand('copy');
        window.getSelection().removeAllRanges();

        showToast('✅ Lizenz wurde kopiert.');
    }
});

// ─────────────────────────────────────────────
// PROFIL MODAL
// Voraussetzung: /api/get_profile.php existiert.
// Muss JSON zurückgeben: { success: true, data: {...} }
// ─────────────────────────────────────────────
const profileModal = document.getElementById('profileModal');
const profileContent = document.getElementById('profileContent');
const profileClose = document.getElementById('profileClose');

document.querySelectorAll('.js-profile-link').forEach((link) => {
    link.addEventListener('click', async (event) => {
        event.preventDefault();

        const url = new URL(link.href);
        const username = url.searchParams.get('profile');

        profileModal.classList.add('open');
        profileContent.innerHTML = '<p class="muted">⏳ Lade Daten...</p>';

        try {
            const response = await fetch(`/api/get_profile.php?username=${encodeURIComponent(username)}`);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Serverfehler beim Laden.');
            }

            if (data.success) {
                const u = data.data || {};
                const progress = Array.isArray(u.progress) ? u.progress : [];

                profileContent.innerHTML = `
                    <div style="background:#0b1329; padding:16px; border-radius:14px; margin-bottom:16px; border:1px solid rgba(96,165,250,.12);">
                        <p><strong>Benutzername:</strong> ${escapeHtml(u.username)}</p>
                        <p><strong>ID:</strong> #${escapeHtml(u.id)}</p>
                        <p><strong>Level:</strong> ${escapeHtml(u.level)}</p>
                        <p><strong>Experience:</strong> ${escapeHtml(u.experience)}</p>
                        <p><strong>Registriert:</strong> ${formatDateTime(u.created_at)}</p>
                        ${u.last_login ? `<p><strong>Letzter Login:</strong> ${formatDateTime(u.last_login)}</p>` : ''}
                    </div>

                    <div style="background:#0b1329; padding:16px; border-radius:14px; border:1px solid rgba(96,165,250,.12);">
                        <h4 style="margin-bottom:10px;">Fortschritt</h4>
                        ${
                            progress.length > 0
                                ? progress.map((p) => `
                                    <div style="border-bottom:1px solid #1a2332; padding:8px 0; font-size:14px;">
                                        <strong>${escapeHtml(p.level_id)}</strong>
                                        <span class="muted">– ${escapeHtml(p.accuracy || 0)}% Genauigkeit, ${escapeHtml(p.darts_thrown || 0)} Darts</span>
                                    </div>
                                `).join('')
                                : '<p class="muted">Kein Fortschritt vorhanden.</p>'
                        }
                    </div>
                `;
            } else {
                profileContent.innerHTML = `<p style="color:#f87171;">❌ ${escapeHtml(data.error || 'Benutzer nicht gefunden.')}</p>`;
            }
        } catch (error) {
            profileContent.innerHTML = `<p style="color:#f87171;">❌ Fehler beim Laden: ${escapeHtml(error.message)}</p>`;
        }
    });
});

profileClose?.addEventListener('click', () => {
    profileModal.classList.remove('open');
});

profileModal?.addEventListener('click', (event) => {
    if (event.target === profileModal) {
        profileModal.classList.remove('open');
    }
});

// ─────────────────────────────────────────────
// ESC SCHLIESST MODALS
// ─────────────────────────────────────────────
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        licenseModal?.classList.remove('open');
        profileModal?.classList.remove('open');
    }
});
</script>

</body>
</html>
