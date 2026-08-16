<?php
declare(strict_types=1);
$pageTitle = 'DartSystem Funktionen – Professionelles Darttraining';
$pageDescription = 'Entdecke Level-System, realistische Dart-Physik, Ranglisten, Statistiken und adaptive Trainingsmodi von DartSystem.';
$activePage = 'produkt';
require __DIR__ . '/includes/header.php';
?>
<main>
    <section class="page-hero"><p class="eyebrow"><span></span>Das System hinter deinem Spiel</p><h1>Ein Dart-Programm, das deinen Wurf <em>wirklich versteht.</em></h1><p>DartSystem verbindet realistische Unity-Physik, schnelle Fortschrittsspeicherung und weltweite Ranglisten mit einem klaren Trainingsweg.</p></section>
    <section class="section feature-grid">
        <?php foreach ([
            ['01','Realistische Dart-Physik','Präzise Flugbahnen, glaubwürdige Treffer und ein direktes Spielgefühl – entwickelt in Unity.'],
            ['02','Level-System','Meistere strukturierte Aufgaben, sammle Erfahrung und steigere dein Spielerlevel.'],
            ['03','Online-Ranglisten','Vergleiche deine Bestwerte pro Level mit Spielern aus der DartSystem-Community.'],
            ['04','Fortschrittsanalyse','Darts, Trefferquote, Versuche und abgeschlossene Level werden in deinem Profil zusammengeführt.'],
            ['05','Lizenzschutz','Gerätegebundene Lizenzen schützen den Zugang und lassen sich zentral verwalten.'],
            ['06','Plattformübergreifend','Vorbereitet für Windows, macOS, Linux, Android und weitere Unity-Builds.'],
        ] as [$number,$title,$copy]): ?>
            <article><span><?= e($number) ?></span><div class="feature-icon">↗</div><h3><?= e($title) ?></h3><p><?= e($copy) ?></p></article>
        <?php endforeach; ?>
    </section>
    <section class="section workflow"><div><p class="kicker">Dein Weg</p><h2>Vom ersten Level zum klaren Fortschritt.</h2></div><ol><li><b>01</b><span><strong>Konto erstellen</strong><small>Ein Profil verbindet Programm und Website.</small></span></li><li><b>02</b><span><strong>DartSystem installieren</strong><small>Den aktuellen Build sicher über MEGA laden.</small></span></li><li><b>03</b><span><strong>Level spielen</strong><small>Ergebnisse und Präzision automatisch speichern.</small></span></li><li><b>04</b><span><strong>Leistung vergleichen</strong><small>Profil und Ranglisten auf der Website verfolgen.</small></span></li></ol></section>
    <section class="product-band"><div><p class="kicker">Bereit für dein nächstes Level?</p><h2>Installation laden.<br><em>Fortschritt starten.</em></h2><p>Die aktuelle Programmversion und verfügbare Plattform-Builds findest du im offiziellen Downloadordner.</p><a class="button" href="<?= e(mega_download_url()) ?>" target="_blank" rel="noopener">Download über MEGA <span>↗</span></a></div><div class="metric-stack"><article><small>Technologie</small><strong>Unity</strong><i>Professionelle Echtzeit-Engine</i></article><article><small>Backend</small><strong>Render</strong><i>PHP 8.2 & Apache</i></article><article><small>Datenbank</small><strong>TiDB</strong><i>Cloud MySQL-kompatibel</i></article></div></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
