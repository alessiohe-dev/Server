<?php
require_once __DIR__ . '/../db.php';

// ─── Login-Prüfung (einfacher Passwortschutz) ───
$dashboardUser = 'admin';
$dashboardPass = '#58DS579!';

// ─── Session starten ───
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ─── Login verarbeiten ───
if ($_POST['login'] ?? false) {
    if ($_POST['username'] === $dashboardUser && $_POST['password'] === $dashboardPass) {
        $_SESSION['dashboard_logged_in'] = true;
        header('Location: index.php');
        exit;
    } else {
        $loginError = 'Falscher Benutzername oder Passwort!';
    }
}

// ─── Logout ───
if ($_GET['logout'] ?? false) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// ─── Ist der Nutzer eingeloggt? ───
if (!($_SESSION['dashboard_logged_in'] ?? false)) {
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b1329] text-white min-h-screen flex items-center justify-center">
    <div class="bg-[#1c2541] p-8 rounded-2xl border border-gray-800 max-w-md w-full shadow-2xl">
        <h1 class="text-2xl font-bold text-center mb-6">🔐 Dashboard Login</h1>
        <?php if (isset($loginError)): ?>
            <div class="bg-red-600/20 text-red-400 p-3 rounded-lg text-sm mb-4"><?php echo $loginError; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-400 mb-1">Benutzername</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-2 bg-[#0b1329] border border-gray-700 rounded-lg focus:border-blue-500 focus:outline-none">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-400 mb-1">Passwort</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-2 bg-[#0b1329] border border-gray-700 rounded-lg focus:border-blue-500 focus:outline-none">
            </div>
            <button type="submit" name="login" value="1" 
                    class="w-full bg-blue-600 hover:bg-blue-700 transition px-4 py-3 rounded-lg font-bold">
                Einloggen
            </button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DartSystem - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0b1329] text-white min-h-screen font-sans">

<div class="container mx-auto max-w-6xl p-6">
    <header class="flex items-center justify-between border-b border-gray-800 pb-6 mb-8">
        <div class="flex items-center space-x-3">
            <div class="bg-red-600 p-2.5 rounded-full flex items-center justify-center w-10 h-10 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                </svg>
            </div>
            <h1 class="text-2xl font-black tracking-wider text-white uppercase">Dart<span class="text-red-500">System</span></h1>
            <span class="text-xs bg-blue-600/20 text-blue-400 px-3 py-1 rounded-full">Dashboard</span>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 bg-[#1c2541] px-4 py-2 rounded-full border border-green-500/30 text-xs font-semibold text-green-400">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span>Online</span>
            </div>
            <a href="?logout=1" class="text-sm text-gray-400 hover:text-red-400 transition">Logout</a>
        </div>
    </header>

    <?php
    // ─── Benutzer löschen ───
    if ($_GET['delete'] ?? false) {
        $username = $_GET['delete'];
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            echo '<div class="bg-green-600/20 text-green-400 p-4 rounded-xl mb-4">✅ Benutzer "' . htmlspecialchars($username) . '" gelöscht.</div>';
        } catch (PDOException $e) {
            echo '<div class="bg-red-600/20 text-red-400 p-4 rounded-xl mb-4">❌ Fehler beim Löschen: ' . $e->getMessage() . '</div>';
        }
    }

    // ─── Benutzerliste laden ───
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query("SELECT id, username, password, level, experience, created_at FROM users ORDER BY id DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die('Datenbankfehler: ' . $e->getMessage());
    }
    ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="md:col-span-2">
            <h2 class="text-3xl font-bold mb-2">Spieler-Zentrale</h2>
            <p class="text-gray-400 text-sm">Dashboard zur Verwaltung aller registrierten Spieler.</p>
        </div>
        <div class="bg-[#1c2541] p-6 rounded-2xl border border-gray-800 flex items-center justify-between shadow-lg">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Registrierte Spieler</p>
                <p class="text-3xl font-black text-white"><?php echo count($users); ?></p>
            </div>
            <div class="bg-blue-600/20 text-blue-400 p-4 rounded-xl text-2xl">👥</div>
        </div>
    </div>

    <div class="bg-[#111a33] rounded-2xl border border-gray-800 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-gray-800 bg-[#16223f] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="text-lg font-bold">Spielerliste</h3>
            <button onclick="togglePasswords()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition duration-200 shadow-md">
                🔑 Passwörter anzeigen / verbergen
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-800 text-gray-400 text-xs uppercase tracking-wider bg-[#0e162c]">
                        <th class="py-4 px-6">ID</th>
                        <th class="py-4 px-6">Benutzername</th>
                        <th class="py-4 px-6">Passwort (Hash)</th>
                        <th class="py-4 px-6">Level</th>
                        <th class="py-4 px-6">Experience</th>
                        <th class="py-4 px-6">Erstellt</th>
                        <th class="py-4 px-6 text-right">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50 text-sm">
                    <?php if (empty($users)): ?>
                        <tr><td colspan="7" class="py-8 text-center text-gray-500 font-medium">Keine registrierten Spieler gefunden.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-[#16223f]/40 transition">
                                <td class="py-4 px-6 font-mono text-xs text-gray-500">#<?php echo $user['id']; ?></td>
                                <td class="py-4 px-6 font-semibold flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-xs font-bold uppercase text-white shadow-inner">
                                        <?php echo substr(htmlspecialchars($user['username']), 0, 2); ?>
                                    </div>
                                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                                </td>
                                <td class="py-4 px-6 font-mono text-xs text-gray-400 max-w-xs truncate">
                                    <span class="password-hash"><?php echo htmlspecialchars(substr($user['password'], 0, 20)) . '...'; ?></span>
                                    <span class="password-plain hidden text-yellow-400 font-bold"><?php echo htmlspecialchars($user['password']); ?></span>
                                </td>
                                <td class="py-4 px-6"><?php echo $user['level']; ?></td>
                                <td class="py-4 px-6"><?php echo $user['experience']; ?></td>
                                <td class="py-4 px-6 text-xs text-gray-500"><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                                <td class="py-4 px-6 text-right">
                                    <a href="?delete=<?php echo urlencode($user['username']); ?>" 
                                       onclick="return confirm('Spieler \'<?php echo htmlspecialchars($user['username']); ?>\' löschen?')"
                                       class="inline-flex items-center space-x-1.5 bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white px-3 py-1.5 rounded-lg border border-red-500/20 hover:border-transparent text-xs font-bold transition duration-150">
                                        🗑️ Löschen
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 text-xs text-gray-600 text-center border-t border-gray-800 pt-6">
        Dashboard läuft auf Render.com · Datenbank: TiDB Cloud
    </div>
</div>

<script>
function togglePasswords() {
    document.querySelectorAll('.password-hash').forEach(el => el.classList.toggle('hidden'));
    document.querySelectorAll('.password-plain').forEach(el => el.classList.toggle('hidden'));
}
</script>
</body>
</html>
