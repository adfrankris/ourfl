<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

$pdo = db();
$id = (int)($_GET["id"] ?? 0);
if ($id <= 0) { header("Location: " . base_url("/admin/")); exit; }

$stmt = $pdo->prepare("SELECT *, created_at FROM photos WHERE id = :id");
$stmt->execute([":id" => $id]);
$p = $stmt->fetch();
if (!$p) { header("Location: " . base_url("/admin/")); exit; }

$pageTitle = "Edit Foto — Admin";
$csrf = csrf_token();

include __DIR__ . "/../../includes/admin_layout_top.php";
?>
<div class="grid"><section class="cardx">
  <div class="admin-head">
    <h1 class="admin-title">Edit Foto</h1>
    <div style="display:flex; gap:10px; flex-wrap:wrap">
      <a class="pillbtn ghost" href="<?= h(base_url("/photo.php?id=" . (int)$p["id"])) ?>">Lihat Foto</a>
      <a class="pillbtn ghost" href="<?= h(base_url("/admin/")) ?>">Kembali Admin</a>
    </div>
  </div>

  
    <section class="admin-card">
      <h2 class="sec-title">Ubah Info</h2>

      <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-start; margin: 10px 0 14px;">
        <img src="<?= h(base_url("/" . (string)$p["file_path"])) ?>" alt="" style="width:140px; height:210px; object-fit:cover; border-radius:18px; border:1px solid rgba(255,255,255,.10)">
        <div class="muted" style="font-size:12px; line-height:1.5">
          <div><b>ID:</b> <?= (int)$p["id"] ?></div>
          <div><b>Created:</b> <?= h(pretty_dt((string)$p["created_at"])) ?></div>
          <div><b>File:</b> <?= h((string)$p["file_path"]) ?></div>
        </div>
      </div>

      <form class="form" method="post" action="<?= h(base_url("/admin/update_photo.php")) ?>">
        <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
        <input type="hidden" name="id" value="<?= (int)$p["id"] ?>">

        <label class="form-label">Judul</label>
        <input class="form-input" type="text" name="title" maxlength="80" required value="<?= h((string)$p["title"]) ?>">

        <label class="form-label">Caption</label>
        <textarea class="form-input" name="caption" rows="3" maxlength="280" placeholder="Cerita singkat…"><?= h((string)($p["caption"] ?? "")) ?></textarea>

       <input class="form-input" type="datetime-local" name="created_at" value="<?= h(date("Y-m-d\TH:i", strtotime((string)$photo["created_at"]))) ?>">
      <p class="muted" style="margin:8px 0 0; font-size:12px">Bisa diset manual oleh admin.</p>

">
">

        <button class="btn primary w100" type="submit">Simpan Perubahan</button>
      </form>
    </section>
  </div>
</section></div>
<?php include __DIR__ . "/../../includes/admin_layout_bottom.php"; ?>
