<?php
// Login-Guard + Upload helper + DB-Service
require_once __DIR__ . '/../utils/check_login.php';
require_once __DIR__ . '/../utils/upload.php';
require_once __DIR__ . '/../services/ad_service.php';

$userId = (int)$_SESSION['user']['id'];
$isAdmin = (($_SESSION['user']['role'] ?? '') === 'admin');

// Ad-ID aus GET oder POST (POST enthält hidden id)
$adId = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));

// Anzeige laden, sonst zurück
$ad = $adId ? ad_get($adId) : null;
if (!$ad) { header('Location: /billoware/pages/dashboard.php'); exit; }

// Rechte prüfen (Owner/Admin)
if (!ad_user_can_edit($adId, $userId, $isAdmin)) {
    header('Location: /billoware/pages/dashboard.php');
    exit;
}

// Einzelnes Bild löschen (GET delete_image_id)
if (isset($_GET['delete_image_id'])) {
    $imgId = (int)$_GET['delete_image_id'];
    ad_image_delete($imgId);
    flash_set('info', 'Bild wurde entfernt.');
    header('Location: /billoware/pages/edit_ad.php?id=' . $adId);
    exit;
}

// Formularwerte initial mit DB-Inhalt befüllen
$errors = [];
$title = (string)$ad['title'];
$description = (string)$ad['description'];
$price = (string)$ad['price'];
$contactEmail = (string)$ad['contact_email'];
$contactPhone = (string)($ad['contact_phone'] ?? '');

// POST = Änderungen speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      // Eingaben lesen + validieren
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '0');
    $contactEmail = trim($_POST['email'] ?? '');
    $contactPhone = trim($_POST['phone'] ?? '');

    if ($title === '') $errors[] = 'Bitte gib einen Titel ein.';
    if ($description === '') $errors[] = 'Bitte gib eine Beschreibung ein.';
    if ($contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Bitte gib eine gültige Kontakt-E-Mail an.';
    if ($price === '' || !is_numeric($price)) $errors[] = 'Preis muss eine Zahl sein.';


    // Upload: nur so viele Bilder wie noch frei sind (max 3 gesamt)
    $currentCount = ad_count_images($adId);
    $remaining = max(0, 3 - $currentCount);

    $uploadResult = ['files'=>[], 'errors'=>[]];
    if (isset($_FILES['images']) && $remaining > 0) {
        $uploadResult = upload_images($_FILES['images'], $remaining);
        $errors = array_merge($errors, $uploadResult['errors']);
    } elseif (isset($_FILES['images']) && $remaining === 0) {
        // falls trotzdem versucht wurde
        // nur wenn wirklich files ausgewählt wurden:
        $names = $_FILES['images']['name'] ?? [];
        $hasAny = false;
        foreach ((array)$names as $n) { if (!empty($n)) { $hasAny = true; break; } }
        if ($hasAny) $errors[] = 'Es sind bereits 3 Bilder vorhanden. Bitte zuerst ein Bild löschen.';
    }

    
    // Wenn OK: Update + neue Bilder anhängen
    if (empty($errors)) {
        ad_update($adId, [
            'title' => $title,
            'description' => $description,
            'price' => (float)$price,
            'contact_email' => $contactEmail,
            'contact_phone' => $contactPhone,
        ]);

        if (!empty($uploadResult['files'])) {
            // sort_order ist egal, wir hängen einfach an (order wird beim Select nach sort_order/id angezeigt)
            ad_add_images($adId, $uploadResult['files']);
        }

        flash_set('success', 'Anzeige wurde aktualisiert.');
        header('Location: /billoware/pages/ad_detail.php?id=' . $adId);
        exit;
    }
}

// Bilder für Thumbnail-Liste rechts laden
$images = ad_get_images($adId);


include __DIR__ . '/../partials/header.php';
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

<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="id" value="<?php echo htmlspecialchars((string)$adId); ?>">

          <div class="mb-3">
            <label class="form-label">Titel *</label>
            <input class="form-control" name="title" required value="<?php echo htmlspecialchars($title); ?>">
          </div>

          <div class="mb-3">
            <label class="form-label">Beschreibung *</label>
            <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Preis</label>
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
            <label class="form-label">Neue Bilder hinzufügen (bis max. 3 gesamt)</label>
            <input type="file" class="form-control" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text">Wenn schon 3 Bilder vorhanden sind, musst du zuerst eins löschen.</div>
          </div>

          <button class="btn btn-primary" type="submit">Speichern</button>
          <a href="/billoware/pages/ad_detail.php?id=<?php echo urlencode((string)$adId); ?>" class="btn btn-outline-secondary ms-2">Abbrechen</a>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h2 class="h6 mb-3">Bilder</h2>
        <?php if (empty($images)): ?>
          <p class="text-muted small mb-0">Keine Bilder vorhanden.</p>
        <?php else: ?>
          <?php foreach ($images as $img): ?>
            <div class="d-flex align-items-center gap-2 mb-2">
              <img src="../uploads/<?php echo htmlspecialchars($img['filename']); ?>" style="width:64px;height:64px;object-fit:cover" class="rounded border" alt="">
              <a class="btn btn-sm btn-outline-danger"
                 href="/billoware/pages/edit_ad.php?id=<?php echo urlencode((string)$adId); ?>&delete_image_id=<?php echo urlencode((string)$img['id']); ?>"
                 onclick="return confirm('Bild wirklich löschen?');">
                Entfernen
              </a>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
