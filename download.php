<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';

header('Cache-Control: no-store');
header('Location: ' . mega_download_url(), true, 302);
exit;
