<?php
// includes/config.php
declare(strict_types=1);

// For XAMPP defaults:
function config(): array {
  return [
    "DB_HOST" => "127.0.0.1",
    "DB_PORT" => "3306",
    "DB_NAME" => "ourflix",
    "DB_USER" => "root",
    "DB_PASS" => "",
    "DB_CHARSET" => "utf8mb4",
    "BASE_URL" => "/ourflix/public", // if deployed in subfolder: "/ourflix"
  ];
}