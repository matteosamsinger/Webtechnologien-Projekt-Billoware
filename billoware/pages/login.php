<?php
// Session/Flash + User-Service (DB Login)
require_once __DIR__ . '/../utils/session.php';
require_once __DIR__ . '/../services/user_service.php';

$error = '';
$email = '';

// Formular abgeschickt?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      // Eingaben lesen
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Login prüfen: user existiert? gesperrt? passwort korrekt?
    $result = user_verify_login($email, $password);

      // OK -> Session-User setzen + Redirect auf Startseite
    if (!empty($result['ok'])) {
        login_user($result['user']);
        header('Location: /billoware/pages/index.php');
        exit;
    }

    // Fehlermeldung setzen (für Alert)
    $error = $result['error'] ?? 'E-Mail oder Passwort ist falsch.';

}

include __DIR__ . '/../partials/header.php';
?>

<!-- Login-Formular (POST auf gleiche Seite) -->
<!-- Wenn $error gesetzt ist, wird ein Bootstrap Alert angezeigt -->


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
      <!--Test-User: <br>
      user@billoware.at / user123 <br>
      admin@billoware.at / admin123-->
    </p>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
