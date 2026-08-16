<?php
declare(strict_types=1);
$pageTitle = 'DartSystem Funktionen – Professionelles Darttraining';
$pageDescription = 'Entdecke Level-System, realistische Dart-Physik, Ranglisten, Statistiken und adaptive Trainingsmodi von DartSystem.';
$activePage = 'produkt';
require __DIR__ . '/includes/header.php';
?>
<main>
    <section class="page-hero visual-hero product-hero"><div class="page-hero-copy"><p class="eyebrow"><span></span>Dein Spiel im Mittelpunkt</p><h1>Ein Dart-Erlebnis, das deinen Wurf <em>wirklich versteht.</em></h1><p>Präzises Spielgefühl, klare Trainingsziele und persönliche Bestwerte führen dich Schritt für Schritt weiter.</p></div></section>
    <section class="section feature-grid">
        <?php foreach ([
            ['01','Präzises Spielgefühl','Glaubwürdige Flugbahnen, klare Treffer und eine direkte Rückmeldung bei jedem Wurf.'],
            ['02','Level-System','Meistere strukturierte Aufgaben, sammle Erfahrung und steigere dein Spielerlevel.'],
            ['03','Online-Ranglisten','Vergleiche deine Bestwerte pro Level mit Spielern aus der DartSystem-Community.'],
            ['04','Fortschrittsanalyse','Darts, Trefferquote, Versuche und abgeschlossene Level werden in deinem Profil zusammengeführt.'],
            ['05','Lizenzschutz','Gerätegebundene Lizenzen schützen den Zugang und lassen sich zentral verwalten.'],
            ['06','Einfacher Einstieg','Schnell eingerichtet, verständlich bedienbar und auf dein Training fokussiert.'],
        ] as [$number,$title,$copy]): ?>
            <article><span><?= e($number) ?></span><div class="feature-icon">↗</div><h3><?= e($title) ?></h3><p><?= e($copy) ?></p></article>
        <?php endforeach; ?>
    </section>
    <section class="section workflow"><div><p class="kicker">Dein Weg</p><h2>Vom ersten Level zum sichtbaren Fortschritt.</h2></div><ol><li><b>01</b><span><strong>Konto erstellen</strong><small>Dein persönliches Profil ist sofort bereit.</small></span></li><li><b>02</b><span><strong>DartSystem installieren</strong><small>Aktuelle Version herunterladen und starten.</small></span></li><li><b>03</b><span><strong>Level spielen</strong><small>Ergebnisse und Präzision automatisch festhalten.</small></span></li><li><b>04</b><span><strong>Leistung vergleichen</strong><small>Fortschritt und Ranglisten jederzeit ansehen.</small></span></li></ol></section>
    <section class="product-band modern-band"><div><p class="kicker">Bereit für dein nächstes Level?</p><h2>Installation laden.<br><em>Fortschritt starten.</em></h2><p>Starte mit deiner persönlichen Trainingsreise und bring mehr Struktur in jede Session.</p><a class="button" href="/download.php" target="_blank" rel="noopener">Jetzt herunterladen <span>↗</span></a></div><div class="benefit-stack"><article><span>01</span><div><strong>Fokussiert trainieren</strong><small>Klare Aufgaben statt planloser Würfe.</small></div></article><article><span>02</span><div><strong>Fortschritt erkennen</strong><small>Werte und Bestleistungen auf einen Blick.</small></div></article><article><span>03</span><div><strong>Motivation behalten</strong><small>Level, Ziele und Ranglisten treiben dich an.</small></div></article></div></section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
