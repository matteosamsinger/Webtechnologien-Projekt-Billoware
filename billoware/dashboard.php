<?php
// dashboard.php

// 1. Session & Login-/Rollen-Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'user') {
    // Nur normale User dürfen das Dashboard sehen
    header('Location: login.php');
    exit;
}

$userEmail = $_SESSION['user']['email'] ?? '';

// 2. Anzeigen des Users aus der Session holen (wenn es schon welche gibt)
$allAds = $_SESSION['ads'] ?? [];
$myAds  = [];

foreach ($allAds as $ad) {
    if (($ad['owner'] ?? '') === $userEmail) {
        $myAds[] = $ad;
    }
}

// 3. Header einbinden (Navbar, <html>, <body>, <main>)
include __DIR__ . '/partials/header.php';
?>

<h1 class="mb-4">Mein Dashboard</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="h5 mb-0">Meine Anzeigen</h2>
  <a href="create_ad.php" class="btn btn-sm btn-success">
    + Neue Anzeige erstellen
  </a>
</div>

<?php if (empty($myAds)): ?>

  <!-- Wenn der User noch keine eigenen Anzeigen hat -->
  <div class="alert alert-info">
    Du hast noch keine Anzeigen erstellt.
    Klicke oben auf <strong>„Neue Anzeige erstellen“</strong>, um deine erste Anzeige zu veröffentlichen.
  </div>

<?php else: ?>


  <!-- Liste der eigenen Anzeigen -->
  <div class="row g-3">
    <?php foreach ($myAds as $ad): ?>
      <div class="col-md-6">
        <div class="card h-100">
          <?php if (!empty($ad['image'])): ?>
            <img
              src="uploads/<?php echo htmlspecialchars($ad['image']); ?>"
              class="card-img-top"
              alt="Bild zu <?php echo htmlspecialchars($ad['title']); ?>"
            >
          <?php endif; ?>

          <div class="card-body d-flex flex-column">
            <h5 class="card-title mb-1">
              <?php echo htmlspecialchars($ad['title'] ?? ''); ?>
            </h5>

            <?php if (!empty($ad['price'])): ?>
              <p class="fw-bold mb-2">
                <?php echo htmlspecialchars($ad['price']); ?> €
              </p>
            <?php endif; ?>

            <?php if (!empty($ad['description'])): ?>
              <p class="card-text small text-muted mb-3">
                <?php
                // Beschreibung etwas kürzen für die Übersicht
                $short = mb_strimwidth($ad['description'], 0, 120, '…');
                echo nl2br(htmlspecialchars($short));
                ?>
              </p>
            <?php endif; ?>

            <div class="mt-auto d-flex gap-2">
              <a
                href="ad_detail.php?id=<?php echo urlencode($ad['id'] ?? ''); ?>"
                class="btn btn-sm btn-outline-primary"
              >
                Ansehen
              </a>
              <a
                href="edit_ad.php?id=<?php echo urlencode($ad['id'] ?? ''); ?>"
                class="btn btn-sm btn-outline-secondary"
              >
                Bearbeiten
              </a>
              <a
                href="delete_ad.php?id=<?php echo urlencode($ad['id'] ?? ''); ?>"
                class="btn btn-sm btn-outline-danger"
              >
                Löschen
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
