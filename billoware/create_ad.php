<?php
// create_ad.php

// 1. Session & Zugriffsschutz (nur normale User)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'user') {
    header('Location: login.php');
    exit;
}

$userEmail = $_SESSION['user']['email'] ?? '';

// 2. Variablen für Formular & Feedback
$errors = [];
$success = false;

$title         = '';
$description   = '';
$price         = '';
$contactEmail  = $userEmail; // Standard: eigene E-Mail
$contactPhone  = '';
$imageFileName = null;

// 3. Wurde das Formular abgeschickt?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Werte einsammeln
    $title        = trim($_POST['title'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $price        = trim($_POST['price'] ?? '');
    $contactEmail = trim($_POST['email'] ?? $userEmail);
    $contactPhone = trim($_POST['phone'] ?? '');

    // Einfache Validierung
    if ($title === '') {
        $errors[] = 'Bitte gib einen Titel ein.';
    }
    if ($description === '') {
        $errors[] = 'Bitte gib eine Beschreibung ein.';
    }
    if ($contactEmail === '') {
        $errors[] = 'Bitte gib eine Kontakt-E-Mail an.';
    }

    // 4. Bild-Upload (optional)
    $imageFileName = null;

    if (!empty($_FILES['image']['name'])) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $tmpName  = $_FILES['image']['tmp_name'];
            $origName = $_FILES['image']['name'];

            // Dateiendung prüfen (sehr simpel)
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Nur JPG, PNG oder WEBP sind als Bild erlaubt.';
            } else {
                // Eindeutiger Dateiname
                $imageFileName = uniqid('ad_', true) . '.' . $ext;

                // Upload-Verzeichnis (muss existieren: /uploads)
                $targetDir = __DIR__ . '/uploads/';
                if (!is_dir($targetDir)) {
                    // Versuchen anzulegen – wenn das nicht klappt, Meldung
                    if (!mkdir($targetDir, 0777, true)) {
                        $errors[] = 'Upload-Ordner konnte nicht erstellt werden.';
                    }
                }

                if (empty($errors)) {
                    $targetPath = $targetDir . $imageFileName;
                    if (!move_uploaded_file($tmpName, $targetPath)) {
                        $errors[] = 'Fehler beim Speichern des Bildes.';
                        $imageFileName = null;
                    }
                }
            }
        } else {
            $errors[] = 'Fehler beim Hochladen des Bildes.';
        }
    }

    // 5. Wenn keine Fehler, Anzeige in Session speichern
    if (empty($errors)) {
        if (!isset($_SESSION['ads']) || !is_array($_SESSION['ads'])) {
            $_SESSION['ads'] = [];
        }

        $newAd = [
            'id'          => uniqid('ad_', true),
            'title'       => $title,
            'description' => $description,
            'price'       => $price,
            'email'       => $contactEmail,
            'phone'       => $contactPhone,
            'image'       => $imageFileName,
            'owner'       => $userEmail,
        ];

        $_SESSION['ads'][] = $newAd;

        $success = true;

        // Formularfelder für nächste Eingabe leeren
        $title        = '';
        $description  = '';
        $price        = '';
        $contactEmail = $userEmail;
        $contactPhone = '';
        $imageFileName = null;
    }
}

// 6. Jetzt HTML mit Header ausgeben
include __DIR__ . '/partials/header.php';
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

<?php if ($success): ?>
  <div class="alert alert-success">
    Deine Anzeige wurde erfolgreich erstellt.
    <a href="dashboard.php" class="alert-link">Zum Dashboard</a>
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-lg-8 col-xl-7">
    <div class="card">
      <div class="card-body">
        <form method="post" enctype="multipart/form-data">
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

          <div class="mb-3">
            <label for="image" class="form-label">Bild (optional)</label>
            <input
              type="file"
              class="form-control"
              id="image"
              name="image"
              accept=".jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">
              Erlaubte Formate: JPG, PNG, WEBP.
            </div>
          </div>

          <button type="submit" class="btn btn-primary">
            Anzeige erstellen
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
