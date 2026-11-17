<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';

$lokasi = trim((string)($_GET['lokasi'] ?? ''));
$tag = trim((string)($_GET['tag'] ?? ''));
$hal = max(1, (int)($_GET['hal'] ?? 1));
$perPage = 9;

$f = ['format' => 'foto'];
if ($lokasi !== '') $f['lokasi'] = $lokasi;
if ($tag !== '') $f['tag'] = $tag;

$total = artikel_hitung($f);
$rows = artikel_terbit($perPage, ($hal - 1) * $perPage, $f);
$lokasiList = lokasi_semua();
$tagAktif = null;
if ($tag !== '') {
    foreach (tags_semua() as $t) if ($t['slug'] === $tag) $tagAktif = $t;
}

$page_title = 'Jelajah Depok — depokin';
$meta_description = 'Temukan kabar dan cerita Depok berdasarkan kategori, lokasi liputan, dan topik.';
$canonical = url('jelajah');
$active_nav = 'jelajah';
$noindex = $lokasi !== '' || $tag !== '' || $hal > 1;

require __DIR__ . '/partials/header.php';
?>
<div class="container">
  <header class="page-head">
    <div class="eyebrow">Temukan cerita di sekitarmu</div>
    <h1>Jelajah Depok.</h1>
    <p>Pilih topik yang dekat denganmu. Dari kabar sehari-hari sampai cerita yang membuat kita mengenal kota sendiri.</p>
  </header>
  <div class="filter-bar">
    <?= chips_kategori() ?>
    <form class="location-filter" method="get" action="<?= url('jelajah') ?>">
      <?php if ($tag !== ''): ?><input type="hidden" name="tag" value="<?= e($tag) ?>"><?php endif; ?>
      <label class="sr-only" for="filter-lokasi">Filter lokasi liputan</label>
      <select id="filter-lokasi" name="lokasi" onchange="this.form.submit()">
        <option value="">Semua lokasi</option>
        <?php foreach ($lokasiList as $l): ?>
        <option value="<?= e($l['slug']) ?>" <?= $lokasi === $l['slug'] ? 'selected' : '' ?>><?= e($l['name']) ?> (<?= (int)$l['jumlah'] ?>)</option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <?php if ($tagAktif): ?><p class="result-count">Topik: <strong><?= e($tagAktif['name']) ?></strong> · <a href="<?= url('jelajah') ?>">hapus filter</a></p><?php endif; ?>
  <p class="result-count" aria-live="polite"><?= $total ?> cerita<?= $lokasi !== '' ? ' di lokasi terpilih' : '' ?></p>
  <?php if ($rows): ?>
  <div class="story-grid"><?= implode('', array_map('kartu_grid', $rows)) ?></div>
  <?= render_paginasi($hal, $total, $perPage, url('jelajah') . ($lokasi !== '' || $tag !== '' ? '?' . http_build_query(array_filter(['lokasi' => $lokasi, 'tag' => $tag])) : '')) ?>
  <?php else: ?>
  <p class="empty-state">Belum ada cerita untuk filter ini. Coba lokasi lain atau kembali ke <a href="<?= url('jelajah') ?>">semua cerita</a>.</p>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>