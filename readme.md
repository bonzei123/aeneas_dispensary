# Aeneas Dispensary – Nextcloud App Developer Guide

Dieses Dokument dient als Gedächtnisstütze für die Weiterentwicklung dieser App, insbesondere im Umgang mit Nextcloud 31+ und Docker-Umgebungen (AIO).

---

## 1. Datenbank-Schema anpassen

Nextcloud nutzt ab den neueren Versionen ein striktes Migrations-Framework. Alte Wege funktionieren nicht mehr.

### ❌ Was NICHT mehr geht
Die Datei `appinfo/database.xml` ist komplett **veraltet (deprecated)** und wird vom System ignoriert. Verwende sie niemals wieder, um Tabellen anzulegen.

### ✅ Wie es richtig geht (Migrations)
Wenn du eine neue Tabelle brauchst oder eine bestehende ändern willst:

1. Gehe in den Ordner `lib/Migration/`.
2. Erstelle eine neue Datei mit einem eindeutigen, aufsteigenden Timestamp im Namen, z.B. `Version20260608150000.php`.
3. Nutze die `ISchemaMigration`-Schnittstelle.

**Beispiel-Struktur:**

```php
<?php
declare(strict_types=1);

namespace OCA\AeneasDispensary\Migration;

use OCP\Migration\ISchemaMigration;
use OCP\Migration\IOutput;
use Closure;

class Version20260608150000 implements ISchemaMigration {
    public function migrate(IOutput $output, Closure $schemaClosure, array $options): void {
        $schema = $schemaClosure();
        
        // Prüfen, ob Tabelle existiert
        if (!$schema->hasTable('aeneas_abgabe')) {
            $table = $schema->createTable('aeneas_abgabe');
            // ... Spalten definieren (siehe Codebase)
        }
    }
}
```

### 🚀 Migration ausführen
Damit Nextcloud die neue Datei erkennt und das SQL an die Datenbank schickt, führe in deinem Nextcloud-Stammverzeichnis (dort wo die `occ` Datei liegt) aus:

```bash
sudo -u www-data php occ migrations:migrate aeneas_dispensary
```
*Tipp: Wenn das nicht klappt, erhöhe die `<version>` in der `appinfo/info.xml` (z.B. von 1.0.1 auf 1.0.2) und führe `sudo -u www-data php occ upgrade` aus.*

---

## 2. Datenbank Notfall-Troubleshooting (AIO Docker)

**Das Problem:** Manchmal verschluckt sich Nextcloud. Die App denkt, die Migration wurde schon gemacht (steht in der Tabelle `oc_migrations`), aber in PostgreSQL existiert die Tabelle gar nicht. Du kriegst dann Fehler wie `relation "oc_aeneas_abgabe" does not exist`.

**Die Lösung (ohne psql in Docker zu fummeln):**
Nutze die interne Datenbankverbindung von Nextcloud per Command-Line-Einzeiler. 

Kopiere diesen Befehl exakt so in die Konsole (der Befehl löst das `*PREFIX*` automatisch auf):

```bash
sudo -u www-data php -r "require_once '/var/www/html/lib/base.php'; \$db = \OC::\$server->get(\OCP\IDBConnection::class); \$db->executeQuery(\"CREATE TABLE *PREFIX*aeneas_abgabe (id SERIAL PRIMARY KEY, user_id VARCHAR(64) NOT NULL, amount INTEGER NOT NULL, timestamp INTEGER NOT NULL, edited_by VARCHAR(64), edited_at INTEGER)\"); \$db->executeQuery(\"CREATE INDEX abgabe_user_idx ON *PREFIX*aeneas_abgabe(user_id)\"); \$db->executeQuery(\"INSERT INTO *PREFIX*migrations (app, version) VALUES ('aeneas_dispensary', '20260608150000')\"); echo \"Tabelle erfolgreich angelegt!\n\";"
```

Vergiss danach nicht, die Caches zu leeren:

```bash
sudo -u www-data php occ cache:clear
```

*(Hinweis für `vi`-Nutzer: Beim Reinkopieren von Code in Dateien immer vorher `i` drücken für den Insert-Modus, sonst fehlt die erste Zeile!)*

---

## 3. Nextcloud 31+ Architektur Best Practices

Damit die App zukunftssicher bleibt, halte dich an diese Regeln:

### Routing & URLs
* **Keine Controller-Attribute:** Das Routing (`#[ApiRoute]` etc.) im Controller ist fehleranfällig. Die "Single Source of Truth" ist die Datei `appinfo/routes.php`.
* **URLs dynamisch generieren:** Niemals URLs hardcoden (`/apps/aeneas/...`). Nutze in PHP immer `\OC::$server->getURLGenerator()->linkToRoute('aeneas_dispensary.controller.action')` und im JS `OC.generateUrl('/apps/aeneas_dispensary/...')`.

### Security (CSRF & Limits)
* **CSRF-Schutz:** Setze das Attribut `#[NoCSRFRequired]` **nur** auf `GET`-Methoden, die das anfängliche HTML-Template laden. Alle `POST`/`PUT`-Methoden, die Daten verändern, müssen ohne dieses Attribut auskommen.
* **JS-Requests:** Sende bei POST-Requests immer den Header `'requesttoken': OC.requestToken` mit.
* **Validierung:** Traue niemals dem User-Input. Prüfe im Service immer, ob z.B. Mengen `<= 0` sind.

### Frontend & CSP (Content Security Policy)
* **Keine Inline-Styles:** Nextcloud blockiert aus Sicherheitsgründen hartcodiertes CSS wie `style="color: red;"` oder `element.style.color = 'red';` in JavaScript.
* **Klassen nutzen:** Definiere alles in einer `css/dispensary.css` (lade sie mit `style('aeneas_dispensary', 'dispensary')` im Template) und wechsle Zustände im JS nur über `classList.add('error')`.
* **Anti-Double-Submit:** Deaktiviere Buttons (`btn.disabled = true`) sofort beim Klick, bis der Fetch-Request im `.finally()`-Block abgeschlossen ist, um asynchrone DB-Race-Conditions zu verhindern.