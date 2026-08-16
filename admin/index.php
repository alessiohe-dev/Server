<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

$error = '';
$adminUser = env_value('ADMIN_USER', 'admin') ?? 'admin';
$adminPassword = env_value('ADMIN_PASSWORD');
$adminPasswordHash = env_value('ADMIN_PASSWORD_HASH');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_login'])) {
    require_csrf();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $passwordValid = $adminPasswordHash ? password_verify($password, $adminPasswordHash) : ($adminPassword !== null && hash_equals($adminPassword, $password));
    if ($username === $adminUser && $passwordValid) {
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        redirect('/admin/');
    }
    $error = $adminPassword === null && $adminPasswordHash === null
        ? 'Der Admin-Zugang ist noch nicht vollständig konfiguriert.'
        : 'Benutzername oder Passwort ist nicht korrekt.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_logout'])) {
    require_csrf();
    unset($_SESSION['admin_authenticated']);
    session_regenerate_id(true);
    redirect('/admin/');
}

if (!is_admin()):
?>
<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administration – DartSystem</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&amp;family=DM+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><link rel="stylesheet" href="/assets/css/site.css?v=2"></head><body><main class="admin-login"><form method="post"><a class="brand brand-dark" href="/"><span class="brand-mark">D</span><span>Dart<span>System</span></span></a><p class="kicker">Geschützter Bereich</p><h1>Administration</h1><p>Spieler, Fortschritt und Programmlizenzen verwalten.</p><?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="admin_login" value="1"><label>Benutzername<input name="username" required autocomplete="username" autofocus></label><label>Passwort<input name="password" type="password" required autocomplete="current-password"></label><button class="button">Sicher anmelden →</button><a class="back-link" href="/">← Zur Website</a></form></main></body></html>
<?php exit; endif;

$message = '';
$tab = ($_GET['tab'] ?? 'players') === 'licenses' ? 'licenses' : 'players';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    try {
        $pdo = getDBConnection();
        if (isset($_POST['delete_user'])) {
            $pdo->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => (int)$_POST['delete_user']]);
            $message = 'Spieler wurde gelöscht.';
        } elseif (isset($_POST['toggle_license'])) {
            $pdo->prepare('UPDATE licenses SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = :id')->execute(['id' => (int)$_POST['toggle_license']]);
            $message = 'Lizenzstatus wurde geändert.';
            $tab = 'licenses';
        } elseif (isset($_POST['delete_license'])) {
            $pdo->prepare('DELETE FROM licenses WHERE id = :id')->execute(['id' => (int)$_POST['delete_license']]);
            $message = 'Lizenz wurde gelöscht.';
            $tab = 'licenses';
        }
    } catch (Throwable $exception) {
        error_log('[admin action] ' . $exception->getMessage());
        $error = 'Aktion konnte nicht ausgeführt werden.';
    }
}

