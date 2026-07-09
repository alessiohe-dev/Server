<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once __DIR__ . '/../db.php';

// ─── UNTERSTÜTZT GET + POST ───
$username = $_POST['username'] ?? $_GET['username'] ?? '';
$password = $_POST['password'] ?? $_GET['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'error' => 'Benutzername und Passwort erforderlich']);
    exit;
}

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode([
            'success' => true,
            'data' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'level' => $user['level'],
                'experience' => $user['experience'],
                'created_at' => $user['created_at']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Benutzer nicht gefunden oder falsches Passwort']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Datenbankfehler: ' . $e->getMessage()]);
}
?>
