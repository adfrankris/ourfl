<?php
// public/api/photos.php
declare(strict_types=1);

require_once __DIR__ . "/../../includes/db.php";
require_once __DIR__ . "/../../includes/helpers.php";

// Simple JSON API: /api/photos.php?offset=0&limit=24&q=optional
header("Content-Type: application/json; charset=utf-8");

$pdo = db();
$offset = max(0, (int)($_GET["offset"] ?? 0));
$limit = (int)($_GET["limit"] ?? 24);
$limit = ($limit <= 0 || $limit > 60) ? 24 : $limit;

$q = trim((string)($_GET["q"] ?? ""));
$params = [];
$where = "";

if ($q !== "") {
  // Search in title/caption/tags/row_name
  $where = "WHERE p.title LIKE :q";
  $params[":q"] = "%" . $q . "%";
}

// fetch limit+1 to know if hasMore
$sql = "
  SELECT p.*, u.name AS uploader
  FROM photos p
  LEFT JOIN users u ON u.id=p.created_by
  $where
  ORDER BY p.created_at DESC, p.id DESC
  LIMIT :lim OFFSET :off
";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) $stmt->bindValue($k, $v, PDO::PARAM_STR);
$stmt->bindValue(":lim", $limit + 1, PDO::PARAM_INT);
$stmt->bindValue(":off", $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll();

$hasMore = count($rows) > $limit;
if ($hasMore) array_pop($rows);

$out = [];
foreach ($rows as $p) {
  $out[] = [
    "id" => (int)$p["id"],
    "title" => (string)$p["title"],
    "caption" => (string)($p["caption"] ?? ""),
    "row_name" => (string)($p["row_name"] ?? "Moments"),
    "tags" => (string)($p["tags"] ?? ""),
    "created_at" => (string)$p["created_at"],
    "created_at_pretty" => pretty_dt((string)$p["created_at"]),
    "file_path" => (string)$p["file_path"],
    "file_url" => base_url("/" . (string)$p["file_path"]),
    "thumb_url" => base_url("/" . (string)$p["file_path"]),
  ];
}

echo json_encode([
  "offset" => $offset,
  "limit" => $limit,
  "nextOffset" => $offset + $limit,
  "hasMore" => $hasMore,
  "items" => $out,
], JSON_UNESCAPED_UNICODE);
