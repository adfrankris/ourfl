<?php
// includes/db.php
declare(strict_types=1);
require_once __DIR__ . "/config.php";

function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $cfg = config();
  $dsn = sprintf(
    "mysql:host=%s;port=%s;dbname=%s;charset=%s",
    $cfg["DB_HOST"], $cfg["DB_PORT"], $cfg["DB_NAME"], $cfg["DB_CHARSET"]
  );

  $pdo = new PDO($dsn, $cfg["DB_USER"], $cfg["DB_PASS"], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]);
  return $pdo;
}
