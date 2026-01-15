<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

$activeNav = "admins";
$pageTitle = "Admins";
$pdo = db();
$csrf = csrf_token();
$me = current_user();

$admins = $pdo->query("SELECT id,username,created_at FROM users ORDER BY id ASC")->fetchAll();

include __DIR__ . "/../../includes/admin_layout_top.php";
?>

<?php
  $err = (string)($_GET["err"] ?? "");
  $ok  = (string)($_GET["ok"] ?? "");
?>
<?php if ($err !== ""): ?>
  <div class="cardx" style="border-color:rgba(255,59,134,.25); background: rgba(255,59,134,.08); margin-bottom:14px;">
    <b>Gagal:</b> <?= h($err) ?>
  </div>
<?php elseif ($ok !== ""): ?>
  <div class="cardx" style="border-color:rgba(34,197,94,.22); background: rgba(34,197,94,.08); margin-bottom:14px;">
    ✅ Sukses: <?= h($ok) ?>
  </div>
<?php endif; ?>


<div class="grid cols-3">
  <section class="cardx" style="grid-column: span 2;">
    <h2 class="h">Create admin</h2>
    <div class="muted" style="font-size:12px; margin-bottom:10px">
      Buat akun admin baru. (Login tetap pakai username + password)
    </div>
    <form class="form" method="post" action="<?= h(base_url("/admin/create_admin.php")) ?>">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <label class="form-label">Username</label>
      <input class="form-input" type="text" name="username" maxlength="40" required placeholder="contoh: admin2">

      <label class="form-label">Password</label>
      <input class="form-input" type="text" name="password" required value="admin123">

      <button class="btn primary w100" type="submit">Buat Admin</button>
    </form>
  </section>

  <section class="cardx">
    <h2 class="h">Your account</h2>
    <div class="muted" style="font-size:12px; line-height:1.6">
      Kamu hanya bisa mengganti password untuk akun yang sedang login: <b><?= h($me["username"] ?? "admin") ?></b>
    </div>
    
    <form class="form" method="post" action="<?= h(base_url("/admin/change_username.php")) ?>" style="margin-top:12px">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <label class="form-label">Username baru</label>
      <input class="form-input" type="text" name="username" required value="<?= h($me["username"] ?? "admin") ?>" placeholder="username" />
      <p class="muted" style="margin:8px 0 0; font-size:12px">Boleh: huruf/angka/._- (min 3).</p>
      <button class="btn ghost w100" type="submit">Simpan Username</button>
    </form>

    <form class="form" method="post" action="<?= h(base_url("/admin/change_password.php")) ?>" style="margin-top:12px">
      <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
      <input type="hidden" name="user_id" value="<?= (int)($me["id"] ?? 0) ?>">
      <label class="form-label">Password baru</label>
      <input class="form-input" type="text" name="new_password" required placeholder="password baru">
      <button class="btn w100" type="submit">Simpan</button>
    </form>
  </section>
</div>

<div class="grid" style="margin-top:14px">
  <section class="cardx">
    <div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap">
      <h2 class="h" style="margin:0">All admins</h2>
      <div class="muted" style="font-size:12px"><?= count($admins) ?> accounts</div>
    </div>

    <div class="admin-list" style="margin-top:10px">
      <?php foreach ($admins as $ad): ?>
        <div class="admin-item" style="align-items:center">
          <div class="admin-item-info">
            <div class="admin-item-title"><?= h((string)$ad["username"]) ?><?= ((int)$ad["id"] === (int)($me["id"] ?? -1)) ? " <span class='muted' style='font-weight:600'>(you)</span>" : "" ?></div>
            <div class="admin-item-sub muted">dibuat: <?= h(pretty_dt((string)$ad["created_at"])) ?></div>
          </div>
          <?php if ((int)$ad["id"] === (int)($me["id"] ?? -1)): ?>
            <div class="pillbtn ghost">editable</div>
          <?php else: ?>
            <div class="pillbtn ghost" title="Tidak bisa ubah password admin lain Attached to request">readonly</div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<?php include __DIR__ . "/../../includes/admin_layout_bottom.php"; ?>
