<?php
declare(strict_types=1);
$pageTitle = 'Training – DartSystem';
$pageDescription = 'Trainiere Level, Präzision und Checkouts mit DartSystem und verfolge deinen Fortschritt online.';
$activePage = 'training';
require __DIR__ . '/includes/header.php';
?>
<main>
    <section class="page-hero"><p class="eyebrow"><span></span>Training im Unity-Programm</p><h1>Jeder Wurf bringt dich <em>ein Level weiter.</em></h1><p>Das Programm kombiniert Spielgefühl und Trainingsstruktur. Ergebnisse werden über die Render-API sicher deinem TiDB-Spielerprofil zugeordnet.</p></section>
    <section class="section photo-section"><div class="feature-photo" role="img" aria-label="Dartspieler beim konzentrierten Training"></div><div class="photo-copy"><p class="kicker">Strukturiert statt zufällig</p><h2>Keine Session ohne Ziel.</h2><p>Beginne mit grundlegender Präzision, arbeite dich durch steigende Schwierigkeitsgrade und sichere deine Bestwerte in der Online-Rangliste.</p><ul class="check-list"><li>Über 100 vorbereitete Level</li><li>Trefferquote und Versuche pro Aufgabe</li><li>Persönliche Erfahrung und Spielerlevel</li><li>Highscores je Level</li></ul><a class="button" href="<?= e(mega_download_url()) ?>" target="_blank" rel="noopener">Programm herunterladen <span>↗</span></a></div></section>
    <section class="section training-cards"><article><span>01</span><h3>Präzision</h3><p>Trainiere einzelne Segmente und sieh sofort, wie stabil deine Trefferquote wird.</p></article><article><span>02</span><h3>Konstanz</h3><p>Wiederhole Aufgaben und verwandle gute Würfe in ein verlässliches Spiel.</p></article><article><span>03</span><h3>Wettbewerb</h3><p>Spiele auf Score, speichere Bestleistungen und klettere in der Rangliste.</p></article></section>
    <section class="final-cta"><p class="eyebrow"><span></span>Dein Fortschritt wartet</p><h2>Installieren. Einloggen.<br><em>Loswerfen.</em></h2><a class="button" href="/register.php">Konto erstellen <span>→</span></a><small>Dein Konto funktioniert in Programm und Website.</small></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
