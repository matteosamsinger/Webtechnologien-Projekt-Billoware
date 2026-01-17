<?php
// Session starten + Flash-Funktionen verfügbar machen
require_once __DIR__ . '/../utils/session.php';

// Login-Status für Navbar
$isLoggedIn = isset($_SESSION['user']);
$isAdmin   = $isLoggedIn && (($_SESSION['user']['role'] ?? '') === 'admin');

// Flash einmalig holen (und dabei aus Session entfernen)
$flash = flash_get();
?>

<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Billoware</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap nur als CSS-Framework -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet">

    <!-- eigenes CSS -->
    <link rel="stylesheet" href="/billoware/assets/css/styles.css">
</head>
<body>

<!-- einfache Navbar ohne Burger, ohne JS -->
<nav class="navbar navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="/billoware/pages/index.php">Billoware</a>

    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link text-white" href="/billoware/pages/index.php">Startseite</a>
      </li>

      <?php if ($isLoggedIn): ?>
        <li class="nav-item">
          <a class="nav-link text-white" href="/billoware/pages/dashboard.php">Dashboard</a>
        </li>
        <?php if ($isAdmin): ?>
          <li class="nav-item">
            <a class="nav-link text-white" href="/billoware/pages/admin_panel.php">Admin</a>
          </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link text-white" href="/billoware/pages/logout.php">Logout</a>
        </li>
      <?php else: ?>
        <li class="nav-item">
          <a class="nav-link text-white" href="/billoware/pages/login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white" href="/billoware/pages/register.php">Registrieren</a>
        </li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<main class="container py-4">

<?php if ($flash): ?>
  <div class="alert alert-<?php echo htmlspecialchars($flash['type']); ?>">
    <?php echo htmlspecialchars($flash['message']); ?>
  </div>
<?php endif; ?>
