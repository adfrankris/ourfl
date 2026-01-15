<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

$activeNav = "dashboard";
$pageTitle = "Dashboard";
$pdo = db();

$photoCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM photos")->fetch()["c"];
$commentCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM comments")->fetch()["c"];
$adminCount = (int)$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()["c"];

$recentPhotos = $pdo->query("SELECT id,title,file_path,created_at FROM photos ORDER BY created_at DESC, id DESC LIMIT 5")->fetchAll();
$recentComments = $pdo->query("SELECT c.id,c.photo_id,c.body,c.created_at FROM comments c ORDER BY c.created_at DESC, c.id DESC LIMIT 5")->fetchAll();

include __DIR__ . "/../../includes/admin_layout_top.php";
?>

<div class="grid cols-3">
  <section class="cardx">
    <h2 class="h">Quick stats</h2>
    <div class="kpis">
      <div class="kpi"><div class="k">Photos</div><div class="v"><?= $photoCount ?></div></div>
      <div class="kpi"><div class="k">Comments</div><div class="v"><?= $commentCount ?></div></div>
      <div class="kpi"><div class="k">Admins</div><div class="v"><?= $adminCount ?></div></div>
    </div>
    <div class="muted" style="margin-top:10px;font-size:12px">
      Kelola foto ada di menu <b>Photos</b>. Kelola akun admin ada di menu <b>Admins</b>.
    </div>
  </section>

  <section class="cardx" style="grid-column: span 2;">
    <h2 class="h">Recent photos</h2>
    <?php if (count($recentPhotos) === 0): ?>
      <div class="muted">Belum ada foto.</div>
    <?php else: ?>
      <div class="admin-list">
        <?php foreach ($recentPhotos as $p): ?>
          <a class="admin-item" href="<?= h(base_url("/admin/edit_photo.php?id=" . (int)$p["id"])) ?>" style="text-decoration:none">
            <img class="thumb" src="<?= h(base_url("/" . $p["file_path"])) ?>" alt="">
            <div class="admin-item-info">
              <div class="admin-item-title"><?= h($p["title"]) ?></div>
              <div class="admin-item-sub muted"><?= h(pretty_dt($p["created_at"])) ?></div>
            </div>
            <div class="pillbtn ghost">Edit</div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>

<div class="grid" style="margin-top:14px">
  <section class="cardx">
    <h2 class="h">Recent comments</h2>
    <?php if (count($recentComments) === 0): ?>
      <div class="muted">Belum ada komentar.</div>
    <?php else: ?>
      <div class="admin-list">
        <?php foreach ($recentComments as $c): ?>
          <a class="admin-item" href="<?= h(base_url("/photo.php?id=" . (int)$c["photo_id"])) ?>" style="text-decoration:none">
            <div class="admin-item-info">
              <div class="admin-item-title">Photo #<?= (int)$c["photo_id"] ?></div>
              <div class="admin-item-sub muted"><?= h(pretty_dt($c["created_at"])) ?></div>
              <div class="muted" style="font-size:12px; margin-top:6px">
                <?= h(mb_substr((string)$c["body"], 0, 120)) ?><?= (mb_strlen((string)$c["body"])>120?'…':'') ?>
              </div>
            </div>
            <div class="pillbtn ghost">View</div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php include __DIR__ . "/../../includes/admin_layout_bottom.php"; ?>
