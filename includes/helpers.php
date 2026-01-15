<?php
// includes/helpers.php
declare(strict_types=1);

require_once __DIR__ . "/config.php";

function h(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function detect_base_url(): string {
  // Auto-detect base URL for apps in subfolders, e.g. /ourflix/public
  // If BASE_URL is set in config.php, it will be used instead.
  $cfg = config();
  $base = trim((string)($cfg["BASE_URL"] ?? ""));
  if ($base !== "") return rtrim($base, "/");

  // SCRIPT_NAME example: /ourflix/public/index.php
  $script = (string)($_SERVER["SCRIPT_NAME"] ?? "");
  $dir = rtrim(str_replace("\\", "/", dirname($script)), "/");
  return $dir === "" ? "" : $dir;
}

function base_url(string $path = ""): string {
  $base = detect_base_url();
  return $base . $path;
}

function start_session(): void {
  if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
  }
}

function csrf_token(): string {
  start_session();
  if (!isset($_SESSION["csrf"])) $_SESSION["csrf"] = bin2hex(random_bytes(16));
  return $_SESSION["csrf"];
}
function csrf_verify(string $token): bool {
  start_session();
  return hash_equals($_SESSION["csrf"] ?? "", $token);
}

function pretty_dt(string $iso): string {
  $ts = strtotime($iso);
  if ($ts === false) return $iso;
  if (class_exists('IntlDateFormatter')) {
    $fmt = new IntlDateFormatter('id_ID', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
    return $fmt->format($ts);
  }
  return date('d M Y H:i', $ts);
}

function sanitize_row(string $row): string {
  $row = trim($row);
  return $row === "" ? "Moments" : mb_substr($row, 0, 30);
}

function sanitize_tags(string $tags): string {
  $tags = preg_replace('/[^\pL\pN,\-\s]/u', '', $tags ?? '');
  $tags = preg_replace('/\s+/', ' ', $tags);
  return trim(mb_substr($tags, 0, 120));
}
