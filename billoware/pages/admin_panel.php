<?php
// Admin-Guard
require_once __DIR__ . '/../utils/check_admin.php';


// Services für User/Ads (DB-Funktionen)
require_once __DIR__ . '/../services/user_service.php';
require_once __DIR__ . '/../services/ad_service.php';

$info = '';


// Aktionen über GET (?action=...)
if (isset($_GET['action'])) {
    $action = $_GET['action'];

        // User sperren/reaktivieren
    if ($action === 'toggle_user' && isset($_GET['id'])) {
        $uid = (int)$_GET['id'];

        // Userliste holen und passenden User finden um Status zu toggeln
        $users = user_list_all();
        foreach ($users as $u) {
            if ((int)$u['id'] === $uid) {
                $new = ((int)$u['is_blocked'] === 1) ? 0 : 1;
                user_set_blocked($uid, $new);
                $info = $new ? 'Benutzer wurde gesperrt.' : 'Benutzer wurde reaktiviert.';
                break;
            }
        }
    }

        // Anzeige löschen (inkl. Bilderdateien über ad_delete)
    if ($action === 'delete_ad' && isset($_GET['id'])) {
        $adId = (int)$_GET['id'];
        ad_delete($adId);
        $info = 'Anzeige wurde gelöscht.';
    }

    // User löschen (inkl. seiner Anzeigen + Bilder)
    if ($action === 'delete_user' && isset($_GET['id'])) {
    $uid = (int)$_GET['id'];
    $currentAdminId = (int)($_SESSION['user']['id'] ?? 0);

    // Admin kann sich nicht selbst löschen
    if ($uid === $currentAdminId) {
        $info = 'Du kannst dich nicht selbst löschen.';
    } else {
        user_delete_by_id($uid);
        $info = 'Benutzer wurde gelöscht.';
    }
}

}


// Daten für die Tabellen laden
$users = user_list_all();
$ads = ad_list_all();


// Header
include __DIR__ . '/../partials/header.php';
?>

<h1 class="mb-4">Admin Panel</h1>

<?php if ($info): ?>
  <div class="alert alert-info"><?php echo htmlspecialchars($info); ?></div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-7 mb-4">
    <h2 class="h5 mb-3">Anzeigen verwalten</h2>

    <?php if (empty($ads)): ?>
      <div class="alert alert-secondary">Keine Anzeigen vorhanden.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th>Titel</th>
              <th>Besitzer</th>
              <th>Preis</th>
              <th>Aktionen</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($ads as $ad): ?>
              <tr>
                <td><?php echo htmlspecialchars($ad['title']); ?></td>
                <td class="small"><?php echo htmlspecialchars($ad['owner_email']); ?></td>
                <td><?php echo htmlspecialchars((string)$ad['price']); ?> €</td>
                <td>
                  <a class="btn btn-sm btn-outline-primary" href="/billoware/pages/ad_detail.php?id=<?php echo urlencode((string)$ad['id']); ?>">Ansehen</a>
                  <a class="btn btn-sm btn-outline-danger"
                     href="/billoware/pages/admin_panel.php?action=delete_ad&id=<?php echo urlencode((string)$ad['id']); ?>"
                     onclick="return confirm('Anzeige wirklich löschen?');">
                    Löschen
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <div class="col-lg-5 mb-4">
    <h2 class="h5 mb-3">Benutzer verwalten</h2>

    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>E-Mail</th>
            <th>Rolle</th>
            <th>Status</th>
            <th>Aktion</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <?php
              $isBlocked = ((int)$u['is_blocked'] === 1);
              $isSelf = ((int)$u['id'] === (int)($_SESSION['user']['id'] ?? -1));
            ?>
            <tr>
              <td class="small"><?php echo htmlspecialchars($u['email']); ?></td>
              <td><?php echo htmlspecialchars($u['role']); ?></td>
              <td>
                <?php if ($isBlocked): ?>
                  <span class="badge text-bg-danger">gesperrt</span>
                <?php else: ?>
                  <span class="badge text-bg-success">aktiv</span>
                <?php endif; ?>
              </td>
             <td>
  <?php if ($isSelf): ?>
    <span class="small text-muted">–</span>
  <?php else: ?>
    <div class="d-flex gap-2">
      <a class="btn btn-sm btn-outline-secondary"
         href="/billoware/pages/admin_panel.php?action=toggle_user&id=<?php echo urlencode((string)$u['id']); ?>">
        <?php echo $isBlocked ? 'Reaktivieren' : 'Sperren'; ?>
      </a>

      <a class="btn btn-sm btn-outline-danger"
         href="/billoware/pages/admin_panel.php?action=delete_user&id=<?php echo urlencode((string)$u['id']); ?>"
         onclick="return confirm('Benutzer wirklich löschen? Alle Anzeigen & Bilder werden ebenfalls gelöscht.');">
        Löschen
      </a>
    </div>
  <?php endif; ?>
</td>

            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
