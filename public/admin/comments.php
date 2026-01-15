<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

$activeNav = "comments";
$pageTitle = "Comments";
$pdo = db();
$csrf = csrf_token();

$comments = $pdo->query("
  SELECT c.id, c.body, c.created_at, c.photo_id,
         p.title AS photo_title
  FROM comments c
  LEFT JOIN photos p ON p.id = c.photo_id
  ORDER BY c.created_at DESC, c.id DESC
")->fetchAll();

include __DIR__ . "/../../includes/admin_layout_top.php";
?>

<div class="grid">
  <section class="cardx">
    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
      <h2 class="h" style="margin:0">All comments</h2>
      <div class="muted" style="font-size:12px"><?= count($comments) ?> comments</div>
    </div>

    <?php if (count($comments) === 0): ?>
      <div class="muted">Belum ada komentar.</div>
    <?php endif; ?>

    <div class="admin-list" style="margin-top:10px">
      <?php foreach ($comments as $c): ?>
        <div class="admin-item" style="align-items:flex-start">
          <div class="admin-item-info" style="max-width:720px">
            <div class="admin-item-title">
              <?= h(mb_substr($c["body"], 0, 80)) ?><?= mb_strlen($c["body"])>80?'…':'' ?>
            </div>
            <div class="admin-item-sub muted">
              Photo: <?= h($c["photo_title"] ?? "Deleted photo") ?> •
              <?= h(pretty_dt($c["created_at"])) ?>
            </div>

            <form class="form" method="post" action="<?= h(base_url("/admin/update_comment.php")) ?>" style="margin-top:8px">
              <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
              <input type="hidden" name="id" value="<?= (int)$c["id"] ?>">
              <textarea class="form-input" name="body" rows="2" required><?= h($c["body"]) ?></textarea>
              <div style="display:flex; gap:8px; margin-top:8px">
                <button class="btn ghost" type="submit">Update</button>
                <a class="pillbtn ghost" href="<?= h(base_url("/photo.php?id=".(int)$c["photo_id"])) ?>">View photo</a>
              </div>
            </form>
          </div>

          <form method="post" action="<?= h(base_url("/admin/delete_comment.php")) ?>" onsubmit="return confirm('Hapus komentar ini?');">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
            <input type="hidden" name="comment_id" value="<?= (int)$c["id"] ?>">
            <button class="iconbtn" type="submit" title="Hapus">🗑️</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<?php include __DIR__ . "/../../includes/admin_layout_bottom.php"; ?>
