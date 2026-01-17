<?php
// Services/Helpers laden: DB-Funktionen für Anzeigen + Session/Flash/Login-Infos
require_once __DIR__ . '/../services/ad_service.php';
require_once __DIR__ . '/../utils/session.php';

// ID aus der URL holen und Anzeige + Bilder aus DB laden
$id = (int)($_GET['id'] ?? 0);
$ad = $id ? ad_get($id) : null;
$images = $ad ? ad_get_images($id) : [];

// Rechte/Status bestimmen
$isLoggedIn = isset($_SESSION['user']);
$isAdmin = $isLoggedIn && (($_SESSION['user']['role'] ?? '') === 'admin');
$isOwner = $isLoggedIn && ((int)$_SESSION['user']['id'] === (int)($ad['user_id'] ?? -1));

// Header (Navbar + Flash-Messages + HTML-Start)
include __DIR__ . '/../partials/header.php';
?>

<?php if (!$ad): ?>
  <!-- Fehlerfall: ID existiert nicht in DB -->
  <div class="alert alert-secondary">Anzeige nicht gefunden.</div>
  <a href="/billoware/pages/index.php" class="btn btn-outline-secondary btn-sm">← Zurück</a>
  <?php include __DIR__ . '/../partials/footer.php'; exit; ?>
<?php endif; ?>

<section class="row">
  <div class="col-md-7">
    <div class="border bg-white rounded p-3 mb-3">
      <?php if (!empty($images)): ?>
        <?php foreach ($images as $img): ?>
          <img src="../uploads/<?php echo htmlspecialchars($img['filename']); ?>" class="img-fluid rounded mb-2" alt="">
        <?php endforeach; ?>
      <?php else: ?>
        <div class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center rounded">
          <span class="text-muted">Kein Bild vorhanden</span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="col-md-5">
    <h1 class="h3 mb-2"><?php echo htmlspecialchars($ad['title']); ?></h1>
    <p class="fw-bold fs-4 mb-3"><?php echo htmlspecialchars((string)$ad['price']); ?> €</p>

    <p class="mb-3"><?php echo nl2br(htmlspecialchars($ad['description'])); ?></p>

    <div class="mb-3">
      <h2 class="h6">Kontakt</h2>
      <p class="mb-1"><strong>E-Mail:</strong>
        <a href="mailto:<?php echo htmlspecialchars($ad['contact_email']); ?>">
          <?php echo htmlspecialchars($ad['contact_email']); ?>
        </a>
      </p>
      <?php if (!empty($ad['contact_phone'])): ?>
        <p class="mb-1"><strong>Telefon:</strong> <?php echo htmlspecialchars($ad['contact_phone']); ?></p>
      <?php endif; ?>
    </div>

    <div class="d-flex gap-2">
      <a href="/billoware/pages/index.php" class="btn btn-outline-secondary btn-sm">← Zurück</a>

      <?php if ($isOwner || $isAdmin): ?>
        <a href="/billoware/pages/edit_ad.php?id=<?php echo urlencode((string)$ad['id']); ?>" class="btn btn-outline-primary btn-sm">Bearbeiten</a>
        <a href="/billoware/pages/delete_ad.php?id=<?php echo urlencode((string)$ad['id']); ?>" class="btn btn-outline-danger btn-sm">Löschen</a>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../partials/footer.php'; ?>
