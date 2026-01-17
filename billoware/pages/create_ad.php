<?php
// nur eingeloggte User dürfen erstellen
require_once __DIR__ . '/../utils/check_login.php';

// Upload-Helper + DB-Service
require_once __DIR__ . '/../utils/upload.php';
require_once __DIR__ . '/../services/ad_service.php';

// User-Infos aus Session
$userId = (int)$_SESSION['user']['id'];
$userEmail = (string)($_SESSION['user']['email'] ?? '');

    // Falls Session irgendwie kaputt ist
if ($userId <= 0) {
    header('Location: /billoware/pages/login.php');
    exit;
}

// Formular-State
$errors = [];
$title = $description = $price = '';
$contactEmail = $userEmail;
$contactPhone = '';


// Formular abgeschickt?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      // Werte einlesen
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '0');
    $contactEmail = trim($_POST['email'] ?? $userEmail);
    $contactPhone = trim($_POST['phone'] ?? '');

        // Server-side Validierung
    if ($title === '') $errors[] = 'Bitte gib einen Titel ein.';
    if ($description === '') $errors[] = 'Bitte gib eine Beschreibung ein.';
    if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Bitte gib eine gültige Kontakt-E-Mail an.';

    if ($price === '') $price = '0';
    if (!is_numeric($price)) $errors[] = 'Preis muss eine Zahl sein.';

    // Bild-Upload (max 3)
    $uploadResult = ['files'=>[], 'errors'=>[]];
    if (isset($_FILES['images'])) {
        $uploadResult = upload_images($_FILES['images'], 3);
        $errors = array_merge($errors, $uploadResult['errors']);
    }

       // Wenn alles OK: Anzeige in DB anlegen + Bilder speichern
    if (empty($errors)) {
        $adId = ad_create($userId, [
            'title' => $title,
            'description' => $description,
            'price' => (float)$price,
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
        ]);

        if (!empty($uploadResult['files'])) {
            ad_add_images($adId, $uploadResult['files']);
        }

                // Flash + Redirect (POST->Redirect->GET)
        flash_set('success', 'Deine Anzeige wurde erfolgreich erstellt.');
        header('Location: /billoware/pages/ad_detail.php?id=' . $adId);
        exit;
    }
}

include __DIR__ . '/../partials/header.php';
?>

<h1 class="mb-4">Neue Anzeige erstellen</h1>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?>
        <li><?php echo htmlspecialchars($e); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-8 col-xl-7">
    <div class="card">
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Titel *</label>
            <input class="form-control" name="title" required value="<?php echo htmlspecialchars($title); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Beschreibung *</label>
            <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Preis (in €)</label>
            <input type="number" step="0.01" class="form-control" name="price" value="<?php echo htmlspecialchars($price); ?>">
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Kontakt-E-Mail *</label>
              <input type="email" class="form-control" name="email" required value="<?php echo htmlspecialchars($contactEmail); ?>">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Kontakt-Telefon</label>
              <input class="form-control" name="phone" value="<?php echo htmlspecialchars($contactPhone); ?>">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Bilder (max. 3, JPG/PNG/WEBP)</label>
            <input type="file" class="form-control" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp">
          </div>

          <button class="btn btn-primary" type="submit">Anzeige erstellen</button>
          <a href="/billoware/pages/dashboard.php" class="btn btn-outline-secondary ms-2">Abbrechen</a>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
