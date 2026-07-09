<?php
// ─── FEHLER ANZEIGEN ───
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ─── KONFIGURATION ───
$dashboardUser = 'admin';
$dashboardPass = '#58DS579!';

// ─── SESSION STARTEN ───
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── 🔥 DEBUG: ZEIGE ALLE POST-DATEN ───
error_log("[Dashboard] ===== START ===== ");
error_log("[Dashboard] POST-Daten: " . print_r($_POST, true));
error_log("[Dashboard] Session: " . print_r($_SESSION, true));

// ─── LOGIN VERARBEITEN ───
if (isset($_POST['login']) || isset($_POST['submit']) || isset($_POST['action'])) {
    error_log("[Dashboard] 🔍 Login-Button wurde gedrückt!");
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    error_log("[Dashboard] Login-Versuch: username='$username', password='$password'");
    
    if ($username === $dashboardUser && $password === $dashboardPass) {
        $_SESSION['dashboard_logged_in'] = true;
        error_log("[Dashboard] ✅ Login erfolgreich!");
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Falscher Benutzername oder Passwort!';
        error_log("[Dashboard] ❌ Login fehlgeschlagen!");
    }
} else {
    error_log("[Dashboard] ⚠️ Kein Login-Button erkannt. POST-Daten: " . print_r($_POST, true));
}

// ─── LOGOUT ───
if ($_GET['logout'] ?? false) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// ─── PRÜFEN OB EINGELOGGT ───
$isLoggedIn = isset($_SESSION['dashboard_logged_in']) && $_SESSION['dashboard_logged_in'] === true;
error_log("[Dashboard] Session-Status: " . ($isLoggedIn ? 'Eingeloggt' : 'Nicht eingeloggt'));

