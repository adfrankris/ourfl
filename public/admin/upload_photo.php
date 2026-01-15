<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/admin/")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$title = trim((string)($_POST["title"] ?? ""));
$caption = trim((string)($_POST["caption"] ?? ""));
$rowName = "Moments";
$tags = "";

if ($title === "" || !isset($_FILES["photo"]) || $_FILES["photo"]["error"] !== UPLOAD_ERR_OK) {
  header("Location: " . base_url("/admin/?err=upload"));
  exit;
}

$file = $_FILES["photo"];
if ($file["size"] > 8 * 1024 * 1024) { header("Location: " . base_url("/admin/?err=size")); exit; }

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file["tmp_name"]);
$allowed = ["image/jpeg"=>"jpg","image/png"=>"png","image/webp"=>"webp"];
if (!isset($allowed[$mime])) { header("Location: " . base_url("/admin/?err=type")); exit; }

$ext = $allowed[$mime];
$basename = bin2hex(random_bytes(12)) . "." . $ext;

$uploadDir = __DIR__ . "/../uploads";
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

$dest = $uploadDir . "/" . $basename;
if (!move_uploaded_file($file["tmp_name"], $dest)) { header("Location: " . base_url("/admin/?err=move")); exit; }

$relPath = "uploads/" . $basename;

$pdo = db();
$u = current_user();$stmt = $pdo->prepare("INSERT INTO photos (title,caption,row_name,tags,file_path,created_at,created_by) VALUES (:t,:c,:r,:g,:p,:d,:u)");
$stmt->execute([
  ":t" => mb_substr($title,0,80),
  ":c" => mb_substr($caption,0,280),
  ":r" => $rowName,
  ":g" => $tags,
  ":p" => $relPath,
  ":d" => date("Y-m-d H:i:s"),
  ":u" => $u ? (int)$u["id"] : null,
]);

header("Location: " . base_url("/admin/"));
exit;

  // Simple upload (no compress/thumbnail)
  $uploadsDirFs = __DIR__ . "/../uploads";
  $uploadsDirRel = "uploads";
  if (!is_dir($uploadsDirFs)) mkdir($uploadsDirFs, 0775, true);

  $origName = (string)($file["name"] ?? "");
  $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
  $allowedExt = ["jpg","jpeg","png","webp"];
  if (!in_array($ext, $allowedExt, true)) {
    $error = "Format gambar harus jpg/png/webp.";
    header("Location: " . base_url("/admin/photos.php?err=" . urlencode($error)));
    exit;
  }

  $base = bin2hex(random_bytes(8));
  $targetFs = rtrim($uploadsDirFs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $base . "." . $ext;
  if (!move_uploaded_file($file["tmp_name"], $targetFs)) {
    $error = "Gagal menyimpan file upload.";
    header("Location: " . base_url("/admin/photos.php?err=" . urlencode($error)));
    exit;
  }

  $targetRel = trim($uploadsDirRel, "/") . "/" . basename($targetFs);

