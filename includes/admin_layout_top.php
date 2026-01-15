<?php
// includes/admin_layout_top.php
declare(strict_types=1);
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/helpers.php";
require_once __DIR__ . "/auth.php";
start_session();
$u = current_user();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <title><?= h($pageTitle ?? "Admin — Ourflix") ?></title>
  <meta name="theme-color" content="#0b0b0f">
  <link rel="stylesheet" href="<?= h(base_url("/assets/css/styles.css")) ?>" />
  <link rel="stylesheet" href="<?= h(base_url("/assets/css/admin.css")) ?>" />
</head>
<body class="admin-body">
  <aside class="admin-sidebar" id="sidebar">
    <div class="sb-top">
      <a class="sb-brand" href="<?= h(base_url("/admin/")) ?>">
        <div class="sb-logo" aria-hidden="true"></div>
        <div class="sb-brandtext">
          <div class="sb-brandname">Admin Dashboard</div>
          <div class="sb-sub">Admin</div>
        </div>
      </a>
      <button class="sb-toggle" id="sbToggle" type="button" aria-label="Toggle sidebar">☰</button>
    </div>

    <div class="sb-section">MENU</div>
    <nav class="sb-nav">
      <a class="sb-item <?= ($activeNav ?? '') === 'dashboard' ? 'active':'' ?>" href="<?= h(base_url("/admin/")) ?>">
        <span class="ic">🏠</span><span>Dashboard</span>
      </a>
      <a class="sb-item <?= ($activeNav ?? '') === 'photos' ? 'active':'' ?>" href="<?= h(base_url("/admin/photos.php")) ?>">
        <span class="ic">🖼️</span><span>Photos</span>
      </a>
      <a class="sb-item <?= ($activeNav ?? '') === 'admins' ? 'active':'' ?>" href="<?= h(base_url("/admin/admins.php")) ?>">
        <span class="ic">👤</span><span>Admins</span>
      </a>
      <a class="sb-item <?= ($activeNav ?? '') === 'comments' ? 'active':'' ?>" href="<?= h(base_url("/admin/comments.php")) ?>">
        <span class="ic">💬</span><span>Comments</span>
      </a>
      <a class="sb-item" href="<?= h(base_url("/admin/system_check.php")) ?>"><span class="ic">🛠️</span><span>System check</span></a>
      <a class="sb-item" href="<?= h(base_url("/")) ?>">
        <span class="ic">🎬</span><span>View site</span>
      </a>
    </nav>

    <div class="sb-section">SUPPORT</div>
    <nav class="sb-nav">
      <a class="sb-item" href="<?= h(base_url("/logout.php")) ?>">
        <span class="ic">🚪</span><span>Logout</span>
      </a>
    </nav>

    <div class="sb-foot">
      <div class="sb-user">
        <div class="dot"></div>
        <div>
          <div class="sb-username"><?= h($u["username"] ?? "admin") ?></div>
          <div class="sb-role">admin</div>
        </div>
      </div>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="iconbtn" id="mobileMenu" type="button" aria-label="Open menu">☰</button>
      <div class="admin-titlewrap">
        <div class="admin-pagetitle"><?= h($pageTitle ?? "Dashboard") ?></div>
        <div class="admin-breadcrumb">Admin / <?= h($pageTitle ?? "Dashboard") ?></div>
      </div>
      <div class="spacer"></div>
      <a class="pillbtn ghost" href="<?= h(base_url("/")) ?>">Open site</a>
    </header>

    <main class="admin-content">
