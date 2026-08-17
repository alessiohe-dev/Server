<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/bootstrap.php';

if (!is_admin()) redirect('/admin/');

$pdo = getDBConnection();
$pdo->exec("CREATE TABLE IF NOT EXISTS support_requests (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, name VARCHAR(160) NOT NULL, email VARCHAR(254) NOT NULL, topic VARCHAR(80) NOT NULL, message TEXT NOT NULL, status VARCHAR(32) NOT NULL DEFAULT 'open', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id), KEY support_status_created_index (status, created_at))");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if ($id) {
        if (isset($_POST['delete'])) {
            $pdo->prepare('DELETE FROM support_requests WHERE id = :id')->execute(['id' => $id]);
        } else {
            $status = (string)($_POST['status'] ?? 'open');
            if (in_array($status, ['open', 'in_progress', 'closed'], true)) {
                $pdo->prepare('UPDATE support_requests SET status = :status WHERE id = :id')->execute(['status' => $status, 'id' => $id]);
            }
        }
    }
    redirect('/admin/support.php');
}

$requests = $pdo->query('SELECT * FROM support_requests ORDER BY created_at DESC')->fetchAll();
$openCount = count(array_filter($requests, static fn(array $request): bool => ($request['status'] ?? 'open') !== 'closed'));
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Support – DartSystem Administration</title>
    <link rel="icon" href="/assets/images/logo-mark.svg?v=2" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/site.css?v=7">
</head>
<body>
<main class="admin-page">
    <aside class="admin-sidebar">
        <a class="brand" href="/"><img class="brand-mark" src="/assets/images/logo-mark.svg?v=2" alt="" width="38" height="38"><span>Dart<span>System</span></span></a>
        <small>ADMINISTRATION</small>
        <nav>
            <a href="/admin/?tab=players"><span>♙</span>Spieler</a>
            <a href="/admin/?tab=licenses"><span>◇</span>Lizenzen</a>
            <a class="active" href="/admin/support.php"><span>✉</span>Support<b><?= $openCount ?></b></a>
            <a href="/"><span>⌂</span>Website</a>
        </nav>
    </aside>
    <section class="admin-content">
        <header><div><p>DARTSYSTEM SUPPORT</p><h1>Support-<em>Anfragen.</em></h1></div><span class="admin-avatar">AD</span></header>
        <div class="admin-stats"><article><span>Anfragen gesamt</span><strong><?= count($requests) ?></strong><small>Gespeichert</small></article><article><span>Noch offen</span><strong><?= $openCount ?></strong><small>Zu bearbeiten</small></article></div>
        <section class="admin-table-card">
            <div class="admin-table-head"><div><p>POSTEINGANG</p><h2>Alle Support-Anfragen</h2></div></div>
            <div class="admin-table-scroll"><table><thead><tr><th>Kontakt</th><th>Thema</th><th>Nachricht</th><th>Eingang</th><th>Status</th><th>Aktionen</th></tr></thead><tbody>
            <?php foreach ($requests as $request): ?><tr>
                <td><b><?= e($request['name']) ?></b><small class="sub-cell"><a href="mailto:<?= e($request['email']) ?>"><?= e($request['email']) ?></a></small></td>
                <td><span class="level-badge"><?= e($request['topic']) ?></span></td>
                <td style="max-width:360px;white-space:normal"><?= nl2br(e($request['message'])) ?></td>
                <td><?= e(date('d.m.Y H:i', strtotime((string)$request['created_at']))) ?></td>
                <td><?= e($request['status'] === 'in_progress' ? 'In Bearbeitung' : ($request['status'] === 'closed' ? 'Geschlossen' : 'Offen')) ?></td>
                <td><div class="row-actions"><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$request['id'] ?>"><select name="status"><option value="open" <?= $request['status'] === 'open' ? 'selected' : '' ?>>Offen</option><option value="in_progress" <?= $request['status'] === 'in_progress' ? 'selected' : '' ?>>In Bearbeitung</option><option value="closed" <?= $request['status'] === 'closed' ? 'selected' : '' ?>>Geschlossen</option></select><button>Speichern</button></form><form method="post" onsubmit="return confirm('Anfrage wirklich löschen?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$request['id'] ?>"><button class="danger" name="delete" value="1">Löschen</button></form></div></td>
            </tr><?php endforeach; ?>
            <?php if (!$requests): ?><tr><td colspan="6">Noch keine Support-Anfragen vorhanden.</td></tr><?php endif; ?>
            </tbody></table></div>
        </section>
    </section>
</main>
</body>
</html>
