<?php
// delete_ad.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nur eingeloggte User/Admins
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$isAdmin     = ($currentUser['role'] ?? '') === 'admin';
$userEmail   = $currentUser['email'] ?? '';

// id kann von GET (erster Aufruf) oder von POST (Bestätigung) kommen
$id = $_GET['id'] ?? ($_POST['id'] ?? null);

if (!$id || empty($_SESSION['ads']) || !is_array($_SESSION['ads'])) {
    // Wenn keine gültige id → zurück
    header('Location: ' . ($isAdmin ? 'admin_panel.php' : 'dashboard.php'));
    exit;
}

// passende Anzeige in Session finden
$adIndex = null;
foreach ($_SESSION['ads'] as $index => $adCandidate) {
    if (($adCandidate['id'] ?? null) === $id) {
        $adIndex = $index;
        break;
    }
}

if ($adIndex === null) {
    // Keine Anzeige gefunden
    header('Location: ' . ($isAdmin ? 'admin_panel.php' : 'dashboard.php'));
    exit;
}

$ad = $_SESSION['ads'][$adIndex];

// Darf nur löschen, wenn Besitzer oder Admin
$isOwner = ($ad['owner'] ?? '') === $userEmail;
if (!$isAdmin && !$isOwner) {
    header('Location: dashboard.php');
    exit;
}

// Wenn Formular bestätigt wurde → wirklich löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['confirm'] ?? '') === 'yes') {
    unset($_SESSION['ads'][$adIndex]);
    $_SESSION['ads'] = array_values($_SESSION['ads']); // Indizes neu ordnen

    header('Location: ' . ($isAdmin ? 'admin_panel.php' : 'dashboard.php'));
    exit;
}

// Bis hierher: Erster Aufruf → Bestätigungsseite anzeigen
include __DIR__ . '/partials/header.php';
?>

<h1 class="mb-4">Anzeige löschen</h1>

<div class="alert alert-warning">
  Bist du sicher, dass du diese Anzeige löschen möchtest?
</div>

<div class="card mb-3">
  <div class="card-body">
    <h5 class="card-title mb-1">
      <?php echo htmlspecialchars($ad['title'] ?? 'Anzeige'); ?>
    </h5>
    <?php if (!empty($ad['price'])): ?>
      <p class="fw-bold mb-2"><?php echo htmlspecialchars($ad['price']); ?> €</p>
    <?php endif; ?>
    <?php if (!empty($ad['description'])): ?>
      <p class="card-text small text-muted mb-0">
        <?php
        $short = mb_strimwidth($ad['description'], 0, 160, '…');
        echo nl2br(htmlspecialchars($short));
        ?>
      </p>
    <?php endif; ?>
  </div>
</div>

<form method="post" class="d-flex gap-2">
  <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
  <input type="hidden" name="confirm" value="yes">

  <button type="submit" class="btn btn-danger">
    Ja, Anzeige endgültig löschen
  </button>

  <a href="<?php echo $isAdmin ? 'admin_panel.php' : 'dashboard.php'; ?>"
     class="btn btn-outline-secondary">
    Abbrechen
  </a>
</form>

<?php include __DIR__ . '/partials/footer.php'; ?>

