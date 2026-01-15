<?php
declare(strict_types=1);

function env(string $key, string $default = ""): string {
  $v = getenv($key);
  return ($v === false || $v === null || $v === "") ? $default : (string)$v;
}

function config(): array {
  return [
    "DB_HOST" => env("DB_HOST", "127.0.0.1"),
    "DB_PORT" => env("DB_PORT", "3306"),
    "DB_NAME" => env("DB_NAME", "ourflix"),
    "DB_USER" => env("DB_USER", "root"),
    "DB_PASS" => env("DB_PASS", ""),
    "DB_CHARSET" => env("DB_CHARSET", "utf8mb4"),

    // untuk hosting biasanya biarkan kosong (auto-detect)
    "BASE_URL" => env("BASE_URL", "/ourflix/public"),
  ];
}
