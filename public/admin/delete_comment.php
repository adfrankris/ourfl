<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/admin/")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$cid = (int)($_POST["comment_id"] ?? 0);
$pid = (int)($_POST["photo_id"] ?? 0);
if ($cid <= 0) { header("Location: " . base_url("/")); exit; }

$pdo = db();
$stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
$stmt->execute([":id" => $cid]);

if ($pid > 0) {
  header("Location: " . base_url("/photo.php?id=" . $pid));
} else {
  header("Location: " . base_url("/admin/"));
}
exit;
