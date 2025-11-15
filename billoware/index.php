<?php include __DIR__ . '/partials/header.php'; ?>

<h1 class="mb-4">Willkommen bei Billoware</h1>
<p class="lead">
  Die Kleinanzeigenplattform für Studierende – kaufen, verkaufen, verschenken.
</p>

<section id="ads" class="mt-4">
  <h2 class="h4 mb-3">Beispielartikel</h2>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card h-100">
        <!-- Wenn du noch kein Bild hast, lass das img erstmal weg oder nimm ein Platzhalterbild -->
        <!-- <img src="assets/img/example1.jpg" class="card-img-top" alt="Beispielartikel"> -->
        <div class="card-body">
          <h5 class="card-title">IKEA Schreibtisch</h5>
          <p class="card-text small text-muted">
            Leicht gebraucht, ideal für Studentenwohnung.
          </p>
          <p class="fw-bold mb-2">40 €</p>
          <a href="ad_detail.php" class="btn btn-sm btn-primary">Details ansehen</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
