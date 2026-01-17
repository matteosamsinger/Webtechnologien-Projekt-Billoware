<?php
// Login-Guard + DB-Service
require_once __DIR__ . '/../utils/check_login.php';
require_once __DIR__ . '/../services/ad_service.php';

// User aus Session
$userId = (int)$_SESSION['user']['id'];

// Begrüßung: Name aus Session, falls leer -> E-Mail
$userName  = trim((string)($_SESSION['user']['name'] ?? ''));
$userEmail = (string)($_SESSION['user']['email'] ?? '');
$displayName = $userName !== '' ? $userName : $userEmail;

// Anzeigen des Users aus DB holen
$myAds = ad_list_by_user($userId);

include __DIR__ . '/../partials/header.php';
?>

<!-- Begrüßung + Liste der eigenen Anzeigen -->
<p class="text-muted mb-1">Hallo <?php echo htmlspecialchars($displayName); ?>! 👋</p>

<h1 class="mb-4">Mein Dashboard</h1>


<!-- Pro Anzeige: erstes Bild laden und Buttons für Detail/Edit/Delete -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h5 mb-0">Meine Anzeigen</h2>
  <a href="/billoware/pages/create_ad.php" class="btn btn-sm btn-success">+ Neue Anzeige erstellen</a>
</div>

<?php if (empty($myAds)): ?>
  <div class="alert alert-info">Du hast noch keine Anzeigen erstellt.</div>
<?php else: ?>
  <div class="row g-3">
    <?php foreach ($myAds as $ad): ?>
      <?php
        $imgs = ad_get_images((int)$ad['id']);
        $first = $imgs[0]['filename'] ?? null;
      ?>
      <div class="col-md-6">
        <div class="card h-100">
          <?php if ($first): ?>
            <img src="../uploads/<?php echo htmlspecialchars($first); ?>" class="card-img-top" alt="">
          <?php endif; ?>
          <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-1"><?php echo htmlspecialchars($ad['title']); ?></h5>
            <p class="fw-bold mb-2"><?php echo htmlspecialchars((string)$ad['price']); ?> €</p>
            <p class="card-text small text-muted mb-3"><?php echo nl2br(htmlspecialchars(mb_strimwidth($ad['description'], 0, 120, '…'))); ?></p>

            <div class="mt-auto d-flex gap-2">
              <a class="btn btn-sm btn-outline-primary" href="/billoware/pages/ad_detail.php?id=<?php echo urlencode((string)$ad['id']); ?>">Ansehen</a>
              <a class="btn btn-sm btn-outline-secondary" href="/billoware/pages/edit_ad.php?id=<?php echo urlencode((string)$ad['id']); ?>">Bearbeiten</a>
              <a class="btn btn-sm btn-outline-danger" href="/billoware/pages/delete_ad.php?id=<?php echo urlencode((string)$ad['id']); ?>">Löschen</a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
