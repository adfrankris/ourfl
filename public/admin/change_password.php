<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/admin/")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$targetId = (int)($_POST["user_id"] ?? 0);
$newPass = (string)($_POST["new_password"] ?? "");


$me = current_user();
if (!$me) { header("Location: " . base_url("/login.php")); exit; }
if ($targetId !== (int)$me["id"]) {
  // Only allow changing your own password
  header("Location: " . base_url("/admin/admins.php?err=forbidden_self_only"));
  exit;
}
if ($targetId <= 0 || $newPass === "") {
  header("Location: " . base_url("/admin/admins.php?err=pass"));
  exit;
}

$hash = password_hash($newPass, PASSWORD_DEFAULT);

$pdo = db();
$stmt = $pdo->prepare("UPDATE users SET password_hash=:p WHERE id=:id");
$stmt->execute([":p"=>$hash, ":id"=>$targetId]);

header("Location: " . base_url("/admin/admins.php?ok=pass_changed"));
exit;
