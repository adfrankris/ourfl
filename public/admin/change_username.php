<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/admin/admins.php")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$me = current_user();
if (!$me) { header("Location: " . base_url("/login.php")); exit; }

$userId = (int)($me["id"] ?? 0);
$newUsername = trim((string)($_POST["username"] ?? ""));

$newUsername = strtolower($newUsername);
$newUsername = mb_substr($newUsername, 0, 40);

if ($userId <= 0 || $newUsername === "") {
  header("Location: " . base_url("/admin/admins.php?err=username"));
  exit;
}

// basic safe chars (letters, numbers, underscore, dot, dash)
if (!preg_match('/^[A-Za-z0-9_.-]{3,40}$/', $newUsername)) {
  header("Location: " . base_url("/admin/admins.php?err=username_format"));
  exit;
}

$pdo = db();
try {
  $stmt = $pdo->prepare("UPDATE users SET username=:u WHERE id=:id");
  $stmt->execute([":u"=>$newUsername, ":id"=>$userId]);
  header("Location: " . base_url("/admin/admins.php?ok=username_changed"));
  exit;
} catch (PDOException $e) {
  header("Location: " . base_url("/admin/admins.php?err=username_taken"));
  exit;
}
