<?php
declare(strict_types=1);
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/helpers.php";
require_once __DIR__ . "/../includes/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$photoId = (int)($_POST["photo_id"] ?? 0);
$body = trim((string)($_POST["body"] ?? ""));
$guest = trim((string)($_POST["guest_name"] ?? ""));

if ($photoId <= 0 || $body === "") {
  header("Location: " . base_url("/"));
  exit;
}

$u = current_user();
$userId = $u ? (int)$u["id"] : null;
$guestName = $u ? null : ($guest !== "" ? mb_substr($guest, 0, 60) : null);
$body = mb_substr($body, 0, 500);

$pdo = db();
$stmt = $pdo->prepare("INSERT INTO comments (photo_id, user_id, guest_name, body, created_at) VALUES (:p,:u,:g,:b,:d)");
$stmt->execute([
  ":p" => $photoId,
  ":u" => $userId,
  ":g" => $guestName,
  ":b" => $body,
  ":d" => date("Y-m-d H:i:s"),
]);

header("Location: " . base_url("/photo.php?id=" . $photoId));
exit;
