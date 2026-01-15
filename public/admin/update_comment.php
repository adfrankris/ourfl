<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  header("Location: " . base_url("/admin/comments.php"));
  exit;
}
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) {
  http_response_code(403);
  echo "CSRF invalid";
  exit;
}

$id = (int)($_POST["id"] ?? 0);
$body = trim((string)($_POST["body"] ?? ""));

if ($id <= 0 || $body === "") {
  header("Location: " . base_url("/admin/comments.php?err=invalid"));
  exit;
}

$body = mb_substr($body, 0, 500);

$pdo = db();
$stmt = $pdo->prepare("UPDATE comments SET body=:b WHERE id=:id");
$stmt->execute([
  ":b" => $body,
  ":id" => $id,
]);

header("Location: " . base_url("/admin/comments.php?ok=updated"));
exit;
