<?php
// service laden
require_once __DIR__ . '/../services/ad_service.php';

include __DIR__ . '/../partials/header.php';

// Suchbegriff aus URL (?q=...) lesen
$q = trim($_GET['q'] ?? '');

// Anzeigenliste holen: entweder neueste oder gefiltert nach Suchbegriff
$ads = ad_list_latest($q, 30);
?>

<!-- Hero/Einleitung -->
<section class="mb-4">
  <h1 class="fw-bold mb-2">Billoware – Kleinanzeigen</h1>
  <p class="text-muted">Verkaufe, verschenke oder finde gebrauchte Dinge – schnell und unkompliziert.</p>
</section>

<section class="mb-4">
  <form class="row g-2" method="get">
    <div class="col-sm-8 col-md-6">
      <input type="text" class="form-control" name="q" placeholder="Suchen…" value="<?php echo htmlspecialchars($q); ?>">
    </div>
    <div class="col-sm-4 col-md-2">
      <button class="btn btn-primary w-100" type="submit">Suchen</button>
    </div>
  </form>
</section>

<section>
  <h2 class="h4 mb-3"><?php echo $q ? 'Suchergebnisse' : 'Aktuelle Anzeigen'; ?></h2>

  <?php if (empty($ads)): ?>
    <div class="alert alert-secondary">Keine Anzeigen gefunden.</div>
  <?php else: ?>
    <div class="row g-3">
      <?php foreach ($ads as $ad): ?>
        <?php
          $imgs = ad_get_images((int)$ad['id']);
          $first = $imgs[0]['filename'] ?? null;
        ?>
        <div class="col-md-4">
          <div class="card h-100">
            <?php if ($first): ?>
              <img src="../uploads/<?php echo htmlspecialchars($first); ?>" class="card-img-top" alt="">
            <?php endif; ?>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-1"><?php echo htmlspecialchars($ad['title']); ?></h5>
              <p class="small text-muted mb-2"><?php echo nl2br(htmlspecialchars(mb_strimwidth($ad['description'], 0, 120, '…'))); ?></p>
              <p class="fw-bold mb-3"><?php echo htmlspecialchars((string)$ad['price']); ?> €</p>
              <a class="btn btn-sm btn-primary mt-auto" href="/billoware/pages/ad_detail.php?id=<?php echo urlencode((string)$ad['id']); ?>">Details ansehen</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/../partials/footer.php'; ?>
