<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
$database = 'not_checked';
if (($_GET['database'] ?? '') === '1') {
    try {
        require_once dirname(__DIR__) . '/db.php';
        getDBConnection()->query('SELECT 1');
        $database = 'ok';
    } catch (Throwable) {
        $database = 'unavailable';
        http_response_code(503);
    }
}
echo json_encode(['status' => http_response_code() === 503 ? 'degraded' : 'ok', 'service' => 'dartsystem', 'database' => $database, 'time' => gmdate(DATE_ATOM)]);
