<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';

$featured = artikel_featured();
$latest = artikel_terbit(8);
$side = array_values(array_filter($latest, fn($a) => !$featured || (int)$a['id'] !== (int)$featured['id']));
$videos = artikel_terbit(2, 0, ['format' => 'video']);

$page_title = 'depokin — Dekat kotanya, dekat ceritanya.';
$meta_description = setting('site_description');
$canonical = url();
$active_nav = 'beranda';
$og = ['type' => 'website', 'image' => $featured ? media_url($featured['thumb_path']) : null];
$jsonld = json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => setting('site_name', 'depokin'),
    'url' => url(),
    'description' => setting('site_description'),
    'inLanguage' => 'id',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

require __DIR__ . '/partials/header.php';
?>
<div class="container">
  <section class="welcome">
    <div>
      <div class="eyebrow">Depok, dari dekat.</div>
      <h1>Ada cerita apa <span class="line-mobile">di <span>Depok?</span></span></h1>
    </div>
    <p class="welcome-note"><span class="date-label">KABAR · ORANG · TEMPAT</span>Dari kabar kota sampai sudut favoritmu.<br>Cerita yang dekat dengan keseharian.</p>
  </section>
  <?= chips_kategori() ?>
  <?php if ($featured): ?>
  <section class="hero-grid" aria-label="Pilihan redaksi">
    <a class="hero-story" href="<?= e(artikel_url($featured)) ?>">
      <?= kartu_thumb($featured, false) ?>
      <div class="hero-top"><span class="pill-label">PILIHAN REDAKSI</span><span class="photo-label"><span data-icon="<?= $featured['format'] === 'video' ? 'video' : 'photo' ?>"></span> <?= $featured['format'] === 'video' ? 'Video cerita' : 'Foto cerita' ?></span></div>
      <div class="hero-content">
        <span class="category" style="color:#d5f591"><?= e(strtoupper($featured['category_name'])) ?></span>
        <h2><?= e($featured['title']) ?></h2>
        <div class="hero-bottom"><?= kartu_meta($featured) ?><span class="round-arrow" aria-hidden="true">↗</span></div>
      </div>
    </a>
    <div class="hero-side"><?= implode('', array_map('kartu_side', array_slice($side, 0, 2))) ?></div>
  </section>
  <?php endif; ?>
  <div class="ticker"><strong>JELAJAH TOPIK</strong>
    <div class="ticker-items">
      <a href="<?= url('kategori/kuliner') ?>"><span>↗</span> Kuliner lokal</a>
      <a href="<?= url('kategori/komunitas') ?>"><span>↗</span> Cerita komunitas</a>
      <a href="<?= url('kategori/transportasi') ?>"><span>↗</span> Mobilitas warga</a>
    </div>
  </div>
  <div class="home-lower">
    <section>
      <div class="section-head"><h2>Kabar terbaru<span style="color:#91b15a">.</span></h2><a href="<?= url('jelajah') ?>">Semua kabar <span data-icon="arrow"></span></a></div>
      <div class="latest-list"><?= implode('', array_map('kartu_row', array_slice($side, 2, 4))) ?></div>
    </section>
    <aside class="video-panel">
      <div class="eyebrow">Depok dalam frame</div>
      <div class="section-head"><h2>Kota kita,<br>lewat cerita.</h2><span data-icon="video"></span></div>
      <div class="video-cards"><?= implode('', array_map('kartu_video', $videos)) ?></div>
      <a class="video-foot" href="<?= url('video') ?>">Jelajahi video <span data-icon="arrow"></span></a>
      <p class="brand-note"><strong>Setiap sudut punya cerita.</strong><br>Depokin hadir untuk mengenal kota dan orang-orang di dalamnya lebih dekat.</p>
    </aside>
  </div>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>