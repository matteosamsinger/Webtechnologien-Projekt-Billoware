<?php
declare(strict_types=1);

require_once __DIR__ . '/../utils/db.php';

// User suchen (für Register/Login/Check)
function user_find_by_email(string $email): ?array
{
    $stmt = db()->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    return $u ?: null;
}

// User erstellen (Passwort wird gehasht gespeichert)
function user_create(string $name, string $email, string $passwordPlain): int
{
    $hash = password_hash($passwordPlain, PASSWORD_DEFAULT);

    $stmt = db()->prepare("
        INSERT INTO users (name, email, password_hash, role, is_blocked)
        VALUES (?, ?, ?, 'user', 0)
    ");
    $stmt->execute([$name, $email, $hash]);

    return (int)db()->lastInsertId();
}

// Login prüfen (existiert? gesperrt? password_verify?)
function user_verify_login(string $email, string $passwordPlain): array
{
    $user = user_find_by_email($email);

    if (!$user) {
        return ['ok' => false, 'error' => 'E-Mail oder Passwort ist falsch.'];
    }

    if ((int)($user['is_blocked'] ?? 0) === 1) {
        return ['ok' => false, 'error' => 'Dein Account wurde gesperrt. Bitte kontaktiere den Admin.'];
    }

    if (!password_verify($passwordPlain, (string)$user['password_hash'])) {
        return ['ok' => false, 'error' => 'E-Mail oder Passwort ist falsch.'];
    }

    return ['ok' => true, 'user' => $user];
}

// Admin: alle User listen
function user_list_all(): array
{
    $stmt = db()->query("SELECT id, name, email, role, is_blocked, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

// Admin: sperren/entsperren
function user_set_blocked(int $userId, int $blocked): void
{
    $stmt = db()->prepare("UPDATE users SET is_blocked = ? WHERE id = ?");
    $stmt->execute([$blocked, $userId]);
}

// Admin: User löschen inkl. seiner Bilderdateien (Ads & DB-Images werden per FK CASCADE gelöscht)
function user_delete_by_id(int $userId): void
{
    // 1) Bilddateien finden (über Ads des Users)
    $stmt = db()->prepare("
        SELECT ai.filename
        FROM ad_images ai
        JOIN ads a ON a.id = ai.ad_id
        WHERE a.user_id = ?
    ");
    $stmt->execute([$userId]);
    $files = $stmt->fetchAll();

    $uploadDir = __DIR__ . '/../uploads/';
    foreach ($files as $row) {
        $fn = (string)($row['filename'] ?? '');
        if ($fn !== '') {
            $path = $uploadDir . $fn;
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    // 2) User löschen (Ads + ad_images fliegen per FK CASCADE mit)
    $stmt2 = db()->prepare("DELETE FROM users WHERE id = ?");
    $stmt2->execute([$userId]);
}

