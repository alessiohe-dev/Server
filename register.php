<?php
header('Content-Type: application/json');
require_once 'db.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Benutzername und Passwort erforderlich']);
    exit;
}

if (strlen($username) < 3 || strlen($password) < 3) {
    echo json_encode(['success' => false, 'error' => 'Benutzername und Passwort müssen mindestens 3 Zeichen lang sein']);
    exit;
}

$pdo = getDBConnection();

// ─── Prüfen, ob Benutzer bereits existiert ───
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Benutzername bereits vergeben']);
    exit;
}

// ─── Passwort hashen und Benutzer erstellen ───
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (username, password, created_at) 
    VALUES (:username, :password, NOW())
");
$stmt->execute([
    'username' => $username,
    'password' => $hashedPassword
]);

echo json_encode(['success' => true, 'message' => 'Benutzer erfolgreich registriert']);
?>
