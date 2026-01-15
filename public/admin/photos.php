<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

$activeNav = "photos";
$pageTitle = "Photos";
$pdo = db();
$csrf = csrf_token();

$photos = $pdo->query("SELECT id,title,file_path,created_at FROM photos ORDER BY created_at DESC, id DESC LIMIT 400")->fetchAll();

include __DIR__ . "/../../includes/admin_layout_top.php";
?>

<?php
  $err = (string)($_GET["err"] ?? "");
  $ok  = (string)($_GET["ok"] ?? "");
?>
<?php if ($err !== ""): ?>
  <div class="cardx" style="border-color:rgba(255,59,134,.25); background: rgba(255,59,134,.08); margin-bottom:14px;">
    <b>Upload gagal:</b> <?= h($err) ?>
    <div class="muted" style="font-size:12px; margin-top:6px">Cek phpinfo: pastikan GD + fileinfo aktif, dan folder <code>public/uploads</code> bisa ditulis.</div>
  </div>
<?php elseif ($ok !== ""): ?>
  <div class="cardx" style="border-color:rgba(34,197,94,.22); background: rgba(34,197,94,.08); margin-bottom:14px;">
    ✅ Sukses: <?= h($ok) ?>
  </div>
<?php endif; ?>


<div class="grid cols-3">
  <section class="cardx" style="grid-column: span 2;">
    <h2 class="h">Upload photo</h2>
    <form class="form" method="post" action="<?= h(base_url("/admin/upload_photo.php")) ?>" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">

      <label class="form-label">File Foto</label>
      <input class="form-input" type="file" name="photo" accept="image/*" required>

      <label class="form-label">Judul</label>
      <input class="form-input" type="text" name="title" maxlength="80" required placeholder="Contoh: First Date">

      <label class="form-label">Caption</label>
      <textarea class="form-input" name="caption" rows="3" maxlength="280" placeholder="Cerita singkat…"></textarea>

      <button class="btn primary w100" type="submit">Upload</button>
      <p class="muted" style="margin:10px 0 0; font-size:12px">jpg/png/webp • max 8MB</p>
    </form>
  </section>

  <section class="cardx">
    <h2 class="h">Tips</h2>
    <div class="muted" style="font-size:12px; line-height:1.6">
      • Klik ✏️ untuk edit judul/caption<br>
      • Klik 🗑️ untuk hapus foto (akan menghapus komentar terkait via DB)<br>
      • Gunakan gambar ukuran tidak terlalu besar agar cepat di-load
    </div>
    <div style="margin-top:12px">
      <a class="pillbtn ghost" href="<?= h(base_url("/")) ?>">View gallery</a>
    </div>
  </section>
</div>

<div class="grid" style="margin-top:14px">
  <section class="cardx">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap">
      <h2 class="h" style="margin:0">Manage photos</h2>
      <div class="muted" style="font-size:12px"><?= count($photos) ?> items</div>
    </div>

    <?php if (count($photos) === 0): ?>
      <div class="muted">Belum ada foto.</div>
    <?php endif; ?>

    <div class="admin-list" style="margin-top:10px">
      <?php foreach ($photos as $p): ?>
        <div class="admin-item">
          <img class="thumb" src="<?= h(base_url("/" . $p["file_path"])) ?>" alt="">
          <div class="admin-item-info">
            <div class="admin-item-title"><?= h($p["title"]) ?></div>
            <div class="admin-item-sub muted"><?= h(pretty_dt($p["created_at"])) ?></div>
          </div>

          <a class="iconbtn" href="<?= h(base_url("/admin/edit_photo.php?id=" . (int)$p["id"])) ?>" title="Edit" aria-label="Edit">✏️</a>

          <form method="post" action="<?= h(base_url("/admin/delete_photo.php")) ?>" onsubmit="return confirm('Hapus foto ini?');">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
            <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">
            <button class="iconbtn" type="submit" title="Hapus">🗑️</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<?php include __DIR__ . "/../../includes/admin_layout_bottom.php"; ?>
