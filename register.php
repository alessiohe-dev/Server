<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

// ─── 🔥 UNTERSTÜTZT GET + POST ───
$username = $_POST['username'] ?? $_GET['username'] ?? '';
$password = $_POST['password'] ?? $_GET['password'] ?? '';

error_log("[register.php] ===== START ===== ");
error_log("[register.php] Username: $username, Password: $password");

if (empty($username) || empty($password)) {
    error_log("[register.php] ❌ Fehler: Felder leer");
    echo json_encode(['success' => false, 'error' => 'Benutzername und Passwort erforderlich']);
    exit;
}

if (strlen($username) < 3 || strlen($password) < 3) {
    error_log("[register.php] ❌ Fehler: Zu kurz");
    echo json_encode(['success' => false, 'error' => 'Benutzername und Passwort müssen mindestens 3 Zeichen lang sein']);
    exit;
}

try {
    $pdo = getDBConnection();
    error_log("[register.php] ✅ Verbindung erfolgreich");

    // ─── Prüfen, ob Benutzer existiert ───
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        error_log("[register.php] ❌ Benutzer existiert bereits: $username");
        echo json_encode(['success' => false, 'error' => 'Benutzername bereits vergeben']);
        exit;
    }

    // ─── Passwort hashen ───
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // ─── Benutzer erstellen ───
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password, created_at) 
        VALUES (:username, :password, NOW())
    ");
    $stmt->execute([
        'username' => $username,
        'password' => $hashedPassword
    ]);

    error_log("[register.php] ✅ Benutzer registriert: $username");
    echo json_encode(['success' => true, 'message' => 'Benutzer erfolgreich registriert']);

} catch (PDOException $e) {
    error_log("[register.php] ❌ PDO-Fehler: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
