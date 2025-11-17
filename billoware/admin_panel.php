<?php
// admin_panel.php

// 1. Session starten & prüfen, ob Admin eingeloggt ist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

// 2. Userliste einbinden (Fake-User aus data/users.php)
require __DIR__ . '/data/users.php';

// 3. Aktionen verarbeiten (Ad löschen, User sperren/reaktivieren)
$infoMessage = '';

$allAds = $_SESSION['ads'] ?? [];
$blockedUsers = $_SESSION['blocked_users'] ?? [];

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Anzeige löschen
    if ($action === 'delete_ad' && isset($_GET['id'])) {
        $id = $_GET['id'];

        if (!empty($_SESSION['ads'])) {
            foreach ($_SESSION['ads'] as $index => $ad) {
                if (($ad['id'] ?? '') === $id) {
                    unset($_SESSION['ads'][$index]);
                    // array neu indexieren
                    $_SESSION['ads'] = array_values($_SESSION['ads']);
                    $infoMessage = 'Anzeige wurde gelöscht.';
                    break;
                }
            }
        }
    }

    // Benutzer sperren / reaktivieren
    if ($action === 'toggle_user' && isset($_GET['email'])) {
        $email = $_GET['email'];

        if (!isset($_SESSION['blocked_users']) || !is_array($_SESSION['blocked_users'])) {
            $_SESSION['blocked_users'] = [];
        }

        if (in_array($email, $_SESSION['blocked_users'], true)) {
            // User wieder freischalten
            $_SESSION['blocked_users'] = array_values(
                array_diff($_SESSION['blocked_users'], [$email])
            );
            $infoMessage = 'Benutzer wurde reaktiviert.';
        } else {
            // User sperren
            $_SESSION['blocked_users'][] = $email;
            $infoMessage = 'Benutzer wurde gesperrt.';
        }

        $blockedUsers = $_SESSION['blocked_users'];
    }
}

// 4. Daten für Ausgabe vorbereiten
$allAds = $_SESSION['ads'] ?? [];
$blockedUsers = $_SESSION['blocked_users'] ?? [];

// 5. Header einbinden
include __DIR__ . '/partials/header.php';
?>

<h1 class="mb-4">Admin Panel</h1>

<?php if (!empty($infoMessage)): ?>
  <div class="alert alert-info">
    <?php echo htmlspecialchars($infoMessage); ?>
  </div>
<?php endif; ?>

<div class="row">
  <!-- ANZEIGEN-VERWALTUNG -->
  <div class="col-lg-7 mb-4">
    <h2 class="h5 mb-3">Anzeigen verwalten</h2>

    <?php if (empty($allAds)): ?>
      <div class="alert alert-secondary">
        Es sind aktuell keine Anzeigen im System vorhanden.
      </div>
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
          <?php foreach ($allAds as $ad): ?>
            <tr>
              <td><?php echo htmlspecialchars($ad['title'] ?? ''); ?></td>
              <td class="small">
                <?php echo htmlspecialchars($ad['owner'] ?? ''); ?>
              </td>
              <td>
                <?php echo $ad['price'] !== '' ? htmlspecialchars($ad['price']) . ' €' : '-'; ?>
              </td>
              <td>
                <a
                  href="ad_detail.php?id=<?php echo urlencode($ad['id'] ?? ''); ?>"
                  class="btn btn-sm btn-outline-primary"
                >
                  Ansehen
                </a>
                <a
                  href="admin_panel.php?action=delete_ad&id=<?php echo urlencode($ad['id'] ?? ''); ?>"
                  class="btn btn-sm btn-outline-danger"
                  onclick="return confirm('Anzeige wirklich löschen?');"
                >
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

  <!-- BENUTZER-VERWALTUNG -->
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
            $email = $u['email'];
            $role  = $u['role'];
            $isBlocked = in_array($email, $blockedUsers, true);
          ?>
          <tr>
            <td class="small"><?php echo htmlspecialchars($email); ?></td>
            <td><?php echo htmlspecialchars($role); ?></td>
            <td>
              <?php if ($isBlocked): ?>
                <span class="badge text-bg-danger">gesperrt</span>
              <?php else: ?>
                <span class="badge text-bg-success">aktiv</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($email === ($_SESSION['user']['email'] ?? '')): ?>
                <!-- Sich selbst nicht sperren -->
                <span class="small text-muted">–</span>
              <?php else: ?>
                <a
                  href="admin_panel.php?action=toggle_user&email=<?php echo urlencode($email); ?>"
                  class="btn btn-sm btn-outline-secondary"
                >
                  <?php echo $isBlocked ? 'Reaktivieren' : 'Sperren'; ?>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <p class="small text-muted mt-2">
      Hinweis: Die Sperr-Informationen werden aktuell nur in der Session gespeichert
      und gehen verloren, wenn die Session abläuft oder der Server neu gestartet wird.
    </p>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
