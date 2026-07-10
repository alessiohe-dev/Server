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

// ─── 🔥 LOGIN VERARBEITEN (mit Debug) ───
$loginError = '';

// ─── Prüfe sowohl POST als auch GET (für Test) ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("[Dashboard] ===== POST-Request empfangen =====");
    error_log("[Dashboard] POST-Daten: " . print_r($_POST, true));
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    error_log("[Dashboard] Login-Versuch: username='$username', password='$password'");
    error_log("[Dashboard] Erwartet: admin / #58DS579!");
    
    if ($username === $dashboardUser && $password === $dashboardPass) {
        $_SESSION['dashboard_logged_in'] = true;
        error_log("[Dashboard] ✅ Login erfolgreich! Session gesetzt.");
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Falscher Benutzername oder Passwort!';
        error_log("[Dashboard] ❌ Login fehlgeschlagen!");
    }
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
        body { font-family: 'Inter', system-ui, sans-serif; background: #0a0e1a; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: #111a33; border: 1px solid #1e2d4a; border-radius: 20px; padding: 48px 40px; width: 100%; max-width: 400px; }
        .login-box .logo { display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 28px; font-weight: 800; margin-bottom: 8px; }
        .login-box .logo .highlight { color: #ef4444; }
        .login-box .logo i { font-size: 28px; color: #3b82f6; }
        .login-box p.sub { text-align: center; color: #94a3b8; font-size: 14px; margin-bottom: 28px; }
        .login-box .form-group { margin-bottom: 16px; }
        .login-box label { display: block; font-size: 14px; font-weight: 600; color: #94a3b8; margin-bottom: 4px; }
        .login-box input { width: 100%; padding: 12px 14px; background: #0b1329; border: 1px solid #1e2d4a; border-radius: 10px; color: #e2e8f0; font-size: 15px; transition: border-color 0.2s; }
        .login-box input:focus { outline: none; border-color: #3b82f6; }
        .btn-login { width: 100%; padding: 14px; background: #3b82f6; color: #fff; border: none; border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s; margin-top: 8px; }
        .btn-login:hover { background: #2563eb; }
        .login-box .error { background: rgba(239,68,68,0.15); color: #f87171; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; }
        .login-box .back-link { display: block; text-align: center; margin-top: 16px; color: #60a5fa; text-decoration: none; font-size: 14px; }
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
        
        <?php if ($loginError): ?>
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
            <button type="submit" class="btn-login">🔓 Einloggen</button>
        </form>
        <a href="/" class="back-link">← Zurück zur Hauptseite</a>
        
        <!-- ─── 🔥 DEBUG-INFO ─── -->
        <div class="debug-info">
            <p><span class="ok">✅</span> Session-ID: <?php echo session_id(); ?></p>
            <p><span class="ok">✅</span> Session-Status: <?php echo session_status() === 2 ? 'Aktiv' : 'Inaktiv'; ?></p>
            <p><span class="ok">✅</span> PHP Version: <?php echo phpversion(); ?></p>
            <p><span class="ok">✅</span> POST empfangen: <?php echo $_SERVER['REQUEST_METHOD'] === 'POST' ? 'Ja' : 'Nein'; ?></p>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// ─── ✅ EINGELOGGT → DASHBOARD ───
require_once __DIR__ . '/../../db.php';

// ─── Aktionen verarbeiten ───
$message = '';
$messageType = '';

// ─── Spieler löschen ───
if ($_GET['delete_user'] ?? false) {
    $username = $_GET['delete_user'];
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $message = "✅ Spieler '$username' wurde gelöscht!";
        $messageType = 'success';
    } catch (Exception $e) {
        $message = "❌ Fehler beim Löschen: " . $e->getMessage();
        $messageType = 'error';
    }
}

// ─── Lizenz löschen ───
if ($_GET['delete_license'] ?? false) {
    $id = $_GET['delete_license'];
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("DELETE FROM licenses WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $message = "✅ Lizenz wurde gelöscht!";
        $messageType = 'success';
    } catch (Exception $e) {
        $message = "❌ Fehler beim Löschen: " . $e->getMessage();
        $messageType = 'error';
    }
}

// ─── Daten laden ───
$pdo = getDBConnection();

// Spieler
$stmt = $pdo->query("SELECT id, username, level, experience, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lizenzen
$stmt = $pdo->query("SELECT * FROM licenses ORDER BY id DESC");
$licenses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Statistik
$totalUsers = count($users);
$totalLicenses = count($licenses);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎯 DartSystem – Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; background: #0a0e1a; color: #e2e8f0; min-height: 100vh; }
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
        nav { display: flex; gap: 6px; align-items: center; }
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
        nav a.active { background: #1e3a5f; color: #60a5fa; }
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
        
        .tabs { display: flex; gap: 8px; margin-bottom: 30px; border-bottom: 1px solid #1a2332; }
        .tabs a {
            padding: 12px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        .tabs a:hover { color: #f1f5f9; }
        .tabs a.active { color: #60a5fa; border-bottom-color: #3b82f6; }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #111a33;
            border: 1px solid #1e2d4a;
            border-radius: 12px;
            padding: 16px 20px;
        }
        .stat-card .label { font-size: 12px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card .value { font-size: 24px; font-weight: 800; margin-top: 4px; }
        
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
        td { padding: 12px 16px; border-bottom: 1px solid #0b1329; vertical-align: middle; }
        tr:hover td { background: #16223f; }
        
        .empty-state { text-align: center; padding: 40px 0; color: #475569; }
        .empty-state i { font-size: 48px; display: block; margin-bottom: 12px; }
        
        .btn-sm {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        .btn-sm i { font-size: 14px; }
        .btn-danger { background: rgba(239,68,68,0.15); color: #f87171; }
        .btn-danger:hover { background: #ef4444; color: #fff; }
        .btn-primary { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .btn-primary:hover { background: #3b82f6; color: #fff; }
        .btn-success { background: rgba(34,197,94,0.15); color: #6ee7b7; }
        .btn-success:hover { background: #22c55e; color: #fff; }
        .btn-warning { background: rgba(234,179,8,0.15); color: #fcd34d; }
        .btn-warning:hover { background: #eab308; color: #fff; }
        
        .actions { display: flex; gap: 4px; flex-wrap: wrap; }
        .badge-license {
            font-family: monospace;
            background: #1e2d4a;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            color: #60a5fa;
        }
        .badge-status {
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-status.active { background: rgba(34,197,94,0.2); color: #6ee7b7; }
        .badge-status.inactive { background: rgba(239,68,68,0.2); color: #f87171; }
        .badge-status.expired { background: rgba(234,179,8,0.2); color: #fcd34d; }
        
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
        
        /* ─── Modal / Popup ─── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: #111a33;
            border: 1px solid #1e2d4a;
            border-radius: 20px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }
        .modal h2 { font-size: 24px; font-weight: 700; margin-bottom: 16px; }
        .modal .form-group { margin-bottom: 16px; }
        .modal label { display: block; font-size: 14px; font-weight: 600; color: #94a3b8; margin-bottom: 4px; }
        .modal input, .modal select {
            width: 100%;
            padding: 10px 14px;
            background: #0b1329;
            border: 1px solid #1e2d4a;
            border-radius: 8px;
            color: #e2e8f0;
            font-size: 14px;
        }
        .modal input:focus, .modal select:focus { outline: none; border-color: #3b82f6; }
        .modal .btn {
            padding: 10px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .modal .btn-close { background: #1e2d4a; color: #94a3b8; }
        .modal .btn-close:hover { background: #2a3a5c; }
        .modal .btn-primary { background: #3b82f6; color: #fff; }
        .modal .btn-primary:hover { background: #2563eb; }
        .modal .btn-success { background: #22c55e; color: #fff; }
        .modal .btn-success:hover { background: #16a34a; }
        .modal .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .modal .license-result {
            background: #0b1329;
            padding: 12px 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 18px;
            color: #6ee7b7;
            text-align: center;
            margin: 12px 0;
            word-break: break-all;
        }
        
        .fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #3b82f6;
            color: #fff;
            border: none;
            font-size: 28px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(59,130,246,0.4);
            transition: all 0.3s;
            z-index: 100;
        }
        .fab:hover { transform: scale(1.1); background: #2563eb; }
        
        @media (max-width: 768px) {
            .page-header { flex-direction: column; align-items: flex-start; }
            .footer-content { flex-direction: column; align-items: center; text-align: center; }
            .tabs { flex-wrap: wrap; }
            .header-inner { flex-wrap: wrap; height: auto; padding: 10px 0; gap: 10px; }
            nav { flex-wrap: wrap; justify-content: center; }
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
            <a href="?tab=users" class="<?php echo ($_GET['tab'] ?? 'users') === 'users' ? 'active' : ''; ?>">👥 Spieler</a>
            <a href="?tab=licenses" class="<?php echo ($_GET['tab'] ?? 'users') === 'licenses' ? 'active' : ''; ?>">🔑 Lizenzen</a>
            <a href="?logout=1" class="logout">🚪 Logout</a>
        </nav>
    </div>
</header>

<main>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>🛠️ Admin Dashboard</h1>
                <p>Verwalte Spieler und Lizenzen</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="label">Registrierte Spieler</div>
                <div class="value"><?php echo $totalUsers; ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Aktive Lizenzen</div>
                <div class="value"><?php echo $totalLicenses; ?></div>
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

        <?php $currentTab = $_GET['tab'] ?? 'users'; ?>

        <!-- ─── SPIELER-TAB ─── -->
        <div id="tab-users" style="display: <?php echo $currentTab === 'users' ? 'block' : 'none'; ?>;">
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>👥 Spielerliste</h3>
                    <span><?php echo count($users); ?> Einträge</span>
                </div>
                <div class="table-scroll">
                    <?php if (empty($users)): ?>
                        <div class="empty-state"><i class="fas fa-users"></i><p>Noch keine registrierten Spieler</p></div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Benutzername</th>
                                    <th>Level</th>
                                    <th>Experience</th>
                                    <th>Registriert</th>
                                    <th>Aktionen</th>
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
                                        <td>
                                            <div class="actions">
                                                <a href="?tab=users&profile=<?php echo urlencode($user['username']); ?>" class="btn-sm btn-primary"><i class="fas fa-user"></i> Profil</a>
                                                <a href="?tab=users&delete_user=<?php echo urlencode($user['username']); ?>" class="btn-sm btn-danger" onclick="return confirm('Spieler \'<?php echo htmlspecialchars($user['username']); ?>\' wirklich löschen?')"><i class="fas fa-trash"></i> Löschen</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ─── LIZENZ-TAB ─── -->
        <div id="tab-licenses" style="display: <?php echo $currentTab === 'licenses' ? 'block' : 'none'; ?>;">
            <div class="table-wrapper">
                <div class="table-header">
                    <h3>🔑 Lizenzverwaltung</h3>
                    <span><?php echo count($licenses); ?> Lizenzen</span>
                </div>
                <div class="table-scroll">
                    <?php if (empty($licenses)): ?>
                        <div class="empty-state"><i class="fas fa-key"></i><p>Noch keine Lizenzen erstellt</p></div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lizenzschlüssel</th>
                                    <th>Kunde</th>
                                    <th>Typ</th>
                                    <th>Status</th>
                                    <th>Ablauf</th>
                                    <th>Aktivierungen</th>
                                    <th>Aktionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($licenses as $license): ?>
                                    <?php
                                    $status = 'active';
                                    if (!$license['is_active']) $status = 'inactive';
                                    elseif ($license['expires_at'] && strtotime($license['expires_at']) < time()) $status = 'expired';
                                    ?>
                                    <tr>
                                        <td>#<?php echo $license['id']; ?></td>
                                        <td><span class="badge-license"><?php echo htmlspecialchars(substr($license['license_key'], 0, 20)) . '...'; ?></span></td>
                                        <td><?php echo htmlspecialchars($license['customer_name'] ?? '—'); ?></td>
                                        <td><span class="badge badge-status active"><?php echo $license['license_type']; ?></span></td>
                                        <td><span class="badge-status <?php echo $status; ?>"><?php echo $status; ?></span></td>
                                        <td><?php echo $license['expires_at'] ? date('d.m.Y', strtotime($license['expires_at'])) : '∞'; ?></td>
                                        <td><?php echo $license['activations']; ?>/<?php echo $license['max_activations']; ?></td>
                                        <td>
                                            <div class="actions">
                                                <a href="?tab=licenses&delete_license=<?php echo $license['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Lizenz wirklich löschen?')"><i class="fas fa-trash"></i> Löschen</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ─── FAB: NEUE LIZENZ ─── -->
<button class="fab" id="fabLicense" title="Neue Lizenz generieren"><i class="fas fa-plus"></i></button>

<!-- ─── MODAL: LIZENZ GENERIEREN ─── -->
<div class="modal-overlay" id="licenseModal">
    <div class="modal">
        <h2>🔑 Lizenz generieren</h2>
        <form id="licenseForm">
            <div class="form-group">
                <label for="customer_name">👤 Kundenname</label>
                <input type="text" id="customer_name" placeholder="z.B. Max Mustermann" required>
            </div>
            <div class="form-group">
                <label for="customer_email">📧 E-Mail (optional)</label>
                <input type="email" id="customer_email" placeholder="max@beispiel.de">
            </div>
            <div class="form-group">
                <label for="license_type">📋 Lizenztyp</label>
                <select id="license_type">
                    <option value="full">Full</option>
                    <option value="trial">Trial</option>
                    <option value="subscription">Subscription</option>
                </select>
            </div>
            <div class="form-group">
                <label for="max_activations">🖥️ Maximale Aktivierungen</label>
                <input type="number" id="max_activations" value="1" min="1">
            </div>
            <div class="form-group">
                <label for="expires_in">⏳ Gültigkeitsdauer</label>
                <select id="expires_in">
                    <option value="30">30 Tage</option>
                    <option value="90">90 Tage</option>
                    <option value="180">180 Tage</option>
                    <option value="365" selected>1 Jahr</option>
                    <option value="0">Unbegrenzt</option>
                </select>
            </div>
            
            <div id="licenseResult" style="display:none;">
                <div class="license-result" id="generatedLicense">XXXX-XXXX-XXXX-XXXX</div>
                <button type="button" class="btn btn-success" id="copyLicenseBtn"><i class="fas fa-copy"></i> Kopieren</button>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-close" id="modalClose">Schließen</button>
                <button type="submit" class="btn btn-primary" id="generateBtn"><i class="fas fa-key"></i> Generieren</button>
            </div>
        </form>
    </div>
</div>

<!-- ─── MODAL: PROFIL ─── -->
<div class="modal-overlay" id="profileModal">
    <div class="modal">
        <h2>👤 Spieler-Profil</h2>
        <div id="profileContent">
            <p style="color:#94a3b8;">Lade Daten...</p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-close" id="profileClose">Schließen</button>
        </div>
    </div>
</div>

<!-- ─── TOAST / BENACHRICHTIGUNG ─── -->
<div id="toast" style="position:fixed; bottom:100px; right:30px; background:#111a33; border:1px solid #1e2d4a; border-radius:12px; padding:16px 24px; color:#e2e8f0; display:none; z-index:2000; max-width:400px; box-shadow:0 8px 30px rgba(0,0,0,0.6);">
    <span id="toastMessage"></span>
</div>

<footer>
    <div class="container footer-content">
        <span>&copy; 2025 DartSystem – Admin-Dashboard</span>
        <span><a href="/">← Zurück zur Hauptseite</a></span>
    </div>
</footer>

<script>
// ─── TOAST ───
function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMsg = document.getElementById('toastMessage');
    toastMsg.textContent = msg;
    toast.style.borderColor = type === 'success' ? '#22c55e' : '#ef4444';
    toast.style.display = 'block';
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 4000);
}

// ─── MODAL ───
const modal = document.getElementById('licenseModal');
const fab = document.getElementById('fabLicense');
const closeBtn = document.getElementById('modalClose');
const licenseResult = document.getElementById('licenseResult');
const generatedLicense = document.getElementById('generatedLicense');
const copyBtn = document.getElementById('copyLicenseBtn');

fab.addEventListener('click', () => {
    modal.classList.add('open');
    licenseResult.style.display = 'none';
    document.getElementById('customer_name').focus();
});

closeBtn.addEventListener('click', () => modal.classList.remove('open'));
modal.addEventListener('click', (e) => { if (e.target === modal) modal.classList.remove('open'); });

// ─── LIZENZ GENERIEREN ───
document.getElementById('licenseForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const customerName = document.getElementById('customer_name').value.trim();
    const customerEmail = document.getElementById('customer_email').value.trim();
    const licenseType = document.getElementById('license_type').value;
    const maxActivations = document.getElementById('max_activations').value;
    const expiresIn = parseInt(document.getElementById('expires_in').value);
    
    if (!customerName) {
        showToast('❌ Bitte Kundenname eingeben!', 'error');
        return;
    }
    
    const generateBtn = document.getElementById('generateBtn');
    generateBtn.disabled = true;
    generateBtn.textContent = '⏳ Wird generiert...';
    
    try {
        const response = await fetch('/api/generate_license.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_name: customerName,
                customer_email: customerEmail,
                license_type: licenseType,
                max_activations: parseInt(maxActivations),
                expires_in: expiresIn
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            generatedLicense.textContent = data.license_key;
            licenseResult.style.display = 'block';
            showToast('✅ Lizenz erfolgreich generiert!');
            
            // ─── Tabelle neu laden ───
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('❌ ' + (data.error || 'Fehler beim Generieren'), 'error');
        }
    } catch (error) {
        showToast('❌ Fehler: ' + error.message, 'error');
    } finally {
        generateBtn.disabled = false;
        generateBtn.textContent = '🔑 Generieren';
    }
});

// ─── KOPIEREN ───
copyBtn.addEventListener('click', () => {
    const text = generatedLicense.textContent;
    navigator.clipboard.writeText(text).then(() => {
        showToast('✅ Lizenz in die Zwischenablage kopiert!');
    }).catch(() => {
        // Fallback
        const range = document.createRange();
        range.selectNode(generatedLicense);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        showToast('✅ Lizenz kopiert!');
    });
});

// ─── PROFIL MODAL ───
const profileModal = document.getElementById('profileModal');
const profileContent = document.getElementById('profileContent');
const profileClose = document.getElementById('profileClose');

document.querySelectorAll('a[href*="profile="]').forEach(link => {
    link.addEventListener('click', async (e) => {
        e.preventDefault();
        const url = new URL(link.href);
        const username = url.searchParams.get('profile');
        
        profileModal.classList.add('open');
        profileContent.innerHTML = '<p style="color:#94a3b8;">⏳ Lade Daten...</p>';
        
        try {
            const response = await fetch(`/api/get_profile.php?username=${encodeURIComponent(username)}`);
            const data = await response.json();
            
            if (data.success) {
                const u = data.data;
                profileContent.innerHTML = `
                    <div style="background:#0b1329; padding:16px; border-radius:12px; margin-bottom:16px;">
                        <p><strong>👤 Benutzername:</strong> ${u.username}</p>
                        <p><strong>🆔 ID:</strong> #${u.id}</p>
                        <p><strong>📊 Level:</strong> ${u.level}</p>
                        <p><strong>⭐ Experience:</strong> ${u.experience}</p>
                        <p><strong>📅 Registriert:</strong> ${new Date(u.created_at).toLocaleString('de-DE')}</p>
                        ${u.last_login ? `<p><strong>🕐 Letzter Login:</strong> ${new Date(u.last_login).toLocaleString('de-DE')}</p>` : ''}
                    </div>
                    <div style="background:#0b1329; padding:16px; border-radius:12px;">
                        <h4>📈 Fortschritt</h4>
                        ${u.progress && u.progress.length > 0 ? 
                            u.progress.map(p => `
                                <div style="border-bottom:1px solid #1a2332; padding:6px 0; font-size:14px;">
                                    <strong>${p.level_id}</strong> - ${p.accuracy || 0}% (${p.darts_thrown || 0} Darts)
                                </div>
                            `).join('') :
                            '<p style="color:#475569;">Kein Fortschritt vorhanden</p>'
                        }
                    </div>
                `;
            } else {
                profileContent.innerHTML = `<p style="color:#f87171;">❌ ${data.error || 'Benutzer nicht gefunden'}</p>`;
            }
        } catch (error) {
            profileContent.innerHTML = `<p style="color:#f87171;">❌ Fehler beim Laden: ${error.message}</p>`;
        }
    });
});

profileClose.addEventListener('click', () => profileModal.classList.remove('open'));
profileModal.addEventListener('click', (e) => { if (e.target === profileModal) profileModal.classList.remove('open'); });

// ─── KEYBOARD SHORTCUTS ───
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        modal.classList.remove('open');
        profileModal.classList.remove('open');
    }
});
</script>

</body>
</html>
