<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
$pageTitle = $pageTitle ?? 'DartSystem – Dein Spiel. Messbar besser.';
$pageDescription = $pageDescription ?? 'Professionelles Darttraining, Ranglisten und Fortschritt in einem System.';
$activePage = $activePage ?? '';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="theme-color" content="#071426">
    <link rel="canonical" href="<?= e(app_url(ltrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/'))) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="de_DE">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:image" content="<?= e(app_url('assets/images/og.jpg')) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="/assets/images/logo-mark.svg?v=2" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/site.css?v=6">
    <script defer src="/assets/js/site.js?v=5"></script>
</head>
<body>
<header class="site-header">
    <a class="brand" href="/" aria-label="DartSystem Startseite"><img class="brand-mark" src="/assets/images/logo-mark.svg?v=2" alt="" width="38" height="38"><span>Dart<span>System</span></span></a>
    <button class="menu-button" type="button" aria-label="Menü öffnen" aria-expanded="false">☰</button>
    <nav class="desktop-nav" aria-label="Hauptnavigation">
        <a class="<?= $activePage === 'produkt' ? 'active' : '' ?>" href="/produkt.php">Produkt</a>
        <a class="<?= $activePage === 'training' ? 'active' : '' ?>" href="/training.php">Training</a>
        <a class="<?= $activePage === 'rangliste' ? 'active' : '' ?>" href="/rangliste.php">Rangliste</a>
        <a class="<?= $activePage === 'preise' ? 'active' : '' ?>" href="/preise.php">Preise</a>
        <a class="<?= $activePage === 'hilfe' ? 'active' : '' ?>" href="/hilfe.php">Hilfe</a>
    </nav>
    <div class="header-actions">
        <?php if ($user): ?>
            <a class="button button-small" href="/account.php"><?= e($user['username'] ?? 'Konto') ?> <span>↗</span></a>
        <?php else: ?>
            <a class="button button-small" href="/login.php">Anmelden <span>↗</span></a>
        <?php endif; ?>
    </div>
</header>
<?php if ($flashMessage = consume_flash()): ?>
    <div class="flash <?= e($flashMessage['type'] ?? 'info') ?>" role="status"><?= e($flashMessage['message'] ?? '') ?></div>
<?php endif; ?>
