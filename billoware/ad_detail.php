<?php
// ad_detail.php

// 1. Session starten, bevor irgendwas ausgegeben wird
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Anzeige anhand der ID aus der Session suchen
$ad = null;

if (!empty($_GET['id']) && !empty($_SESSION['ads']) && is_array($_SESSION['ads'])) {
    $id = $_GET['id'];

    foreach ($_SESSION['ads'] as $candidate) {
        if (($candidate['id'] ?? null) === $id) {
            $ad = $candidate;
            break;
        }
    }
}

// 3. Header einbinden (Navbar, <html> etc.)
include __DIR__ . '/partials/header.php';
?>

<?php if ($ad): ?>
  <!-- Dynamische Detailansicht einer echten Anzeige -->
  <section class="row">
    <div class="col-md-7">
      <div class="border bg-white rounded p-3 mb-3 mb-md-0">
        <?php if (!empty($ad['image'])): ?>
          <img
            src="uploads/<?php echo htmlspecialchars($ad['image']); ?>"
            class="img-fluid rounded"
            alt="Bild zu <?php echo htmlspecialchars($ad['title'] ?? 'Anzeige'); ?>"
          >
        <?php else: ?>
          <div class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center rounded">
            <span class="text-muted">Kein Bild vorhanden</span>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="col-md-5">
      <h1 class="h3 mb-2">
        <?php echo htmlspecialchars($ad['title'] ?? 'Anzeige'); ?>
      </h1>

      <?php if (!empty($ad['price'])): ?>
        <p class="fw-bold fs-4 mb-3">
          <?php echo htmlspecialchars($ad['price']); ?> €
        </p>
      <?php endif; ?>

      <?php if (!empty($ad['description'])): ?>
        <p class="mb-3">
          <?php echo nl2br(htmlspecialchars($ad['description'])); ?>
        </p>
      <?php endif; ?>

      <div class="mb-3">
        <h2 class="h6">Kontakt</h2>
        <?php if (!empty($ad['email'])): ?>
          <p class="mb-1">
            <strong>E-Mail:</strong>
            <a href="mailto:<?php echo htmlspecialchars($ad['email']); ?>">
              <?php echo htmlspecialchars($ad['email']); ?>
            </a>
          </p>
        <?php endif; ?>
        <?php if (!empty($ad['phone'])): ?>
          <p class="mb-3">
            <strong>Telefon:</strong>
            <?php echo htmlspecialchars($ad['phone']); ?>
          </p>
        <?php endif; ?>
      </div>

      <a href="dashboard.php" class="btn btn-outline-secondary btn-sm me-2">
        ← Zurück zum Dashboard
      </a>
      <a href="index.php" class="btn btn-link btn-sm">
        Zur Startseite
      </a>
    </div>
  </section>

<?php else: ?>
  <!-- Fallback, wenn keine passende Anzeige gefunden wurde -->
  <section class="row">
    <div class="col-md-7">
      <div class="border bg-white rounded p-3 mb-3 mb-md-0">
        <div class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center rounded">
          <span class="text-muted">Platz für Produktbild</span>
        </div>
      </div>
    </div>

    <div class="col-md-5">
      <h1 class="h3 mb-2">Beispielanzeige</h1>
      <p class="text-muted small mb-1">Keine gültige Anzeige gefunden.</p>
      <p class="fw-bold fs-4 mb-3">–</p>

      <p class="mb-3">
        Diese Detailseite wird für echte Anzeigen verwendet, die über das Formular
        erstellt wurden. Bitte kehre zur Übersicht zurück und wähle eine gültige Anzeige.
      </p>

      <a href="index.php" class="btn btn-outline-secondary btn-sm">
        ← Zurück zur Startseite
      </a>
    </div>
  </section>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
