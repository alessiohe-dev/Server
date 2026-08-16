<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}
require_csrf();
unset($_SESSION['user']);
session_regenerate_id(true);
flash('success', 'Du wurdest sicher abgemeldet.');
redirect('/');
