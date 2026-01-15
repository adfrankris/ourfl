<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/admin/")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$id = (int)($_POST["id"] ?? 0);
$title = trim((string)($_POST["title"] ?? ""));
$caption = trim((string)($_POST["caption"] ?? ""));
$createdAtRaw = trim((string)($_POST["created_at"] ?? ""));
$rowName = "Moments";
$tags = "";

if ($id <= 0 || $title === "") {
  header("Location: " . base_url("/admin/"));
  exit;
}

$title = mb_substr($title, 0, 80);
$caption = mb_substr($caption, 0, 280);

$pdo = db();

// created_at (optional)
$createdAtSql = null;
if ($createdAtRaw !== "") {
  // input format: YYYY-MM-DDTHH:MM
  $createdAtRaw = str_replace("T", " ", $createdAtRaw);
  $dt = DateTime::createFromFormat("Y-m-d H:i", $createdAtRaw);
  if (!$dt) {
    header("Location: " . base_url("/admin/edit_photo.php?id=" . $id . "&err=created_at"));
    exit;
  }
  $createdAtSql = $dt->format("Y-m-d H:i:00");
}
$stmt = $pdo->prepare("UPDATE photos SET title=:t, caption=:c, row_name=:r, tags=:g WHERE id=:id");
$stmt->execute([
  ":t"  => $title,
  ":c"  => $caption,
  ":ca" => $createdAtSql,
  ":id" => $id,
]);

header("Location: " . base_url("/admin/edit_photo.php?id=" . $id . "&ok=1"));
exit;
