<?php
declare(strict_types=1);
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/helpers.php";
require_once __DIR__ . "/../includes/auth.php";

$pageTitle = "Ourflix — Kenangan Kita 💘";
$pdo = db();

$limit = 24;
$offset = 0;

$stmt = $pdo->prepare("SELECT p.id,p.title,p.caption,p.row_name,p.tags,p.file_path,p.created_at, u.username AS uploader FROM photos p LEFT JOIN users u ON u.id=p.created_by ORDER BY p.created_at DESC, p.id DESC LIMIT :lim OFFSET :off");
$stmt->bindValue(":lim", $limit, PDO::PARAM_INT);
$stmt->bindValue(":off", $offset, PDO::PARAM_INT);
$stmt->execute();
$photos = $stmt->fetchAll();

// group rows
$groups = [];
foreach ($photos as $p) {
  $row = $p["row_name"] ?: "Moments";
  $groups[$row] ??= [];
  $groups[$row][] = $p;
}
// Top Picks first
uksort($groups, function($a,$b){
  if ($a === "Top Picks") return -1;
  if ($b === "Top Picks") return 1;
  return strcasecmp($a,$b);
});

include __DIR__ . "/../includes/layout_top.php";
?>

<section class="hero" id="hero">
  <div class="hero-inner">
    <div class="hero-tags">
      <span class="tag">💘 Couple Originals</span>
      <span class="tag ghost" id="heroTag2">Romantis</span>
  <div class="hero-fade"></div>
</section>

<main class="container" id="rows"
      data-api="<?= h(base_url("/api/photos.php")) ?>"
      data-limit="<?= (int)$limit ?>"
      data-offset="<?= (int)($offset + $limit) ?>">
  <?php if (count($photos) === 0): ?>
    <div class="empty">
      <h3 class="empty-title">Belum ada foto 🥺</h3>
      <p class="empty-sub">Login sebagai admin untuk upload foto pertama.</p>
    </div>
  <?php endif; ?>

  <?php foreach ($groups as $rowName => $items): ?>
    <section class="row" data-row="<?= h($rowName) ?>">
      <div class="row-head">
        <h2 class="row-title"><?= h($rowName) ?></h2>
        <div class="row-meta"><?= count($items) ?> item</div>
      </div>
      <div class="cards" aria-label="<?= h($rowName) ?>" data-row-cards="<?= h($rowName) ?>">
        <?php foreach ($items as $p): ?>
          <a class="card fade-up"
             href="<?= h(base_url("/photo.php?id=" . (int)$p["id"])) ?>"
             data-src="<?= h(base_url("/" . $p["file_path"])) ?>"
             data-title="<?= h($p["title"]) ?>"
             data-caption="<?= h($p["caption"] ?? "") ?>"
             data-tags="<?= h($p["tags"] ?? "") ?>">
            <img loading="lazy" src="<?= h(base_url("/" . $p["file_path"])) ?>" alt="<?= h($p["title"]) ?>"/>
            <div class="badge"><?= h(pretty_dt($p["created_at"])) ?></div>
            <div class="grad"></div>
            <div class="cap"><?= h($p["title"]) ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endforeach; ?>

  <!-- Infinite scroll sentinel -->
  <div class="infinite">
    <div class="spinner" id="spinner" aria-hidden="true"></div>
    <div class="muted" id="loadText">Memuat lagi…</div>
  </div>
</main>

<?php include __DIR__ . "/../includes/layout_bottom.php"; ?>
