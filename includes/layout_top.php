<?php
// includes/layout_top.php
declare(strict_types=1);
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
start_session();
$u = current_user();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title><?= h($pageTitle ?? "Ourflix") ?></title>
  <meta name="theme-color" content="#0b0b0f">
  <link rel="stylesheet" href="<?= h(base_url("/assets/css/styles.css")) ?>" />
</head>
<body>
<header class="topbar">
  <a class="brand" href="<?= h(base_url("/")) ?>">
    <div class="logo" aria-hidden="true"></div>
    <div class="brand-text">
      <div class="brand-name">Gallery Online</div>
      <div class="brand-sub">Adit&Arra</div>
    </div>
  </a>

  <div class="spacer"></div>

  <div class="search" title="Cari judul/caption/tag">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M10.5 19a8.5 8.5 0 1 1 0-17 8.5 8.5 0 0 1 0 17Z" stroke="currentColor" stroke-width="2"/>
      <path d="M16.7 16.7 21 21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <input id="searchInput" placeholder="Cari momen…" />
  </div>

  <?php if ($u && $u["role"] === "admin"): ?>
    <a class="pillbtn" href="<?= h(base_url("/admin/")) ?>">Admin</a>
  <?php endif; ?>

  <?php if ($u): ?>
    <a class="iconbtn" href="<?= h(base_url("/logout.php")) ?>" title="Logout" aria-label="Logout">
      <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M10 7V6a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2v-1" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M15 12H3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M6 9l-3 3 3 3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
  <?php else: ?>
    <a class="pillbtn" href="<?= h(base_url("/login.php")) ?>">Login</a>
  <?php endif; ?>
</header>
