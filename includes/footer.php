<?php declare(strict_types=1); ?>
<footer class="site-footer">
    <div class="footer-main">
        <div>
            <a class="brand" href="/"><span class="brand-mark">D</span><span>Dart<span>System</span></span></a>
            <p>Das professionelle Dart-Programm für strukturiertes Training, echte Fortschritte und weltweite Ranglisten.</p>
        </div>
        <div><b>Produkt</b><a href="/produkt.php">Funktionen</a><a href="/training.php">Training</a><a href="/rangliste.php">Rangliste</a><a href="<?= e(mega_download_url()) ?>" target="_blank" rel="noopener">Download</a></div>
        <div><b>Support</b><a href="/hilfe.php">Hilfe & Kontakt</a><a href="/login.php">Anmelden</a><a href="/admin/">Administration</a></div>
        <div><b>Rechtliches</b><a href="/impressum.php">Impressum</a><a href="/datenschutz.php">Datenschutz</a><a href="https://github.com/alessiohe-dev/Server" target="_blank" rel="noopener">GitHub</a></div>
    </div>
    <div class="footer-bottom"><span>© <?= date('Y') ?> DartSystem</span><span>Entwickelt in Deutschland · Powered by Unity, Render & TiDB Cloud</span></div>
</footer>
</body>
</html>
