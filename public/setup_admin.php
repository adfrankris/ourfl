<?php
// public/setup_admin.php
// Jalankan SEKALI untuk membuat akun admin default jika belum ada admin.
// Setelah berhasil, HAPUS file ini untuk keamanan.
declare(strict_types=1);

require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/helpers.php";

$pdo = db();

// Default credentials (simple)
$ADMIN_USERNAME = "admin";
$ADMIN_PASS     = "admin123";

// Gate simple (ubah biar aman)
$SETUP_KEY = "setup123";
$key = (string)($_GET["key"] ?? "");
if ($SETUP_KEY !== "" && $key !== $SETUP_KEY) {
  http_response_code(403);
  echo "Forbidden. Tambahkan ?key=SETUP_KEY\n";
  exit;
}

$cnt = (int)$pdo->query("SELECT COUNT(*) AS c FROM users")->fetch()["c"];
if ($cnt > 0) {
  echo "Akun admin sudah ada / tabel users tidak kosong.\n";
  echo "Disarankan hapus file public/setup_admin.php\n";
  exit;
}

$hash = password_hash($ADMIN_PASS, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (username,password_hash,role,created_at) VALUES (:u,:p,'admin',:d)");
$stmt->execute([
  ":u" => $ADMIN_USERNAME,
  ":p" => $hash,
  ":d" => date("Y-m-d H:i:s"),
]);

echo "✅ Admin dibuat!\n\n";
echo "Username: {$ADMIN_USERNAME}\n";
echo "Password: {$ADMIN_PASS}\n\n";
echo "Login: " . base_url("/login.php") . "\n";
echo "⚠️ Hapus file public/setup_admin.php setelah selesai.\n";
