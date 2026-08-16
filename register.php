<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('/account.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $confirmation = (string)($_POST['password_confirmation'] ?? '');
    if (!preg_match('/^[a-zA-Z0-9_-]{3,32}$/', $username)) {
        $error = 'Der Benutzername muss 3–32 Zeichen lang sein und darf Buchstaben, Zahlen, _ und - enthalten.';
    } elseif (strlen($password) < 8) {
        $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    } elseif ($password !== $confirmation) {
        $error = 'Die Passwörter stimmen nicht überein.';
    } else {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);
            if ($stmt->fetch()) {
                $error = 'Dieser Benutzername ist bereits vergeben.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO users (username, password, created_at) VALUES (:username, :password, NOW())');
                $stmt->execute(['username' => $username, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
                flash('success', 'Dein Konto wurde erstellt. Du kannst dich jetzt anmelden.');
                redirect('/login.php');
            }
        } catch (Throwable $exception) {
            error_log('[website register] ' . $exception->getMessage());
            $error = 'Die Registrierung ist derzeit nicht erreichbar. Bitte versuche es später erneut.';
        }
    }
}
$pageTitle = 'Registrieren – DartSystem';
require __DIR__ . '/includes/header.php';
?>
<main class="auth-layout"><section class="auth-image register"><div><p class="eyebrow"><span></span>Dein Spielerprofil</p><h1>Ein Konto.<br><em>Alle Fortschritte.</em></h1><p>Dein Konto verbindet Unity-Programm, Online-Ranglisten und Website-Dashboard.</p></div></section><section class="auth-panel"><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><p class="kicker">Kostenlos starten</p><h2>Registrieren</h2><p>Erstelle deine DartSystem-Spieleridentität.</p><?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?><label>Benutzername<input name="username" required minlength="3" maxlength="32" pattern="[a-zA-Z0-9_-]+" autocomplete="username" autofocus value="<?= e($_POST['username'] ?? '') ?>"></label><label>Passwort<input name="password" type="password" required minlength="8" autocomplete="new-password"></label><label>Passwort wiederholen<input name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></label><button class="button">Konto erstellen <span>→</span></button><p class="form-foot">Bereits registriert? <a href="/login.php">Zur Anmeldung</a></p></form></section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
