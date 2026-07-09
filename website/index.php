<?php
// ──────────────────────────────────────────────────────────────
// DARTSYSTEM – Moderne LED-Style Landingpage
// Eine einzige PHP-Datei mit integriertem HTML, CSS & JS
// ──────────────────────────────────────────────────────────────

// ─── KEINE PHP-LOGIK (rein statische Seite) ───
// Alle Inhalte sind direkt in den HTML-Templates hinterlegt.
// ──────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎯 DartSystem – Das ultimative Dart-Erlebnis</title>
    
    <!-- ─── Google Fonts (Inter) ─── -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">
    
    <!-- ─── Font Awesome 6 (Icons) ─── -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* ──────────────────────────────────────────────────────────────
           GLOBALE RESETS & BASICS
           ────────────────────────────────────────────────────────────── */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #0a0e1a;
            color: #e2e8f0;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        /* ─── Scroll-Verhalten ─── */
        html {
            scroll-behavior: smooth;
        }
        
        /* ──────────────────────────────────────────────────────────────
           HEADER / NAVIGATION
           ────────────────────────────────────────────────────────────── */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(10, 14, 26, 0.85);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(59, 130, 246, 0.15);
            transition: background 0.3s, box-shadow 0.3s;
        }
        
        header.scrolled {
            background: rgba(10, 14, 26, 0.95);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.6);
        }
        
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -0.5px;
        }
        
        .logo .highlight {
            color: #ef4444;
        }
        
        .logo i {
            font-size: 28px;
            color: #3b82f6;
            filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.5));
        }
        
        nav {
            display: flex;
            gap: 8px;
        }
        
        nav a {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #94a3b8;
            transition: all 0.2s;
        }
        
        nav a:hover {
            background: rgba(59, 130, 246, 0.1);
            color: #f1f5f9;
        }
        
        nav a.btn-outline {
            border: 1px solid #3b82f6;
            color: #3b82f6;
            padding: 8px 20px;
            border-radius: 30px;
            background: transparent;
            transition: all 0.3s;
        }
        
        nav a.btn-outline:hover {
            background: #3b82f6;
            color: #fff;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
        }
        
        /* ─── Mobile Menu Toggle ─── */
        .menu-toggle {
            display: none;
            font-size: 24px;
            background: none;
            border: none;
            color: #e2e8f0;
            cursor: pointer;
            padding: 4px 8px;
        }
        
        @media (max-width: 768px) {
            nav {
                display: none;
                flex-direction: column;
                width: 100%;
                background: #0a0e1a;
                padding: 20px 0;
                position: absolute;
                top: 72px;
                left: 0;
                border-bottom: 1px solid #1a2332;
            }
            nav.open {
                display: flex;
            }
            .menu-toggle {
                display: block;
            }
            nav a {
                padding: 12px 24px;
                width: 100%;
                text-align: center;
            }
            nav a.btn-outline {
                border: none;
                background: #3b82f6;
                color: #fff;
                margin: 8px 24px;
                text-align: center;
            }
        }
        
        /* ──────────────────────────────────────────────────────────────
           HERO SECTION
           ────────────────────────────────────────────────────────────── */
        .hero {
            padding: 140px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -30%;
            left: -20%;
            width: 80%;
            height: 120%;
            background: radial-gradient(ellipse at center, rgba(59, 130, 246, 0.08), transparent 70%);
            pointer-events: none;
            z-index: 0;
        }
        
        .hero .container {
            position: relative;
            z-index: 1;
        }
        
        .hero h1 {
            font-size: clamp(48px, 8vw, 80px);
            font-weight: 900;
            letter-spacing: -2px;
            line-height: 1.1;
            margin-bottom: 16px;
        }
        
        .hero h1 .highlight {
            color: #ef4444;
            text-shadow: 0 0 40px rgba(239, 68, 68, 0.3);
        }
        
        .hero p {
            font-size: clamp(18px, 2.5vw, 24px);
            color: #94a3b8;
            max-width: 640px;
            margin: 0 auto 32px;
        }
        
        .hero .btn-primary {
            display: inline-block;
            padding: 16px 48px;
            background: #3b82f6;
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            border-radius: 50px;
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.35);
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            animation: pulse-glow 2.5s infinite;
        }
        
        .hero .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 0 50px rgba(59, 130, 246, 0.6);
        }
        
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
            50% { box-shadow: 0 0 50px rgba(59, 130, 246, 0.6); }
            100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
        }
        
        .hero .badge {
            display: inline-block;
            margin-top: 24px;
            padding: 8px 20px;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 30px;
            font-size: 14px;
            color: #94a3b8;
        }
        
        .hero .badge i {
            color: #3b82f6;
            margin-right: 6px;
        }
        
        /* ──────────────────────────────────────────────────────────────
           PLATTFORMEN (GRID)
           ────────────────────────────────────────────────────────────── */
        .section-title {
            font-size: 36px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        
        .section-sub {
            text-align: center;
            color: #94a3b8;
            font-size: 18px;
            margin-bottom: 48px;
        }
        
        .platform-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 40px 0 60px;
        }
        
        .platform-card {
            background: linear-gradient(145deg, #111a33, #0f172a);
            border: 1px solid rgba(59, 130, 246, 0.08);
            border-radius: 20px;
            padding: 32px 24px;
            text-align: center;
            transition: all 0.35s ease;
            position: relative;
            overflow: hidden;
        }
        
        .platform-card::after {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 20px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), transparent 60%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s;
        }
        
        .platform-card:hover::after {
            opacity: 1;
        }
        
        .platform-card:hover {
            transform: translateY(-6px);
            border-color: rgba(59, 130, 246, 0.2);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.5);
        }
        
        .platform-icon {
            font-size: 48px;
            margin-bottom: 12px;
            display: inline-block;
            filter: drop-shadow(0 0 20px rgba(59, 130, 246, 0.15));
        }
        
        .platform-card h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .platform-card p {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .platform-card .btn-download {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 28px;
            border-radius: 30px;
            font-size: 14px;
            font-weight: 600;
            color: #3b82f6;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.15);
            transition: all 0.3s;
        }
        
        .platform-card .btn-download:hover {
            background: #3b82f6;
            color: #fff;
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.3);
            transform: scale(1.03);
        }
        
        /* ──────────────────────────────────────────────────────────────
           HIGHLIGHTS (FEATURES)
           ────────────────────────────────────────────────────────────── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            margin: 40px 0 60px;
        }
        
        .feature-card {
            background: rgba(17, 26, 51, 0.6);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.03);
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
            transition: all 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-4px);
            border-color: rgba(59, 130, 246, 0.15);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        }
        
        .feature-icon {
            font-size: 36px;
            color: #3b82f6;
            margin-bottom: 12px;
            display: inline-block;
            filter: drop-shadow(0 0 12px rgba(59, 130, 246, 0.2));
        }
        
        .feature-card h4 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }
        
        .feature-card p {
            font-size: 14px;
            color: #94a3b8;
        }
        
        /* ──────────────────────────────────────────────────────────────
           SYSTEMANFORDERUNGEN
           ────────────────────────────────────────────────────────────── */
        .requirements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin: 40px 0 60px;
        }
        
        .req-card {
            background: #111a33;
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
        }
        
        .req-card .os-icon {
            font-size: 32px;
            margin-bottom: 8px;
            display: inline-block;
        }
        
        .req-card h5 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .req-card ul {
            list-style: none;
            font-size: 14px;
            color: #94a3b8;
            padding: 0;
        }
        
        .req-card ul li {
            padding: 2px 0;
        }
        
        /* ──────────────────────────────────────────────────────────────
           NEWS / UPDATES
           ────────────────────────────────────────────────────────────── */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 40px 0 60px;
        }
        
        .news-card {
            background: #111a33;
            border: 1px solid rgba(255, 255, 255, 0.04);
            border-radius: 16px;
            padding: 24px 20px;
            transition: all 0.3s;
        }
        
        .news-card:hover {
            border-color: rgba(59, 130, 246, 0.15);
            transform: translateY(-3px);
        }
        
        .news-card .date {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #3b82f6;
            font-weight: 600;
        }
        
        .news-card h4 {
            font-size: 18px;
            font-weight: 700;
            margin: 8px 0 4px;
        }
        
        .news-card p {
            font-size: 14px;
            color: #94a3b8;
        }
        
        /* ──────────────────────────────────────────────────────────────
           FOOTER
           ────────────────────────────────────────────────────────────── */
        footer {
            background: #060a14;
            border-top: 1px solid #111a33;
            padding: 48px 0 24px;
            margin-top: 80px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 32px;
        }
        
        .footer-brand .logo {
            font-size: 24px;
            font-weight: 800;
        }
        
        .footer-brand p {
            color: #94a3b8;
            font-size: 14px;
            margin-top: 8px;
            max-width: 300px;
        }
        
        .footer-links h6 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
        }
        
        .footer-links a {
            display: block;
            color: #94a3b8;
            font-size: 14px;
            padding: 4px 0;
            transition: color 0.2s;
        }
        
        .footer-links a:hover {
            color: #f1f5f9;
        }
        
        .footer-social {
            display: flex;
            gap: 16px;
            margin-top: 12px;
        }
        
        .footer-social a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            font-size: 18px;
            transition: all 0.3s;
        }
        
        .footer-social a:hover {
            background: #3b82f6;
            color: #fff;
            border-color: #3b82f6;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        
        .footer-bottom {
            border-top: 1px solid #111a33;
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            color: #475569;
            flex-wrap: wrap;
            gap: 8px;
        }
        
        .footer-bottom a.dashboard-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .footer-bottom a.dashboard-link:hover {
            color: #60a5fa;
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }
            .footer-bottom {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
        
        /* ──────────────────────────────────────────────────────────────
           SCROLL-TO-TOP BUTTON
           ────────────────────────────────────────────────────────────── */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #3b82f6;
            color: #fff;
            border: none;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
            transition: all 0.3s;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            z-index: 999;
        }
        
        .scroll-top.visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .scroll-top:hover {
            background: #2563eb;
            transform: scale(1.1);
            box-shadow: 0 0 40px rgba(59, 130, 246, 0.5);
        }
        
        /* ──────────────────────────────────────────────────────────────
           FADE-IN ANIMATION BEIM SCROLLEN
           ────────────────────────────────────────────────────────────── */
        .fade-section {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        
        .fade-section.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* ──────────────────────────────────────────────────────────────
           RESPONSIVE FEINJUSTIERUNGEN
           ────────────────────────────────────────────────────────────── */
        @media (max-width: 480px) {
            .hero h1 {
                font-size: 36px;
            }
            .hero .btn-primary {
                padding: 14px 32px;
                font-size: 18px;
            }
            .platform-card {
                padding: 24px 16px;
            }
        }
    </style>
</head>
<body>

<!-- ─── HEADER ─── -->
<header id="header">
    <div class="container header-inner">
        <div class="logo">
            <i class="fas fa-bullseye"></i>
            <span>Dart<span class="highlight">System</span></span>
        </div>
        <button class="menu-toggle" id="menuToggle" aria-label="Menü"><i class="fas fa-bars"></i></button>
        <nav id="mainNav">
            <a href="#features">Features</a>
            <a href="#platforms">Plattformen</a>
            <a href="#news">News</a>
            <a href="#download" class="btn-outline">Jetzt herunterladen</a>
        </nav>
    </div>
</header>

<!-- ─── HERO ─── -->
<section class="hero" id="home">
    <div class="container">
        <h1>Dart<span class="highlight">System</span></h1>
        <p>Das ultimative Dart-Erlebnis – Jetzt für deine Plattform</p>
        <a href="#download" class="btn-primary"><i class="fas fa-download" style="margin-right:10px;"></i>Jetzt herunterladen</a>
        <div class="badge">
            <i class="fas fa-check-circle"></i> Version 1.0.0 · 50 Level · Online-Ranglisten
        </div>
    </div>
</section>

<!-- ─── HIGHLIGHTS (FEATURES) ─── -->
<section id="features" class="container fade-section">
    <h2 class="section-title">Highlights</h2>
    <p class="section-sub">Was DartSystem auszeichnet</p>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-bullseye"></i></div>
            <h4>Präzise Dart-Physik</h4>
            <p>Realistische Flugbahn und präzises Zielen</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-trophy"></i></div>
            <h4>Online-Ranglisten</h4>
            <p>Vergleiche dich mit Spielern weltweit</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-layer-group"></i></div>
            <h4>Level-System</h4>
            <p>Meistere über 100 herausfordernde Level</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-users"></i></div>
            <h4>Multiplayer</h4>
            <p>Spiele gegen Freunde oder online</p>
        </div>
    </div>
</section>

<!-- ─── PLATTFORMEN (GRID) ─── -->
<section id="platforms" class="container fade-section">
    <h2 class="section-title">Plattformen</h2>
    <p class="section-sub">Wähle deine Version</p>
    <div class="platform-grid">
        <!-- Windows -->
        <div class="platform-card">
            <div class="platform-icon"><i class="fab fa-windows"></i></div>
            <h3>Windows</h3>
            <p>Für Windows 10/11</p>
            <a href="https://mega.nz/folder/j8YGkayJ#CzyaHuJY68BtI0vFQcSFrw" class="btn-download" target="_blank">
                <i class="fas fa-cloud-download-alt"></i> Download
            </a>
        </div>
        <!-- macOS -->
        <div class="platform-card">
            <div class="platform-icon"><i class="fab fa-apple"></i></div>
            <h3>macOS</h3>
            <p>Für macOS 12+</p>
            <a href="https://mega.nz/folder/j8YGkayJ#CzyaHuJY68BtI0vFQcSFrw" class="btn-download" target="_blank">
                <i class="fas fa-cloud-download-alt"></i> Download
            </a>
        </div>
        <!-- Linux -->
        <div class="platform-card">
            <div class="platform-icon"><i class="fab fa-linux"></i></div>
            <h3>Linux</h3>
            <p>Für Ubuntu, Debian, Fedora</p>
            <a href="https://mega.nz/folder/j8YGkayJ#CzyaHuJY68BtI0vFQcSFrw" class="btn-download" target="_blank">
                <i class="fas fa-cloud-download-alt"></i> Download
            </a>
        </div>
        <!-- Android -->
        <div class="platform-card">
            <div class="platform-icon"><i class="fab fa-android"></i></div>
            <h3>Android</h3>
            <p>Für Android 8+</p>
            <a href="https://mega.nz/folder/j8YGkayJ#CzyaHuJY68BtI0vFQcSFrw" class="btn-download" target="_blank">
                <i class="fas fa-cloud-download-alt"></i> Download
            </a>
        </div>
        <!-- iOS -->
        <div class="platform-card">
            <div class="platform-icon"><i class="fas fa-mobile-alt"></i></div>
            <h3>iOS / iPadOS</h3>
            <p>Für iPhone/iPad (TestFlight)</p>
            <a href="https://mega.nz/folder/j8YGkayJ#CzyaHuJY68BtI0vFQcSFrw" class="btn-download" target="_blank">
                <i class="fas fa-cloud-download-alt"></i> Download
            </a>
        </div>
        <!-- Web -->
        <div class="platform-card">
            <div class="platform-icon"><i class="fas fa-globe"></i></div>
            <h3>Web (WebGL)</h3>
            <p>Browser-Version</p>
            <a href="https://mega.nz/folder/j8YGkayJ#CzyaHuJY68BtI0vFQcSFrw" class="btn-download" target="_blank">
                <i class="fas fa-cloud-download-alt"></i> Download
            </a>
        </div>
    </div>
</section>

<!-- ─── SYSTEMANFORDERUNGEN ─── -->
<section id="requirements" class="container fade-section">
    <h2 class="section-title">Systemanforderungen</h2>
    <p class="section-sub">So läuft DartSystem auf deinem Gerät</p>
    <div class="requirements-grid">
        <div class="req-card">
            <div class="os-icon"><i class="fab fa-windows"></i></div>
            <h5>Windows</h5>
            <ul>
                <li>Windows 10/11</li>
                <li>4 GB RAM</li>
                <li>500 MB Speicher</li>
            </ul>
        </div>
        <div class="req-card">
            <div class="os-icon"><i class="fab fa-apple"></i></div>
            <h5>macOS</h5>
            <ul>
                <li>macOS 12+</li>
                <li>4 GB RAM</li>
                <li>500 MB Speicher</li>
            </ul>
        </div>
        <div class="req-card">
            <div class="os-icon"><i class="fab fa-linux"></i></div>
            <h5>Linux</h5>
            <ul>
                <li>Ubuntu 20.04+</li>
                <li>4 GB RAM</li>
                <li>500 MB Speicher</li>
            </ul>
        </div>
        <div class="req-card">
            <div class="os-icon"><i class="fab fa-android"></i></div>
            <h5>Android</h5>
            <ul>
                <li>Android 8+</li>
                <li>3 GB RAM</li>
                <li>200 MB Speicher</li>
            </ul>
        </div>
    </div>
</section>

<!-- ─── NEWS / UPDATES ─── -->
<section id="news" class="container fade-section">
    <h2 class="section-title">News & Updates</h2>
    <p class="section-sub">Was gibt's Neues?</p>
    <div class="news-grid">
        <div class="news-card">
            <div class="date">15. Juli 2025</div>
            <h4>Version 1.0.0 Release</h4>
            <p>Das Spiel ist offiziell gestartet! Erlebe die erste Version mit 50 Leveln.</p>
        </div>
        <div class="news-card">
            <div class="date">02. August 2025</div>
            <h4>Neue Level hinzugefügt</h4>
            <p>Update 1.1 bringt 20 neue Level und verbesserte Physik.</p>
        </div>
        <div class="news-card">
            <div class="date">20. August 2025</div>
            <h4>Bugfixes und Verbesserungen</h4>
            <p>Performance-Optimierungen und Fehlerbehebungen für alle Plattformen.</p>
        </div>
    </div>
</section>

<!-- ─── DOWNLOAD SECTION (Call-to-Action) ─── -->
<section id="download" class="container fade-section" style="text-align:center; padding: 60px 0;">
    <h2 class="section-title">Bereit für den Wurf?</h2>
    <p style="color:#94a3b8; max-width:500px; margin: 0 auto 32px;">Lade DartSystem jetzt kostenlos herunter und starte dein Dart-Abenteuer.</p>
    <a href="#platforms" class="btn-primary" style="display:inline-block;"><i class="fas fa-download" style="margin-right:10px;"></i>Alle Plattformen anzeigen</a>
</section>

<!-- ─── FOOTER ─── -->
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="logo">
                    <i class="fas fa-bullseye" style="font-size:24px;"></i>
                    <span>Dart<span class="highlight">System</span></span>
                </div>
                <p>Das ultimative Dart-Erlebnis – entwickelt mit Leidenschaft in Deutschland.</p>
                <div class="footer-social">
                    <a href="#" aria-label="GitHub"><i class="fab fa-github"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="Discord"><i class="fab fa-discord"></i></a>
                </div>
            </div>
            <div class="footer-links">
                <h6>Rechtliches</h6>
                <a href="#">Impressum</a>
                <a href="#">Datenschutz</a>
                <a href="#">AGB</a>
                <a href="#">Kontakt</a>
            </div>
            <div class="footer-links">
                <h6>Support</h6>
                <a href="#">FAQ</a>
                <a href="#">Forum</a>
                <a href="#">Bug melden</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; 2025 DartSystem – Alle Rechte vorbehalten</span>
            <span>
                <a href="https://dartsystem.alessiohennecke.de/website/dashboard/" class="dashboard-link">
                    📊 Dashboard
                </a>
                <span style="margin:0 8px;">·</span>
                Made with <i class="fas fa-heart" style="color:#ef4444;"></i> in Germany
            </span>
        </div>
    </div>
</footer>

<!-- ─── SCROLL-TO-TOP BUTTON ─── -->
<button id="scrollTopBtn" class="scroll-top" aria-label="Nach oben"><i class="fas fa-chevron-up"></i></button>

<!-- ─── JAVASCRIPT ─── -->
<script>
    // ─── Mobile Menu Toggle ───
    document.getElementById('menuToggle').addEventListener('click', function() {
        document.getElementById('mainNav').classList.toggle('open');
    });

    // ─── Header Shadow on Scroll ───
    window.addEventListener('scroll', function() {
        const header = document.getElementById('header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // ─── Scroll-to-Top Button ───
    const scrollBtn = document.getElementById('scrollTopBtn');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 400) {
            scrollBtn.classList.add('visible');
        } else {
            scrollBtn.classList.remove('visible');
        }
    });
    scrollBtn.addEventListener('click', function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // ─── Fade-In Sections (Intersection Observer) ───
    const fadeSections = document.querySelectorAll('.fade-section');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.15 });
    fadeSections.forEach(section => observer.observe(section));

    // ─── Smooth Scroll für Navigation (ohne #-Springen) ───
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const offset = 80;
                const top = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top, behavior: 'smooth' });
                document.getElementById('mainNav').classList.remove('open');
            }
        });
    });
</script>

</body>
</html>
