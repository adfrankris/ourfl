<?php
declare(strict_types=1);
require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";
require_once __DIR__ . "/../../includes/auth.php";

require_admin();

if ($_SERVER["REQUEST_METHOD"] !== "POST") { header("Location: " . base_url("/admin/")); exit; }
if (!csrf_verify((string)($_POST["csrf"] ?? ""))) { http_response_code(403); echo "CSRF invalid"; exit; }

$username = trim((string)($_POST["username"] ?? ""));

$username = strtolower($username);
$password = (string)($_POST["password"] ?? "");

$username = mb_substr($username, 0, 40);

if ($username === "" || $password === "") {
  header("Location: " . base_url("/admin/admins.php?err=create_admin"));
  exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

$pdo = db();
try {
  $stmt = $pdo->prepare("INSERT INTO users (username,password_hash,role,created_at) VALUES (:u,:p,'admin',:d)");
  $stmt->execute([
    ":u" => $username,
    ":p" => $hash,
    ":d" => date("Y-m-d H:i:s"),
  ]);
  header("Location: " . base_url("/admin/admins.php?ok=admin_created"));
  exit;
} catch (PDOException $e) {
  header("Location: " . base_url("/admin/admins.php?err=username_taken"));
  exit;
}
