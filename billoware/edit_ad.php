<?php
// edit_ad.php

// 1. Session & Zugriff prüfen
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$isAdmin     = ($currentUser['role'] ?? '') === 'admin';
$userEmail   = $currentUser['email'] ?? '';

$id = $_GET['id'] ?? ($_POST['id'] ?? null);

if (!$id || empty($_SESSION['ads']) || !is_array($_SESSION['ads'])) {
    header('Location: dashboard.php');
    exit;
}

// 2. Anzeige in der Session finden
$adIndex = null;
foreach ($_SESSION['ads'] as $index => $candidate) {
    if (($candidate['id'] ?? null) === $id) {
        $adIndex = $index;
        break;
    }
}

if ($adIndex === null) {
    header('Location: dashboard.php');
    exit;
}

$ad = $_SESSION['ads'][$adIndex];

// Nur Besitzer oder Admin darf bearbeiten
$isOwner = ($ad['owner'] ?? '') === $userEmail;
if (!$isAdmin && !$isOwner) {
    header('Location: dashboard.php');
    exit;
}

// 3. Formular-Variablen & Fehler
$errors = [];
$success = false;

// Startwerte aus der Anzeige
$title        = $ad['title']       ?? '';
$description  = $ad['description'] ?? '';
$price        = $ad['price']       ?? '';
$contactEmail = $ad['email']       ?? $userEmail;
$contactPhone = $ad['phone']       ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $price        = trim($_POST['price'] ?? '');
    $contactEmail = trim($_POST['email'] ?? '');
    $contactPhone = trim($_POST['phone'] ?? '');

    if ($title === '') {
        $errors[] = 'Bitte gib einen Titel ein.';
    }
    if ($description === '') {
        $errors[] = 'Bitte gib eine Beschreibung ein.';
    }
    if ($contactEmail === '') {
        $errors[] = 'Bitte gib eine Kontakt-E-Mail an.';
    }

    if (empty($errors)) {
        // 4. Anzeige in der Session aktualisieren
        $_SESSION['ads'][$adIndex]['title']       = $title;
        $_SESSION['ads'][$adIndex]['description'] = $description;
        $_SESSION['ads'][$adIndex]['price']       = $price;
        $_SESSION['ads'][$adIndex]['email']       = $contactEmail;
        $_SESSION['ads'][$adIndex]['phone']       = $contactPhone;

        // Aktuelles $ad-Array für die Anzeige aktualisieren
        $ad = $_SESSION['ads'][$adIndex];

        $success = true;
    }
}

// 5. HTML ausgeben
include __DIR__ . '/partials/header.php';
?>

<h1 class="mb-4">Anzeige bearbeiten</h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?>
        <li><?php echo htmlspecialchars($e); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="alert alert-success">
    Die Anzeige wurde erfolgreich aktualisiert.
    <a href="dashboard.php" class="alert-link">Zurück zum Dashboard</a>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-8 col-xl-7">
    <div class="card">
      <div class="card-body">
        <form method="post">
          <!-- versteckte ID, damit wir sie beim POST haben -->
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

          <div class="mb-3">
            <label for="title" class="form-label">Titel *</label>
            <input
              type="text"
              class="form-control"
              id="title"
              name="title"
              required
              value="<?php echo htmlspecialchars($title); ?>"
            >
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Beschreibung *</label>
            <textarea
              class="form-control"
              id="description"
              name="description"
              rows="4"
              required
            ><?php echo htmlspecialchars($description); ?></textarea>
          </div>

          <div class="mb-3">
            <label for="price" class="form-label">Preis (in €)</label>
            <input
              type="number"
              step="0.01"
              class="form-control"
              id="price"
              name="price"
              value="<?php echo htmlspecialchars($price); ?>"
            >
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Kontakt-E-Mail *</label>
              <input
                type="email"
                class="form-control"
                id="email"
                name="email"
                required
                value="<?php echo htmlspecialchars($contactEmail); ?>"
              >
            </div>
            <div class="col-md-6 mb-3">
              <label for="phone" class="form-label">Kontakt-Telefon</label>
              <input
                type="text"
                class="form-control"
                id="phone"
                name="phone"
                value="<?php echo htmlspecialchars($contactPhone); ?>"
              >
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            Änderungen speichern
          </button>
          <a href="dashboard.php" class="btn btn-outline-secondary ms-2">
            Abbrechen
          </a>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
