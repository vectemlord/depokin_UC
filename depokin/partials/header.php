<?php
// expects: $page_title, $meta_description, $canonical, $active_nav, $og (array), $jsonld (?string), $noindex (bool)
$page_title = $page_title ?? 'depokin — Dekat kotanya, dekat ceritanya.';
$meta_description = $meta_description ?? setting('site_description');
$canonical = $canonical ?? url(ltrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/'));
$active_nav = $active_nav ?? '';
$og = $og ?? [];
$jsonld = $jsonld ?? null;
$noindex = $noindex ?? false;
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="<?= $noindex ? 'noindex,nofollow' : 'index,follow' ?>">
  <meta name="theme-color" content="#f2f4fb">
  <meta name="description" content="<?= e($meta_description) ?>">
  <title><?= e($page_title) ?></title>
  <link rel="canonical" href="<?= e($canonical) ?>">
  <meta property="og:site_name" content="<?= e(setting('site_name', 'depokin')) ?>">
  <meta property="og:title" content="<?= e($og['title'] ?? $page_title) ?>">
  <meta property="og:description" content="<?= e($og['description'] ?? $meta_description) ?>">
  <meta property="og:type" content="<?= e($og['type'] ?? 'website') ?>">
  <meta property="og:url" content="<?= e($canonical) ?>">
  <?php if (!empty($og['image'])): ?><meta property="og:image" content="<?= e($og['image']) ?>">
  <meta name="twitter:card" content="summary_large_image"><?php endif; ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= url('styles.css') ?>">
  <?php if ($jsonld): ?><script type="application/ld+json"><?= $jsonld ?></script><?php endif; ?>
</head>
<body>
  <a class="skip-link" href="#main">Langsung ke konten</a>
  <header class="site-header">
    <div class="header-inner">
      <a href="<?= url() ?>" class="brand" aria-label="Depokin, beranda">depokin<span class="brand-dot">.</span><span class="brand-tag">CERITA KOTA KITA</span></a>
      <nav class="desktop-nav" aria-label="Navigasi utama">
        <a href="<?= url() ?>" <?= $active_nav === 'beranda' ? 'aria-current="page"' : '' ?>>Beranda</a>
        <a href="<?= url('jelajah') ?>" <?= $active_nav === 'jelajah' ? 'aria-current="page"' : '' ?>>Jelajah Depok</a>
        <a href="<?= url('video') ?>" <?= $active_nav === 'video' ? 'aria-current="page"' : '' ?>>Video</a>
        <a href="<?= url('tentang') ?>" <?= $active_nav === 'tentang' ? 'aria-current="page"' : '' ?>>Tentang kami</a>
      </nav>
      <button class="search-trigger" aria-label="Cari berita Depok"><span data-icon="search"></span><span>Cari cerita</span></button>
    </div>
  </header>
  <main id="main" tabindex="-1">