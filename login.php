<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('/account.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('SELECT id, username, password, level, experience, created_at FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, (string)$user['password'])) {
            session_regenerate_id(true);
            unset($user['password']);
            $_SESSION['user'] = $user;
            try {
                $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = :id')->execute(['id' => $user['id']]);
            } catch (Throwable) {}
            $target = safe_return_path(is_string($_SESSION['after_login'] ?? null) ? $_SESSION['after_login'] : null);
            unset($_SESSION['after_login']);
            redirect($target);
        }
        $error = 'Benutzername oder Passwort ist nicht korrekt.';
    } catch (Throwable $exception) {
        error_log('[website login] ' . $exception->getMessage());
        $error = 'Die Anmeldung ist derzeit nicht erreichbar. Bitte versuche es später erneut.';
    }
}
$pageTitle = 'Anmelden – DartSystem';
require __DIR__ . '/includes/header.php';
?>
<main class="auth-layout"><section class="auth-image login-visual"><div><p class="eyebrow"><span></span>Willkommen zurück</p><h1>Weiterwerfen.<br><em>Weiterkommen.</em></h1><p>Öffne dein persönliches Profil mit Leveln, Erfahrung, Fortschritt und Highscores.</p></div></section><section class="auth-panel"><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><p class="kicker">Spielerkonto</p><h2>Anmelden</h2><p>Mit deinem DartSystem-Konto weitermachen.</p><?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?><label>Benutzername<input name="username" required autocomplete="username" autofocus value="<?= e($_POST['username'] ?? '') ?>"></label><label>Passwort<input name="password" type="password" required autocomplete="current-password"></label><button class="button">Anmelden <span>→</span></button><p class="form-foot">Noch kein Konto? <a href="/register.php">Jetzt registrieren</a></p></form></section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
