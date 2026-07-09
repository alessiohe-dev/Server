<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require_once __DIR__ . '/../db.php';

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT username FROM users ORDER BY username");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
