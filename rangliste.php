<?php
declare(strict_types=1);
$pageTitle = 'Online-Rangliste – DartSystem';
$pageDescription = 'Die besten DartSystem-Spieler und Highscores pro Level.';
$activePage = 'rangliste';
require __DIR__ . '/includes/header.php';
$levelId = trim((string)($_GET['level'] ?? 'level_1'));
$levelId = preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $levelId) ? $levelId : 'level_1';
$scores = [];
$dbError = null;
try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare('SELECT u.username, h.score, h.created_at FROM highscores h JOIN users u ON h.user_id = u.id WHERE h.level_id = :level ORDER BY h.score DESC, h.created_at ASC LIMIT 100');
    $stmt->execute(['level' => $levelId]);
    $scores = $stmt->fetchAll();
} catch (Throwable $error) {
    $dbError = 'Die Rangliste ist vorübergehend nicht erreichbar.';
    error_log('[rangliste] ' . $error->getMessage());
}
?>
<main>
    <section class="page-hero compact"><p class="eyebrow"><span></span>Online-Wettbewerb</p><h1>Die besten Würfe <em>der Community.</em></h1><p>Wähle ein Level und vergleiche die aktuellen Bestwerte direkt aus TiDB Cloud.</p></section>
    <section class="leaderboard-section"><form class="level-search" method="get"><label for="level">Level-ID</label><input id="level" name="level" value="<?= e($levelId) ?>" maxlength="64"><button class="button">Rangliste laden</button></form><?php if ($dbError): ?><div class="notice error"><?= e($dbError) ?></div><?php elseif (!$scores): ?><div class="empty-state"><b>Noch keine Highscores für <?= e($levelId) ?></b><span>Spiele das Level im Programm und sichere dir den ersten Platz.</span></div><?php else: ?><div class="leaderboard"><div class="leaderboard-head"><span>Platz</span><span>Spieler</span><span>Score</span><span>Datum</span></div><?php foreach ($scores as $index => $score): ?><div><strong>#<?= $index + 1 ?></strong><span><?= e($score['username'] ?? '') ?></span><b><?= number_format((int)($score['score'] ?? 0), 0, ',', '.') ?></b><small><?= e(date('d.m.Y', strtotime((string)($score['created_at'] ?? 'now')))) ?></small></div><?php endforeach; ?></div><?php endif; ?></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
