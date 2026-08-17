# DartSystem Website & API

Professionelle Website und PHP-API für das in Unity entwickelte DartSystem-Programm.

## Architektur

- **Unity:** DartSystem-Programm für Training, Level und Highscores
- **Render:** PHP 8.2, Apache und öffentliche Website/API
- **TiDB Cloud:** Spieler, Fortschritt, Highscores und Lizenzen
- **MEGA:** Download der aktuellen Programm-Builds
- **GitHub:** Quellcode und automatische Render-Deployments

## Öffentliche Bereiche

- `/` – Startseite und Download
- `/produkt.php` – Funktionen und technische Plattform
- `/training.php` – Trainings- und Levelkonzept
- `/rangliste.php` – Live-Highscores aus TiDB
- `/preise.php` – Lizenzmodelle
- `/hilfe.php` – FAQ und Support
- `/login.php`, `/register.php`, `/account.php` – verbundenes Spielerkonto
- `/admin/` – Spieler-, Lizenz- und Supportverwaltung

Der alte Dashboard-Pfad `/website/dashboard/` leitet dauerhaft auf `/admin/` um.

## Unity-kompatible API

- `POST /api/login.php`
- `POST /api/register.php`
- `GET /api/get_players.php`
- `GET /api/get_profile.php?username=...`
- `GET /api/get_highscores.php?levelId=...`
- `POST /api/save_progress.php`
- `POST /api/save_highscore.php`
- `POST /api/verify_license.php`
- `POST /api/generate_license.php` – nur für angemeldete Administratoren
- `GET /api/health.php`

Die bisherigen Unity-Feldnamen (`levelId`, `dartsThrown`, `successfulHits`, `device_id`, `license_key`) bleiben unterstützt. Login und Registrierung akzeptieren ausschließlich POST-Anfragen. Fortschritt und Highscores können nur für das angemeldete Spielerkonto gespeichert werden.

Jede Lizenz wird bereits beim Erstellen im Adminbereich fest einer Device ID zugeordnet. Unity verlangt anschließend genau diese manuell eingegebene Device ID zusammen mit dem vom Support ausgegebenen Lizenzschlüssel; eine nachträgliche Bindung oder Mehrgeräteaktivierung findet nicht statt.

## Render-Konfiguration

`render.yaml` und `Dockerfile` konfigurieren Port, Apache, Health Check und die nicht geheimen Werte. Folgende Secrets müssen im Render-Dashboard gesetzt werden:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`
- `ADMIN_PASSWORD` oder `ADMIN_PASSWORD_HASH`

Die übrigen Werte stehen in `.env.example`. Geheimnisse niemals in GitHub committen.

## TiDB

Das erwartete Schema befindet sich in `database/schema.sql`. Bestehende Tabellen vor einer Migration sichern und Spalten mit dem Schema vergleichen.

## Domain

Die Anwendung ist für `https://dartsystem.alessiohennecke.de` vorbereitet. Nach dem ersten erfolgreichen Render-Deployment muss die Custom Domain im Render-Dashboard hinzugefügt und der von Render angezeigte DNS-Eintrag beim Domainanbieter gesetzt werden.

## Lokaler Test

```bash
php -S 127.0.0.1:8080
```

Für Seiten mit Datenbankzugriff müssen die TiDB-Variablen in der Umgebung gesetzt sein.
