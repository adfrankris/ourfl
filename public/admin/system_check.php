<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();
$activeNav = "dashboard";
$pageTitle = "System Check";

include __DIR__ . "/../../includes/admin_layout_top.php";

$gd = null;
$fileinfo = null;
$uploads = __DIR__ . "/../uploads";
$writable = is_dir($uploads) && is_writable($uploads);
?>

<div class="grid">
  <section class="cardx">
    <h2 class="h">System check</h2>
    <div class="admin-list" style="margin-top:10px">
      <div class="admin-item"><div class="admin-item-info"><div class="admin-item-title">Photos</div><div class="admin-item-sub muted">Menambah Gambar / Mengahapus Gambar</div></div><div class="pillbtn ghost">OPTIONAL</div></div>
      <div class="admin-item"><div class="admin-item-info"><div class="admin-item-title">Comments</div><div class="admin-item-sub muted">Mengahapus / Mengedit Comments </div></div><div class="pillbtn ghost">OPTIONAL</div></div>
      <div class="admin-item"><div class="admin-item-info"><div class="admin-item-title">Admins</div><div class="admin-item-sub muted"> Mengganti Kata Sandi  </div></div><div class="pillbtn ghost">OPTIONAL</div></div>
    <div class="muted" style="font-size:12px; margin-top:12px; line-height:1.6">
    </div>
  </section>
</div>

<?php include __DIR__ . "/../../includes/admin_layout_bottom.php"; ?>
