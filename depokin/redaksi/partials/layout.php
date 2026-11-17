<?php
declare(strict_types=1);

function layout_mulai(string $title, string $active): void
{
    $user = require_login();
    $menu = [
        ['index.php', 'Dashboard', 'dashboard', true, 'dashboard'],
        ['artikel.php', 'Artikel', 'artikel', true, 'doc'],
        ['tinjauan.php', 'Tinjauan', 'tinjauan', is_editor(), 'eye'],
        ['taxonomi.php', 'Kategori & Lokasi', 'taxonomi', is_editor(), 'tag'],
        ['pengguna.php', 'Pengguna', 'pengguna', is_admin(), 'users'],
        ['pengaturan.php', 'Pengaturan situs', 'pengaturan', is_admin(), 'gear'],
        ['profil.php', 'Profil saya', 'profil', true, 'user'],
    ];
    $sukses = flash('sukses');
    $error = flash('error');
    ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <meta name="theme-color" content="#f2f4fb">
  <title><?= e($title) ?> — Redaksi depokin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= url('redaksi/assets/admin.css') ?>">
</head>
<body>
<div class="admin-shell">
  <div class="overlay" aria-hidden="true"></div>
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= url('redaksi/index.php') ?>">depokin<span class="brand-dot">.</span><small>PANEL REDAKSI</small></a>
    <nav class="side-nav" aria-label="Menu redaksi">
      <?php foreach ($menu as [$path, $label, $key, $boleh, $ikon]): if (!$boleh) continue; ?>
      <a href="<?= url('redaksi/' . $path) ?>" <?= $active === $key ? 'aria-current="page"' : '' ?>><span data-icon="<?= e($ikon) ?>"></span><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="side-foot">
      <a href="<?= url() ?>" target="_blank" rel="noreferrer"><span data-icon="external"></span>Lihat situs</a>
      <form method="post" action="<?= url('redaksi/logout.php') ?>"><?= csrf_field() ?><button type="submit"><span data-icon="logout"></span>Keluar</button></form>
    </div>
  </aside>
  <div class="admin-main">
    <header class="topbar">
      <button class="hamburger" aria-label="Buka menu" aria-controls="sidebar"><span data-icon="menu"></span></button>
      <h1><?= e($title) ?></h1>
      <div class="user-chip">
        <span class="user-name"><?= e($user['name']) ?></span>
        <span class="role-badge"><?= e($user['role']) ?></span>
        <span class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
      </div>
    </header>
    <div class="admin-content">
      <?php if ($sukses): ?><div class="flash sukses" role="status"><?= e($sukses) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="flash error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <?php
}

function layout_selesai(): void
{
    ?>
    </div>
  </div>
</div>
<script src="<?= url('redaksi/assets/admin.js') ?>"></script>
</body>
</html>
    <?php
}