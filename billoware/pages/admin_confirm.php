<?php
// pages/admin_confirm.php

require_once __DIR__ . '/../utils/check_admin.php';
require_once __DIR__ . '/../services/user_service.php';
require_once __DIR__ . '/../services/ad_service.php';

$type = $_GET['type'] ?? ($_POST['type'] ?? '');
$id   = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));

function back_to_panel(): void {
    header('Location: /billoware/pages/admin_panel.php');
    exit;
}

// Aktion ausführen (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($type === 'toggle_user' && $id > 0) {
        // User holen (wenn ihr keine find_by_id Funktion habt, via list_all suchen)
        $users = user_list_all();
        foreach ($users as $u) {
            if ((int)$u['id'] === $id) {
                $new = ((int)$u['is_blocked'] === 1) ? 0 : 1;
                user_set_blocked($id, $new);
                $_SESSION['flash_info'] = $new ? 'Benutzer wurde gesperrt.' : 'Benutzer wurde reaktiviert.';
                break;
            }
        }
    }

    elseif ($type === 'delete_ad' && $id > 0) {
        ad_delete($id);
        $_SESSION['flash_info'] = 'Anzeige wurde gelöscht.';
    }

    elseif ($type === 'delete_user' && $id > 0) {
        $currentAdminId = (int)($_SESSION['user']['id'] ?? 0);

        if ($id === $currentAdminId) {
            $_SESSION['flash_info'] = 'Du kannst dich nicht selbst löschen.';
        } else {
            user_delete_by_id($id);
            $_SESSION['flash_info'] = 'Benutzer wurde gelöscht.';
        }
    }

    back_to_panel();
}

// GET: Bestätigung anzeigen
$label = '';
$dangerText = '';

if ($type === 'toggle_user') {
    $label = 'Benutzer sperren/reaktivieren';
} elseif ($type === 'delete_ad') {
    $label = 'Anzeige löschen';
    $dangerText = 'Diese Aktion kann nicht rückgängig gemacht werden.';
} elseif ($type === 'delete_user') {
    $label = 'Benutzer löschen';
    $dangerText = 'Alle Anzeigen und Bilder dieses Benutzers werden ebenfalls gelöscht.';
} else {
    back_to_panel();
}

if ($id <= 0) back_to_panel();

include __DIR__ . '/../partials/header.php';
?>

<h1 class="h4 mb-4">Bestätigung</h1>

<div class="d-flex justify-content-center">
  <div class="bg-light border rounded p-3" style="max-width: 520px; width: 100%;">
    <p class="mb-2">Willst du wirklich: <strong><?php echo htmlspecialchars($label); ?></strong>?</p>

    <?php if ($dangerText): ?>
      <p class="small text-muted mb-3"><?php echo htmlspecialchars($dangerText); ?></p>
    <?php endif; ?>

    <form method="post" class="d-flex gap-2">
      <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$id); ?>">

      <button type="submit" class="btn btn-danger btn-sm">Ja, bestätigen</button>
      <a href="/billoware/pages/admin_panel.php" class="btn btn-outline-secondary btn-sm">Abbrechen</a>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
