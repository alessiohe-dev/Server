<?php
declare(strict_types=1);
$pageTitle = 'Hilfe & Kontakt – DartSystem';
$pageDescription = 'Antworten zu Installation, Konto, Lizenzen und DartSystem-Support.';
$activePage = 'hilfe';
require __DIR__ . '/includes/header.php';
$submitted = false;
$formError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $topic = trim((string)($_POST['topic'] ?? ''));
    $message = trim((string)($_POST['message'] ?? ''));
    $allowedTopics = ['Installation', 'Konto & Fortschritt', 'Lizenz', 'Fehler melden'];
    if ($name === '' || mb_strlen($name) > 160 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($topic, $allowedTopics, true) || $message === '' || mb_strlen($message) > 5000) {
        $formError = 'Bitte prüfe deine Angaben und versuche es erneut.';
    } else {
        try {
            $pdo = getDBConnection();
            $pdo->exec("CREATE TABLE IF NOT EXISTS support_requests (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, name VARCHAR(160) NOT NULL, email VARCHAR(254) NOT NULL, topic VARCHAR(80) NOT NULL, message TEXT NOT NULL, status VARCHAR(32) NOT NULL DEFAULT 'open', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id), KEY support_status_created_index (status, created_at))");
            $stmt = $pdo->prepare('INSERT INTO support_requests (name, email, topic, message) VALUES (:name, :email, :topic, :message)');
            $stmt->execute(['name' => $name, 'email' => $email, 'topic' => $topic, 'message' => $message]);
            $submitted = true;
        } catch (Throwable $exception) {
            error_log('[support] ' . $exception->getMessage());
            $formError = 'Die Anfrage konnte gerade nicht gespeichert werden. Bitte versuche es später erneut.';
        }
    }
}
$faqs = [
    ['installation','Wie installiere ich DartSystem?','Öffne den Download, lade die aktuelle Version herunter, entpacke sie vollständig und starte DartSystem.'],
    ['konto','Funktioniert dasselbe Konto überall?','Ja. Mit deinem DartSystem-Konto kannst du dich anmelden und deinen Fortschritt im persönlichen Profil ansehen.'],
    ['fortschritt','Wie wird mein Fortschritt gespeichert?','Abgeschlossene Level, Würfe und Treffer werden deinem Konto zugeordnet und anschließend in deinem Profil dargestellt.'],
    ['lizenz','Wie aktiviere ich eine Lizenz?','Gib den erhaltenen Lizenzschlüssel im Programm ein. Bei der ersten Aktivierung wird die Lizenz entsprechend ihrer Konfiguration deinem Gerät zugeordnet.'],
    ['download','Wo finde ich neue Versionen?','Die aktuelle Version erreichst du immer über den offiziellen Downloadlink auf dieser Website.'],
];
?>
<main><section class="page-hero visual-hero help-hero"><div class="page-hero-copy"><p class="eyebrow"><span></span>Wir helfen weiter</p><h1>Antworten ohne <em>Umwege.</em></h1><p>Finde schnelle Hilfe zu Installation, Konto, Fortschritt und Lizenzierung.</p></div></section><section class="help-layout"><div><p class="kicker">Häufige Fragen</p><h2>Was möchtest du wissen?</h2><div class="accordion"><?php foreach ($faqs as $index => [$id,$question,$answer]): ?><article><button type="button" data-accordion aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>" aria-controls="faq-<?= e($id) ?>"><span><?= e($question) ?></span><b>+</b></button><p id="faq-<?= e($id) ?>" <?= $index === 0 ? '' : 'hidden' ?>><?= e($answer) ?></p></article><?php endforeach; ?></div></div><form class="contact-card" id="kontakt" method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><p class="kicker">Direkter Kontakt</p><h3>Support-Anfrage</h3><?php if ($submitted): ?><div class="notice success">Deine Anfrage wurde gespeichert. Wir melden uns so schnell wie möglich bei dir.</div><?php endif; ?><?php if ($formError): ?><div class="notice error"><?= e($formError) ?></div><?php endif; ?><label>Name<input required maxlength="160" name="name" autocomplete="name" value="<?= e($submitted ? '' : ($_POST['name'] ?? '')) ?>"></label><label>E-Mail<input required maxlength="254" type="email" name="email" autocomplete="email" value="<?= e($submitted ? '' : ($_POST['email'] ?? '')) ?>"></label><label>Thema<select name="topic"><option>Installation</option><option>Konto & Fortschritt</option><option>Lizenz</option><option>Fehler melden</option></select></label><label>Nachricht<textarea required maxlength="5000" name="message" rows="5"><?= e($submitted ? '' : ($_POST['message'] ?? '')) ?></textarea></label><button class="button">Anfrage absenden <span>→</span></button></form></section></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
