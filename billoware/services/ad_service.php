<?php
declare(strict_types=1);

// DB-Verbindung 
require_once __DIR__ . '/../utils/db.php';

// CREATE: neue Anzeige speichern
function ad_create(int $userId, array $data): int
{
    $stmt = db()->prepare("
        INSERT INTO ads (user_id, title, description, price, contact_email, contact_phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $userId,
        $data['title'],
        $data['description'],
        $data['price'],
        $data['contact_email'],
        $data['contact_phone'],
    ]);

    return (int)db()->lastInsertId();
}

// CREATE: Bilder einer Anzeige speichern (DB-Tabelle ad_images)
function ad_add_images(int $adId, array $filenames): void
{
    $stmt = db()->prepare("INSERT INTO ad_images (ad_id, filename, sort_order) VALUES (?, ?, ?)");

    $order = 1;
    foreach ($filenames as $fn) {
        $stmt->execute([$adId, $fn, $order]);
        $order++;
    }
}

// READ: einzelne Anzeige laden (JOIN mit users, damit owner_email/owner_name verfügbar ist)
function ad_get(int $adId): ?array
{
    $stmt = db()->prepare("
        SELECT a.*, u.email AS owner_email, u.name AS owner_name
        FROM ads a
        JOIN users u ON u.id = a.user_id
        WHERE a.id = ?
        LIMIT 1
    ");
    $stmt->execute([$adId]);
    $ad = $stmt->fetch();
    return $ad ?: null;
}

// READ: Bilder zu einer Anzeige laden
function ad_get_images(int $adId): array
{
    $stmt = db()->prepare("SELECT * FROM ad_images WHERE ad_id = ? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([$adId]);
    return $stmt->fetchAll();
}

// READ: alle Anzeigen eines Users (für Dashboard)
function ad_list_by_user(int $userId): array
{
    $stmt = db()->prepare("SELECT * FROM ads WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// READ: neueste Anzeigen (Startseite + optional Suche via LIKE)
// damit kein SQL-Injection/kaputtes SQL möglich ist.
function ad_list_latest(?string $q = null, int $limit = 30): array
{
    // Sicherheits-Clamp: nur sinnvolle Grenzen zulassen
    $limit = (int)$limit;
    if ($limit < 1)   $limit = 1;
    if ($limit > 200) $limit = 200;

    if ($q !== null && $q !== '') {
        $like = '%' . $q . '%';

        $stmt = db()->prepare("
            SELECT a.*, u.email AS owner_email
            FROM ads a
            JOIN users u ON u.id = a.user_id
            WHERE a.title LIKE ? OR a.description LIKE ?
            ORDER BY a.created_at DESC
            LIMIT $limit
        ");
        $stmt->execute([$like, $like]);
        return $stmt->fetchAll();
    }

    $stmt = db()->query("
        SELECT a.*, u.email AS owner_email
        FROM ads a
        JOIN users u ON u.id = a.user_id
        ORDER BY a.created_at DESC
        LIMIT $limit
    ");
    return $stmt->fetchAll();
}


// READ: alle Anzeigen (Admin Panel)
function ad_list_all(): array
{
    $stmt = db()->query("
        SELECT a.*, u.email AS owner_email
        FROM ads a JOIN users u ON u.id=a.user_id
        ORDER BY a.created_at DESC
    ");
    return $stmt->fetchAll();
}

// Permission: darf User die Anzeige bearbeiten? (Owner oder Admin)
function ad_user_can_edit(int $adId, int $userId, bool $isAdmin): bool
{
    if ($isAdmin) return true;

    $stmt = db()->prepare("SELECT user_id FROM ads WHERE id = ? LIMIT 1");
    $stmt->execute([$adId]);
    $row = $stmt->fetch();
    if (!$row) return false;

    return (int)$row['user_id'] === $userId;
}

// UPDATE: Anzeige aktualisieren
function ad_update(int $adId, array $data): void
{
    // owner/admin check macht die Page vorher
    $stmt = db()->prepare("
        UPDATE ads
        SET title=?, description=?, price=?, contact_email=?, contact_phone=?
        WHERE id=?
    ");
    $stmt->execute([
        $data['title'],
        $data['description'],
        $data['price'],
        $data['contact_email'],
        $data['contact_phone'],
        $adId
    ]);
}

// DELETE: Anzeige löschen (löscht zuerst Upload-Dateien, dann DB; DB CASCADE löscht ad_images rows)
function ad_delete(int $adId): void
{
    // filenames holen, damit wir Dateien löschen können
    $imgs = ad_get_images($adId);
    $uploadDir = __DIR__ . '/../uploads/';

    foreach ($imgs as $img) {
        $fn = (string)$img['filename'];
        $path = $uploadDir . $fn;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    // ads löschen -> CASCADE löscht ad_images rows
    $stmt = db()->prepare("DELETE FROM ads WHERE id = ?");
    $stmt->execute([$adId]);
}


// DELETE: einzelnes Bild löschen (DB + Datei)
function ad_image_delete(int $imageId): void
{
    $stmt = db()->prepare("SELECT filename FROM ad_images WHERE id = ? LIMIT 1");
    $stmt->execute([$imageId]);
    $img = $stmt->fetch();
    if ($img) {
        $path = __DIR__ . '/../uploads/' . $img['filename'];
        if (is_file($path)) @unlink($path);
    }

    $stmt2 = db()->prepare("DELETE FROM ad_images WHERE id = ?");
    $stmt2->execute([$imageId]);
}

// Helper: Anzahl Bilder (für max. 3 Limit beim Edit)
function ad_count_images(int $adId): int
{
    $stmt = db()->prepare("SELECT COUNT(*) AS c FROM ad_images WHERE ad_id = ?");
    $stmt->execute([$adId]);
    $row = $stmt->fetch();
    return (int)($row['c'] ?? 0);
}
