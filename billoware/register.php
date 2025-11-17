<?php
// register.php

// 1. Session & Userliste laden
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/data/users.php';

$errors = [];
$success = false;

$name            = '';
$email           = '';
$password        = '';
$passwordRepeat  = '';

// Bereits registrierte (Session-)User laden
$registeredUsers = $_SESSION['registered_users'] ?? [];

// 2. Formular abgeschickt?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name           = trim($_POST['name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $password       = $_POST['password'] ?? '';
    $passwordRepeat = $_POST['password_repeat'] ?? '';

    // Einfache Validierung
    if ($name === '') {
        $errors[] = 'Bitte gib einen Namen ein.';
    }

    if ($email === '') {
        $errors[] = 'Bitte gib eine E-Mail-Adresse ein.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Die E-Mail-Adresse ist nicht gültig.';
    }

    if ($password === '') {
        $errors[] = 'Bitte gib ein Passwort ein.';
    } elseif (strlen($password) < 4) {
        $errors[] = 'Das Passwort muss mindestens 4 Zeichen lang sein.';
    }

    if ($passwordRepeat === '') {
        $errors[] = 'Bitte wiederhole das Passwort.';
    } elseif ($password !== $passwordRepeat) {
        $errors[] = 'Die Passwörter stimmen nicht überein.';
    }

    // Prüfen, ob E-Mail schon existiert (entweder in data/users.php oder in Session)
    $allUsers = array_merge($users, $registeredUsers);
    foreach ($allUsers as $u) {
        if (($u['email'] ?? '') === $email) {
            $errors[] = 'Für diese E-Mail-Adresse existiert bereits ein Konto.';
            break;
        }
    }

    // Wenn keine Fehler: neuen User in Session speichern
    if (empty($errors)) {
        $newUser = [
            'name'     => $name,
            'email'    => $email,
            'password' => $password, // später: password_hash(...)
            'role'     => 'user',
        ];

        $registeredUsers[] = $newUser;
        $_SESSION['registered_users'] = $registeredUsers;

        $success = true;

        // Felder leeren
        $name           = '';
        $email          = '';
        $password       = '';
        $passwordRepeat = '';
    }
}

// 3. HTML-Ausgabe
include __DIR__ . '/partials/header.php';
?>

<h1 class="mb-4">Registrieren</h1>

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
    Dein Konto wurde erfolgreich erstellt. Du kannst dich jetzt
    <a href="login.php" class="alert-link">einloggen</a>.
  </div>
<?php endif; ?>

<div class="row">
  <div class="col-md-6 col-lg-5">
    <div class="card">
      <div class="card-body">
        <form method="post" novalidate>
          <div class="mb-3">
            <label for="name" class="form-label">Name *</label>
            <input
              type="text"
              class="form-control"
              id="name"
              name="name"
              required
              value="<?php echo htmlspecialchars($name); ?>"
            >
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">E-Mail *</label>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              required
              value="<?php echo htmlspecialchars($email); ?>"
            >
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Passwort *</label>
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              required
            >
          </div>

          <div class="mb-3">
            <label for="password_repeat" class="form-label">Passwort wiederholen *</label>
            <input
              type="password"
              class="form-control"
              id="password_repeat"
              name="password_repeat"
              required
            >
          </div>

          <button type="submit" class="btn btn-primary w-100">
            Registrieren
          </button>
        </form>
      </div>
    </div>

    <p class="mt-3 small text-muted">
      Du hast schon ein Konto?
      <a href="login.php">Zum Login</a>
    </p>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
