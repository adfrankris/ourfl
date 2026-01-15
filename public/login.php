<?php
declare(strict_types=1);
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/helpers.php";
require_once __DIR__ . "/../includes/auth.php";

start_session();
$u = current_user();
if ($u) { header("Location: " . base_url("/admin/")); exit; }

$pageTitle = "Login — Ourflix";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $username = trim((string)($_POST["username"] ?? ""));
  
  $username = strtolower($username);
  $pass  = (string)($_POST["password"] ?? "");
  $pdo = db();
  $stmt = $pdo->prepare("SELECT id,password_hash FROM users WHERE username = :u");
  $stmt->execute([":u" => $username]);
  $row = $stmt->fetch();
  if ($row && password_verify($pass, (string)$row["password_hash"])) {
    login_user((int)$row["id"]);
    header("Location: " . base_url("/admin/"));
    exit;
  }
  $error = "Username atau password salah 😅";
}

include __DIR__ . "/../includes/layout_top.php";
?>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-head">
      <div class="logo lg" aria-hidden="true"></div>
      <div>
        <div class="auth-title">Admin Login</div>
        <div class="auth-sub">Masuk untuk mengelola gallery (upload, edit, hapus, admin).</div>
      </div>
    </div>

    <form method="post" class="form">
      <label class="form-label">Username</label>
      <input class="form-input" type="text" name="username" required placeholder="admin">

      <label class="form-label">Password</label>
      <input class="form-input" type="password" name="password" required placeholder="••••••••">

      <button class="btn primary w100" type="submit">Login</button>
      <div class="auth-hint"><?= h($error) ?></div>

      <div class="auth-foot">
        Hanya admin yang dapat login.
      </div>
      <div class="muted" style="font-size:12px;margin-top:10px">
        Catatan: akun pertama yang didaftarkan otomatis jadi <b>admin</b>.
      </div>
    </form>
  </div>
</div>
<?php include __DIR__ . "/../includes/layout_bottom.php"; ?>
