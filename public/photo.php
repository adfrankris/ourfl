<?php
declare(strict_types=1);
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/helpers.php";
require_once __DIR__ . "/../includes/auth.php";

$pdo = db();
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) { header("Location: " . base_url("/")); exit; }

$stmt = $pdo->prepare("SELECT p.*, u.username AS uploader FROM photos p LEFT JOIN users u ON u.id=p.created_by WHERE p.id = :id");
$stmt->execute([":id" => $id]);
$photo = $stmt->fetch();
if (!$photo) { header("Location: " . base_url("/")); exit; }

$cstmt = $pdo->prepare("SELECT c.*, u.username AS user_name FROM comments c LEFT JOIN users u ON u.id=c.user_id WHERE c.photo_id = :pid ORDER BY c.created_at ASC, c.id ASC");
$cstmt->execute([":pid" => $id]);
$comments = $cstmt->fetchAll();

$pageTitle = $photo["title"] . " — Ourflix";
$csrf = csrf_token();
$u = current_user();

include __DIR__ . "/../includes/layout_top.php";
?>

<main class="container">
  <div class="detail">
    <div class="detail-media">
      <img src="<?= h(base_url("/" . $photo["file_path"])) ?>" alt="<?= h($photo["title"]) ?>">
    </div>
    <div class="detail-info">
      <div class="detail-head">
        <h1 class="detail-title"><?= h($photo["title"]) ?></h1>
        <a class="pillbtn ghost" href="<?= h(base_url("/")) ?>">← Kembali</a>
      </div>
      <p class="detail-caption"><?= h($photo["caption"] ?? "") ?></p>
      <div class="detail-meta">
        <span class="chip"><?= h(pretty_dt($photo["created_at"])) ?></span>
        <?php if (!empty($photo["row_name"])): ?><span class="chip"><?= h($photo["row_name"]) ?></span><?php endif; ?>
        <?php if (!empty($photo["tags"])): ?>
          <?php foreach (array_filter(array_map('trim', explode(',', (string)$photo["tags"]))) as $t): ?>
            <span class="chip"><?= h($t) ?></span>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div class="comment-box">
        <h2 class="sec-title">Komentar 💬</h2>

        <form class="form" method="post" action="<?= h(base_url("/comment_post.php")) ?>">
          <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
          <input type="hidden" name="photo_id" value="<?= (int)$photo["id"] ?>">
          <?php if (!$u): ?>
            <label class="form-label">Nama</label>
            <input class="form-input" type="text" name="guest_name" maxlength="60" placeholder="Nama kamu (optional)" />
          <?php endif; ?>
          <label class="form-label">Komentar</label>
          <textarea class="form-input" name="body" rows="3" maxlength="500" required placeholder="Tulis komentar yang manis…"></textarea>
          <button class="btn primary" type="submit">Kirim</button>
          <p class="muted" style="margin:10px 0 0; font-size:12px">User biasa tidak perlu login untuk komentar.</p>
        </form>

        <div class="comments">
          <?php if (count($comments) === 0): ?>
            <div class="muted" style="padding:10px 0">Belum ada komentar. Jadi yang pertama ya 💘</div>
          <?php endif; ?>
          <?php foreach ($comments as $c): ?>
            <div class="comment">
              <div class="comment-head">
                <div class="comment-name">
                  <?= h($c["user_name"] ?: ($c["guest_name"] ?: "Guest")) ?>
                </div>
                <div class="comment-time"><?= h(pretty_dt($c["created_at"])) ?></div>
              </div>
              <?php if ($u && $u["role"] === "admin"): ?>
                <form method="post" action="<?= h(base_url("/admin/delete_comment.php")) ?>" style="margin-top:8px" onsubmit="return confirm('Hapus komentar ini?');">
                  <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                  <input type="hidden" name="comment_id" value="<?= (int)$c["id"] ?>">
                  <input type="hidden" name="photo_id" value="<?= (int)$photo["id"] ?>">
                  <button class="pillbtn ghost" type="submit" style="padding:6px 10px">Hapus</button>
                </form>
              <?php endif; ?>
              <div class="comment-body"><?= nl2br(h($c["body"])) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if ($u && $u["role"] === "admin"): ?>
        <div class="admin-mini">
          <div class="sec-title">Admin</div>
          <form method="post" action="<?= h(base_url("/admin/delete_photo.php")) ?>" onsubmit="return confirm('Hapus foto ini?');">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int)$photo["id"] ?>">
            <button class="btn ghost" type="submit">🗑️ Hapus Foto</button>
          </form>
        </div>
      <?php endif; ?>

    </div>
  </div>
</main>

<?php include __DIR__ . "/../includes/layout_bottom.php"; ?>
