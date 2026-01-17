<?php
// Login-Guard + DB-Service
require_once __DIR__ . '/../utils/check_login.php';
require_once __DIR__ . '/../services/ad_service.php';

// User-Rechte (Owner/Admin)
$userId = (int)$_SESSION['user']['id'];
$isAdmin = (($_SESSION['user']['role'] ?? '') === 'admin');

// Ad-ID aus GET oder POST holem
$adId = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));

// Anzeige laden
$ad = $adId ? ad_get($adId) : null;

// Wenn keine Anzeige: zurück
if (!$ad) { header('Location: /billoware/pages/dashboard.php'); exit; }

// Rechte prüfen: nur Owner oder Admin darf löschen
if (!ad_user_can_edit($adId, $userId, $isAdmin)) {
    header('Location: /billoware/pages/dashboard.php');
    exit;
}

// POST = User hat bestätigt -> wirklich löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ad_delete($adId); // löscht Bilderdateien + DB-Einträge
    flash_set('info', 'Anzeige wurde gelöscht.');
    header('Location: /billoware/pages/dashboard.php');
    exit;
}

// GET = Bestätigungsseite anzeigen
include __DIR__ . '/../partials/header.php';
?>

<h1 class="h4 mb-4">Anzeige löschen</h1>

<div class="d-flex justify-content-center">
  <div class="bg-light border rounded p-3" style="max-width: 420px; width: 100%;">
    <p class="mb-2">Willst du diese Anzeige wirklich löschen?</p>
    <p class="fw-bold small mb-3"><?php echo htmlspecialchars($ad['title']); ?></p>

    <form method="post" class="d-flex gap-2">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$adId); ?>">
      <button type="submit" class="btn btn-danger btn-sm">Ja, löschen</button>
      <a href="/billoware/pages/ad_detail.php?id=<?php echo urlencode((string)$adId); ?>" class="btn btn-outline-secondary btn-sm">Abbrechen</a>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