$players = $licenses = [];
try {
    $pdo = getDBConnection();
    $players = $pdo->query('SELECT id, username, level, experience, created_at, last_login FROM users ORDER BY id DESC')->fetchAll();
    $licenses = $pdo->query('SELECT * FROM licenses ORDER BY id DESC')->fetchAll();
} catch (Throwable $exception) {
    error_log('[admin data] ' . $exception->getMessage());
    $error = 'Die Verwaltungsdaten konnten nicht geladen werden.';
}
$activeLicenses = count(array_filter($licenses, static fn(array $license): bool => (int)($license['is_active'] ?? 0) === 1));
?>
<!DOCTYPE html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin – DartSystem</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800&amp;family=DM+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><link rel="stylesheet" href="/assets/css/site.css?v=2"></head><body>
<main class="admin-page"><aside class="admin-sidebar"><a class="brand" href="/"><span class="brand-mark">D</span><span>Dart<span>System</span></span></a><small>ADMINISTRATION</small><nav><a class="<?= $tab === 'players' ? 'active' : '' ?>" href="?tab=players"><span>♙</span>Spieler<b><?= count($players) ?></b></a><a class="<?= $tab === 'licenses' ? 'active' : '' ?>" href="?tab=licenses"><span>◇</span>Lizenzen<b><?= count($licenses) ?></b></a><a href="/rangliste.php"><span>↗</span>Rangliste</a><a href="/"><span>⌂</span>Website</a></nav><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="admin-logout" name="admin_logout" value="1">↪ Abmelden</button></form></aside>
<section class="admin-content"><header><div><p>DARTSYSTEM CONTROL CENTER</p><h1>Guten Abend, <em>Admin.</em></h1></div><span class="admin-avatar">AD</span></header><?php if ($message): ?><div class="notice success"><?= e($message) ?></div><?php endif; ?><?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?><div class="admin-stats"><article><span>Registrierte Spieler</span><strong><?= count($players) ?></strong><small>Aktueller Stand</small></article><article><span>Lizenzen gesamt</span><strong><?= count($licenses) ?></strong><small>Alle Typen</small></article><article><span>Aktive Lizenzen</span><strong><?= $activeLicenses ?></strong><small><?= count($licenses) ? round($activeLicenses / count($licenses) * 100) : 0 ?>% aktiv</small></article><article><span>Inaktiv / abgelaufen</span><strong><?= count($licenses) - $activeLicenses ?></strong><small>Prüfen</small></article></div>
<section class="admin-table-card"><div class="admin-table-head"><div><p><?= $tab === 'players' ? 'SPIELERVERWALTUNG' : 'LIZENZVERWALTUNG' ?></p><h2><?= $tab === 'players' ? 'Alle Spieler' : 'Alle Lizenzen' ?></h2></div><?php if ($tab === 'licenses'): ?><button class="button" type="button" id="openLicense">+ Neue Lizenz</button><?php endif; ?></div><div class="admin-table-scroll"><table><?php if ($tab === 'players'): ?><thead><tr><th>Spieler</th><th>Level</th><th>Erfahrung</th><th>Registriert</th><th>Letzter Login</th><th>Aktionen</th></tr></thead><tbody><?php foreach ($players as $player): ?><tr><td><span class="user-cell"><i><?= e(strtoupper(substr((string)$player['username'],0,2))) ?></i><b><?= e($player['username']) ?><small>#<?= (int)$player['id'] ?></small></b></span></td><td><span class="level-badge">LVL <?= (int)($player['level'] ?? 1) ?></span></td><td><?= number_format((int)($player['experience'] ?? 0),0,',','.') ?> XP</td><td><?= e(date('d.m.Y',strtotime((string)$player['created_at']))) ?></td><td><?= !empty($player['last_login']) ? e(date('d.m.Y H:i',strtotime((string)$player['last_login']))) : '—' ?></td><td><div class="row-actions"><button type="button" class="profile-button" data-user="<?= e($player['username']) ?>">Profil</button><form method="post" onsubmit="return confirm('Spieler wirklich löschen?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="danger" name="delete_user" value="<?= (int)$player['id'] ?>">Löschen</button></form></div></td></tr><?php endforeach; ?></tbody><?php else: ?><thead><tr><th>Lizenz</th><th>Kunde</th><th>Typ</th><th>Status</th><th>Ablauf</th><th>Aktivierungen</th><th>Aktionen</th></tr></thead><tbody><?php foreach ($licenses as $license): $active=(int)($license['is_active']??0)===1; ?><tr><td><code><?= e($license['license_key'] ?? '') ?></code></td><td><b><?= e($license['customer_name'] ?? '—') ?></b><small class="sub-cell"><?= e($license['customer_email'] ?? '') ?></small></td><td><span class="level-badge"><?= e($license['license_type'] ?? 'full') ?></span></td><td><span class="status <?= $active ? 'active' : 'inactive' ?>"><?= $active ? 'Aktiv' : 'Inaktiv' ?></span></td><td><?= !empty($license['expires_at']) ? e(date('d.m.Y',strtotime((string)$license['expires_at']))) : 'Unbegrenzt' ?></td><td><?= (int)($license['activations']??0) ?> / <?= (int)($license['max_activations']??1) ?></td><td><div class="row-actions"><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button name="toggle_license" value="<?= (int)$license['id'] ?>"><?= $active ? 'Pausieren' : 'Aktivieren' ?></button></form><button type="button" data-copy="<?= e($license['license_key']??'') ?>">Kopieren</button><form method="post" onsubmit="return confirm('Lizenz wirklich löschen?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="danger" name="delete_license" value="<?= (int)$license['id'] ?>">Löschen</button></form></div></td></tr><?php endforeach; ?></tbody><?php endif; ?></table></div><footer><span><?= $tab === 'players' ? count($players) : count($licenses) ?> Einträge</span><span>Live aus Aktueller Stand</span></footer></section></section></main>
<div class="modal-backdrop" id="licenseModal" hidden><form class="admin-modal" id="licenseForm"><div class="modal-title"><div><p>NEUER ZUGANG</p><h2>Lizenz generieren</h2></div><button type="button" data-close>×</button></div><label>Kundenname<input required name="customer_name" autofocus></label><label>E-Mail<input name="customer_email" type="email"></label><label>Device ID<input name="device_id"></label><div class="form-row"><label>Lizenztyp<select name="license_type"><option value="full">Full</option><option value="trial">Trial</option><option value="subscription">Subscription</option><option value="club">Club</option></select></label><label>Aktivierungen<input name="max_activations" type="number" min="1" max="999" value="1"></label></div><label>Gültigkeit<select name="expires_in"><option value="30">30 Tage</option><option value="90">90 Tage</option><option value="365" selected>1 Jahr</option><option value="0">Unbegrenzt</option></select></label><div class="modal-actions"><button type="button" class="outline-button" data-close>Abbrechen</button><button class="button">Generieren</button></div><div class="license-output" hidden></div></form></div>
<div class="modal-backdrop" id="profileModal" hidden><div class="admin-modal profile-modal"><div class="modal-title"><div><p>SPIELERPROFIL</p><h2 id="profileName">Profil</h2></div><button type="button" data-profile-close>×</button></div><div id="profileBody">Wird geladen…</div></div></div>
<script>window.DARTSYSTEM_ADMIN={csrf:<?= json_encode(csrf_token()) ?>};</script><script src="/assets/js/admin.js?v=2"></script></body></html>
