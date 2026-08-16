<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
$sessionUser = require_user();
$profile = $sessionUser;
$progress = [];
$highscores = [];
$dbError = null;
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('SELECT id, username, level, experience, created_at, last_login FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $sessionUser['id']]);
    $profile = $stmt->fetch() ?: $sessionUser;
    $_SESSION['user'] = array_merge($sessionUser, $profile);
    $stmt = $pdo->prepare('SELECT level_id, darts_thrown, successful_hits, accuracy, attempts, completed, completed_at FROM progress WHERE user_id = :id ORDER BY last_updated DESC LIMIT 12');
    $stmt->execute(['id' => $profile['id']]);
    $progress = $stmt->fetchAll();
    $stmt = $pdo->prepare('SELECT level_id, MAX(score) AS score, MAX(created_at) AS created_at FROM highscores WHERE user_id = :id GROUP BY level_id ORDER BY score DESC LIMIT 8');
    $stmt->execute(['id' => $profile['id']]);
    $highscores = $stmt->fetchAll();
} catch (Throwable $exception) {
    error_log('[account] ' . $exception->getMessage());
    $dbError = 'Aktuelle Profildaten konnten vorübergehend nicht vollständig geladen werden.';
}
$totalDarts = array_sum(array_map(static fn(array $row): int => (int)($row['darts_thrown'] ?? 0), $progress));
$accuracyValues = array_filter(array_map(static fn(array $row): float => (float)($row['accuracy'] ?? 0), $progress));
$averageAccuracy = $accuracyValues ? array_sum($accuracyValues) / count($accuracyValues) : 0;
$pageTitle = 'Mein Profil – DartSystem';
require __DIR__ . '/includes/header.php';
?>
<main class="account-page"><section class="account-head account-visual"><div><p class="kicker">Dein Spielerprofil</p><h1>Guten Tag, <em><?= e($profile['username'] ?? '') ?>.</em></h1><p>Deine aktuellen Werte, Bestleistungen und nächsten Ziele auf einen Blick.</p></div><div><a class="button" href="/download.php" target="_blank" rel="noopener">DartSystem öffnen ↗</a><form method="post" action="/logout.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="outline-button">Abmelden</button></form></div></section><?php if ($dbError): ?><div class="notice error"><?= e($dbError) ?></div><?php endif; ?><section class="account-stats"><article><span>Spielerlevel</span><strong><?= (int)($profile['level'] ?? 1) ?></strong><small>DartSystem-Level</small></article><article><span>Erfahrung</span><strong><?= number_format((int)($profile['experience'] ?? 0), 0, ',', '.') ?></strong><small>Gesammelte XP</small></article><article><span>Genauigkeit</span><strong><?= number_format($averageAccuracy, 1, ',', '.') ?>%</strong><small>Letzte <?= count($progress) ?> Level</small></article><article><span>Erfasste Darts</span><strong><?= number_format($totalDarts, 0, ',', '.') ?></strong><small>Aktueller Stand</small></article></section><div class="account-grid"><section class="data-panel"><div class="panel-heading"><div><p class="kicker">Training</p><h2>Letzter Fortschritt</h2></div></div><?php if (!$progress): ?><div class="empty-state"><b>Noch kein Fortschritt</b><span>Spiele dein erstes Level und starte deine Statistik.</span></div><?php else: ?><div class="data-list"><?php foreach ($progress as $row): ?><article><div><b><?= e($row['level_id'] ?? '') ?></b><small><?= (int)($row['darts_thrown'] ?? 0) ?> Darts · <?= (int)($row['attempts'] ?? 0) ?> Versuche</small></div><strong><?= number_format((float)($row['accuracy'] ?? 0), 1, ',', '.') ?>%</strong></article><?php endforeach; ?></div><?php endif; ?></section><section class="data-panel"><div class="panel-heading"><div><p class="kicker">Bestwerte</p><h2>Highscores</h2></div><a href="/rangliste.php">Rangliste →</a></div><?php if (!$highscores): ?><div class="empty-state"><b>Noch keine Highscores</b><span>Deine Bestwerte erscheinen hier.</span></div><?php else: ?><div class="data-list highscores"><?php foreach ($highscores as $index => $row): ?><article><span>#<?= $index + 1 ?></span><div><b><?= e($row['level_id'] ?? '') ?></b><small><?= e(date('d.m.Y', strtotime((string)($row['created_at'] ?? 'now')))) ?></small></div><strong><?= number_format((int)($row['score'] ?? 0), 0, ',', '.') ?></strong></article><?php endforeach; ?></div><?php endif; ?></section></div></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