// ─── WENN NICHT EINGELOGGT: LOGIN ZEIGEN ───
if (!$isLoggedIn) {
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔐 Dashboard Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0a0e1a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #111a33;
            border: 1px solid #1e2d4a;
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-box .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 8px;
        }
        .login-box .logo .highlight { color: #ef4444; }
        .login-box .logo i { font-size: 28px; color: #3b82f6; }
        .login-box p.sub {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 28px;
        }
        .login-box .form-group { margin-bottom: 16px; }
        .login-box label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 4px;
        }
        .login-box input {
            width: 100%;
            padding: 12px 14px;
            background: #0b1329;
            border: 1px solid #1e2d4a;
            border-radius: 10px;
            color: #e2e8f0;
            font-size: 15px;
            transition: border-color 0.2s;
        }
        .login-box input:focus { outline: none; border-color: #3b82f6; }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
        }
        .btn-login:hover { background: #2563eb; }
        .login-box .error {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .login-box .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #60a5fa;
            text-decoration: none;
            font-size: 14px;
        }
        .login-box .back-link:hover { text-decoration: underline; }
        .debug-info {
            margin-top: 20px;
            padding: 12px;
            background: #0b1329;
            border-radius: 8px;
            font-size: 12px;
            color: #475569;
            font-family: monospace;
            word-break: break-all;
        }
        .debug-info .ok { color: #6ee7b7; }
        .debug-info .error { color: #f87171; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo">
            <i class="fas fa-bullseye"></i>
            <span>Dart<span class="highlight">System</span></span>
        </div>
        <p class="sub">Admin-Dashboard Login</p>
        
        <?php if (isset($loginError)): ?>
            <div class="error">❌ <?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="username">👤 Benutzername</label>
                <input type="text" id="username" name="username" value="admin" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">🔑 Passwort</label>
                <input type="password" id="password" name="password" value="#58DS579!" required>
            </div>
            <button type="submit" name="login" class="btn-login">🔓 Einloggen</button>
        </form>
        <a href="/" class="back-link">← Zurück zur Hauptseite</a>
        
        <!-- ─── 🔥 DEBUG-INFO ─── -->
        <div class="debug-info">
            <p><span class="ok">✅</span> Session-ID: <?php echo session_id(); ?></p>
            <p><span class="ok">✅</span> Session-Status: <?php echo session_status() === 2 ? 'Aktiv' : 'Inaktiv'; ?></p>
            <p><span class="ok">✅</span> PHP Version: <?php echo phpversion(); ?></p>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// ─── WENN EINGELOGGT: DASHBOARD ANZEIGEN ───
require_once __DIR__ . '/../../db.php';

$dbError = '';
$users = [];
try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, username, level, experience, created_at FROM users ORDER BY id DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $dbError = 'Datenbankfehler: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎯 DartSystem – Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #0a0e1a;
            color: #e2e8f0;
            min-height: 100vh;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 24px; }
        header {
            background: rgba(10,14,26,0.95);
            border-bottom: 1px solid #1a2332;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }
        .header-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: 800;
        }
        .logo .highlight { color: #ef4444; }
        .logo i { font-size: 26px; color: #3b82f6; }
        .badge-admin {
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 8px;
        }
        nav { display: flex; gap: 6px; }
        nav a {
            color: #94a3b8;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        nav a:hover { background: #1e2d4a; color: #f1f5f9; }
        nav a.logout { color: #ef4444; }
        nav a.logout:hover { background: rgba(239,68,68,0.1); }
        main { padding: 40px 0; }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #1a2332;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .page-header h1 { font-size: 28px; font-weight: 800; }
        .page-header p { color: #94a3b8; font-size: 16px; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #111a33;
            border: 1px solid #1e2d4a;
            border-radius: 12px;
            padding: 20px 24px;
        }
        .stat-card .label { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card .value { font-size: 28px; font-weight: 800; margin-top: 4px; }
        .table-wrapper {
            background: #111a33;
            border: 1px solid #1e2d4a;
            border-radius: 16px;
            overflow: hidden;
        }
        .table-header {
            padding: 16px 24px;
            border-bottom: 1px solid #1e2d4a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .table-header h3 { font-size: 18px; font-weight: 700; }
        .table-header span { color: #94a3b8; font-size: 14px; }
        .table-scroll { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th {
            text-align: left;
            padding: 12px 16px;
            color: #94a3b8;
            font-weight: 600;
            border-bottom: 1px solid #1e2d4a;
            background: #0e162c;
        }
        td {
            padding: 12px 16px;
            border-bottom: 1px solid #0b1329;
        }
        tr:hover td { background: #16223f; }
        .empty-state { text-align: center; padding: 60px 0; color: #475569; }
        .empty-state i { font-size: 48px; display: block; margin-bottom: 12px; }
        .message {
            padding: 14px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .message.success { background: #064e3b; color: #6ee7b7; border: 1px solid #065f46; }
        .message.error { background: #7f1d1d; color: #fca5a5; border: 1px solid #991b1b; }
        footer {
            background: #060a14;
            border-top: 1px solid #111a33;
            padding: 24px 0;
            margin-top: 40px;
        }
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #475569;
            flex-wrap: wrap;
            gap: 8px;
        }
        .footer-content a { color: #60a5fa; text-decoration: none; }
        .footer-content a:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: flex-start; }
            .footer-content { flex-direction: column; align-items: center; text-align: center; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-inner">
            <div class="logo">
                <i class="fas fa-bullseye"></i>
                <span>Dart<span class="highlight">System</span></span>
                <span class="badge-admin">Admin</span>
            </div>
            <nav>
                <a href="/">🏠 Home</a>
                <a href="?logout=1" class="logout">🚪 Logout</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="page-header">
                <div>
                    <h1>🛠️ Dashboard</h1>
                    <p>Übersicht aller registrierten Spieler</p>
                </div>
            </div>

            <?php if ($dbError): ?>
                <div class="message error">❌ <?php echo htmlspecialchars($dbError); ?></div>
            <?php endif; ?>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="label">Registrierte Spieler</div>
                    <div class="value"><?php echo count($users); ?></div>
                </div>
                <div class="stat-card">
                    <div class="label">Datenbank</div>
                    <div class="value" style="font-size:16px; font-weight:600; color:#60a5fa;">✅ Verbunden</div>
                </div>
                <div class="stat-card">
                    <div class="label">Server</div>
                    <div class="value" style="font-size:16px; font-weight:600; color:#60a5fa;">✅ Online</div>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-header">
                    <h3>👥 Spielerliste</h3>
                    <span><?php echo count($users); ?> Einträge</span>
                </div>
                <div class="table-scroll">
                    <?php if (empty($users)): ?>
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Noch keine registrierten Spieler</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Benutzername</th>
                                    <th>Level</th>
                                    <th>Experience</th>
                                    <th>Registriert</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td>#<?php echo $user['id']; ?></td>
                                        <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                        <td><?php echo $user['level']; ?></td>
                                        <td><?php echo $user['experience']; ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container footer-content">
            <span>&copy; 2025 DartSystem – Admin-Dashboard</span>
            <span><a href="/">← Zurück zur Hauptseite</a></span>
        </div>
    </footer>
</body>
</html>
