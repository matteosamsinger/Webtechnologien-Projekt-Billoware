<?php include __DIR__ . '/partials/header.php'; ?>

<!-- Hero-Bereich oben -->
<section class="mb-5">
  <div class="row align-items-center">
    <div class="col-lg-7">
      <h1 class="fw-bold mb-3">Billoware – Kleinanzeigen für Studierende</h1>
      <p class="lead text-muted">
        Verkaufe, verschenke oder finde gebrauchte Dinge rund ums Studierendenleben –
        schnell und unkompliziert.
      </p>
    </div>
  </div>
</section>

<!-- Suchfeld -->
<section class="mb-4">
  <form class="row g-2">
    <div class="col-sm-8 col-md-6">
      <input
        type="text"
        class="form-control"
        placeholder="Nach Artikeln suchen … (z.B. Schreibtisch, Fahrrad, Laptop)"
        name="q"
      >
    </div>
    <div class="col-sm-4 col-md-2">
      <button class="btn btn-primary w-100" type="submit">Suchen</button>
    </div>
  </form>
</section>

<!-- Anzeigen-Gitter -->
<section id="ads">
  <h2 class="h4 mb-3">Aktuelle Anzeigen (Beispiele)</h2>

  <div class="row g-4">

    <!-- Anzeige 1 -->
    <div class="col-md-4">
      <div class="card h-100">
        <!-- Wenn du noch kein Bild hast, lass das img erstmal weg oder nimm ein Platzhalterbild -->
        <!-- <img src="assets/img/example1.jpg" class="card-img-top" alt="Beispielartikel"> -->
        <div class="card-body">
          <h5 class="card-title">IKEA Schreibtisch</h5>
          <p class="card-text small text-muted mb-2">
            Weiß, 120×60 cm, ideal fürs Home-Office oder Studium. Leichte Gebrauchsspuren.
          </p>
          <p class="fw-bold mb-3">40 €</p>
          <a href="ad_detail.php" class="btn btn-sm btn-primary mt-auto">Details ansehen</a>
        </div>
      </div>
    </div>

    <!-- Anzeige 2 -->
    <div class="col-md-4">
      <div class="card h-100">
        <!-- <img src="assets/img/fahrrad.jpg" class="card-img-top" alt="Fahrrad"> -->
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">Citybike</h5>
          <p class="card-text small text-muted mb-2">
            Herrenrad, 28 Zoll, fahrbereit. Perfekt für den Weg zur FH.
          </p>
          <p class="fw-bold mb-3">90 €</p>
          <a href="ad_detail.php" class="btn btn-sm btn-primary mt-auto">Details ansehen</a>
        </div>
      </div>
    </div>

    <!-- Anzeige 3 -->
    <div class="col-md-4">
      <div class="card h-100">
        <!-- <img src="assets/img/monitor.jpg" class="card-img-top" alt="Monitor"> -->
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">24" Monitor</h5>
          <p class="card-text small text-muted mb-2">
            Full HD, HDMI-Eingang, gut für Programmieren und Gaming.
          </p>
          <p class="fw-bold mb-3">60 €</p>
          <a href="ad_detail.php" class="btn btn-sm btn-primary mt-auto">Details ansehen</a>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>
