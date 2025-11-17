<?php
// delete_ad.php

// Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nur eingeloggte Benutzer dürfen hier her
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Wenn Formular schon bestätigt wurde → wirklich löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id !== null && isset($_SESSION['ads']) && is_array($_SESSION['ads'])) {
        foreach ($_SESSION['ads'] as $index => $ad) {
            if (!empty($ad['id']) && $ad['id'] === $id) {
                unset($_SESSION['ads'][$index]);
                $_SESSION['ads'] = array_values($_SESSION['ads']); // Indizes neu ordnen
                break;
            }
        }
    }

    // Zurück zum Dashboard
    header('Location: dashboard.php');
    exit;
}

// Wenn wir hier sind: Seite wurde per GET aufgerufen → Bestätigungsfenster anzeigen
$id = $_GET['id'] ?? null;
$adTitle = '';

if ($id !== null && isset($_SESSION['ads']) && is_array($_SESSION['ads'])) {
    foreach ($_SESSION['ads'] as $ad) {
        if (!empty($ad['id']) && $ad['id'] === $id) {
            $adTitle = $ad['title'] ?? '';
            break;
        }
    }
}

include __DIR__ . '/partials/header.php';
?>

<h1 class="h4 mb-4">Anzeige löschen</h1>

<div class="d-flex justify-content-center">
  <div class="bg-light border rounded p-3" style="max-width: 380px; width: 100%;">
    <p class="mb-2">Willst du diese Anzeige wirklich löschen?</p>

    <?php if ($adTitle !== ''): ?>
      <p class="fw-bold small mb-3">
        <?php echo htmlspecialchars($adTitle); ?>
      </p>
    <?php endif; ?>

    <form method="post" class="d-flex gap-2">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($id ?? ''); ?>">

      <button type="submit" class="btn btn-danger btn-sm">
        Ja, löschen
      </button>

      <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
        Abbrechen
      </a>
    </form>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
