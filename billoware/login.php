<?php
// login.php

// 1. Sessions & Userdaten einbinden, bevor irgendwas ausgegeben wird
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/data/users.php';

$error = '';

// 2. Wenn Formular abgeschickt wurde, Login prüfen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $foundUser = null;

    foreach ($users as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            $foundUser = $user;
            break;
        }
    }

    if ($foundUser) {
        // 3. User in Session speichern
        $_SESSION['user'] = [
            'email' => $foundUser['email'],
            'role'  => $foundUser['role'],
        ];

        // 4. Nach erfolgreichem Login weiterleiten
        header('Location: index.php');
        exit;
    } else {
        $error = 'E-Mail oder Passwort ist falsch.';
    }
}

// 5. Ab hier HTML-Ausgabe mit Header
include __DIR__ . '/partials/header.php';
?>

<h1 class="mb-4">Login</h1>

<div class="row justify-content-center">
  <div class="col-md-6 col-lg-5">
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <div class="card">
      <div class="card-body">
        <form method="post" novalidate>
          <div class="mb-3">
            <label for="email" class="form-label">E-Mail</label>
            <input
              type="email"
              class="form-control"
              id="email"
              name="email"
              required
              value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
            >
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Passwort</label>
            <input
              type="password"
              class="form-control"
              id="password"
              name="password"
              required
            >
          </div>

          <button type="submit" class="btn btn-primary w-100">
            Einloggen
          </button>
        </form>
      </div>
    </div>

    <p class="mt-3 small text-muted">
      Test-User: <br>
      user@billoware.at / user123 <br>
      admin@billoware.at / admin123
    </p>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
