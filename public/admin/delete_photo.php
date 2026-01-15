<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/admin/photos.php")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$id = (int)($_POST["id"] ?? 0);
if ($id <= 0) { header("Location: " . base_url("/admin/photos.php")); exit; }

$pdo = db();
$stmt = $pdo->prepare("SELECT file_path FROM photos WHERE id = :id");
$stmt->execute([":id"=>$id]);
$p = $stmt->fetch();

if ($p) {
  $pdo->prepare("DELETE FROM photos WHERE id = :id")->execute([":id"=>$id]);
  $path = __DIR__ . "/../" . $p["file_path"]; // public/<file_path>
  if (is_file($path)) @unlink($path);
}

header("Location: " . base_url("/admin/photos.php"));
exit;
