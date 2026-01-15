<?php
// includes/auth.php
declare(strict_types=1);
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/helpers.php";

function current_user(): ?array {
  start_session();
  if (!isset($_SESSION["uid"])) return null;
  $pdo = db();
  $stmt = $pdo->prepare("SELECT id,username,role FROM users WHERE id = :id");
  $stmt->execute([":id" => (int)$_SESSION["uid"]]);
  $u = $stmt->fetch();
  return $u ?: null;
}

function login_user(int $uid): void {
  start_session();
  $_SESSION["uid"] = $uid;
  // rotate CSRF
  unset($_SESSION["csrf"]);
}

function logout_user(): void {
  start_session();
  session_destroy();
}

function require_admin(): void {
  $u = current_user();
  if (!$u || $u["role"] !== "admin") {
    header("Location: " . base_url("/login.php"));
    exit;
  }
}

