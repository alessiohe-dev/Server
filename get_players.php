<?php
header('Content-Type: application/json');
require_once 'db.php');

try {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT username FROM users ORDER BY username");
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode([]);
}
?>
