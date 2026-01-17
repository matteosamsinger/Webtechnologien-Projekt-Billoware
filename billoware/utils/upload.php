<?php
declare(strict_types=1);


/**
 * Helper für multiple Uploads:
 * - akzeptiert JPG/PNG/WEBP
 * - maximal $maxFiles
 * - speichert in /uploads
 * - gibt filenames + errors zurück
 */
function upload_images(array $files, int $maxFiles = 3): array
{
    $allowedExt = ['jpg','jpeg','png','webp'];
    $stored = [];
    $errors = [];

    // normalize multiple upload arrays
    $names = $files['name'] ?? [];
    $tmp   = $files['tmp_name'] ?? [];
    $err   = $files['error'] ?? [];
    $count = is_array($names) ? count($names) : 0;

    // leere Auswahl -> ok
    $nonEmpty = 0;
    for ($i=0; $i<$count; $i++) {
        if (!empty($names[$i])) $nonEmpty++;
    }
    if ($nonEmpty === 0) {
        return ['files' => [], 'errors' => []];
    }

    if ($nonEmpty > $maxFiles) {
        return ['files' => [], 'errors' => ["Maximal $maxFiles Bilder erlaubt."]];
    }

    $targetDir = __DIR__ . '/../uploads/';
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            return ['files' => [], 'errors' => ['Upload-Ordner konnte nicht erstellt werden.']];
        }
    }

    for ($i=0; $i<$count; $i++) {
        if (empty($names[$i])) continue;

        if (($err[$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $errors[] = 'Fehler beim Hochladen eines Bildes.';
            continue;
        }

        $origName = $names[$i];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt, true)) {
            $errors[] = "Ungültiges Bildformat ($origName). Erlaubt: JPG, PNG, WEBP.";
            continue;
        }

        $newName = uniqid('ad_', true) . '.' . $ext;
        $targetPath = $targetDir . $newName;

        if (!move_uploaded_file($tmp[$i], $targetPath)) {
            $errors[] = "Fehler beim Speichern ($origName).";
            continue;
        }

        $stored[] = $newName;
    }

    return ['files' => $stored, 'errors' => $errors];
}
