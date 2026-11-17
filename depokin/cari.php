<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';

$q = trim((string)($_GET['q'] ?? ''));
$hal = max(1, (int)($_GET['hal'] ?? 1));
$perPage = 10;
$total = $q !== '' ? artikel_hitung(['q' => $q]) : 0;
$rows = $q !== '' ? artikel_terbit($perPage, ($hal - 1) * $perPage, ['q' => $q]) : [];

$page_title = ($q !== '' ? 'Pencarian: ' . $q : 'Cari cerita') . ' — depokin';
$meta_description = 'Cari kabar dan cerita Depok berdasarkan judul, ringkasan, kategori, dan lokasi.';
$canonical = url('cari');
$active_nav = 'jelajah';
$noindex = true;

require __DIR__ . '/partials/header.php';
?>
<div class="container">
  <header class="page-head">
    <div class="eyebrow">Pencarian</div>
    <h1><?= $q !== '' ? 'Hasil untuk “' . e($q) . '”' : 'Cari cerita di Depok.' ?></h1>
  </header>
  <form class="search-page-field" method="get" action="<?= url('cari') ?>">
    <label class="sr-only" for="q">Kata kunci</label>
    <div class="search-field"><span data-icon="search"></span><input id="q" name="q" type="search" value="<?= e($q) ?>" placeholder="Kuliner, Margonda, komunitas…" autocomplete="off"><button type="submit">Cari</button></div>
  </form>
  <?php if ($q === ''): ?>
  <p class="search-hint">Ketik kata kunci untuk mencari judul, ringkasan, isi, kategori, dan lokasi liputan.</p>
  <?php elseif ($rows): ?>
  <p class="result-count" aria-live="polite"><?= $total ?> cerita ditemukan</p>
  <div class="latest-list"><?= implode('', array_map('kartu_row', $rows)) ?></div>
  <?= render_paginasi($hal, $total, $perPage, url('cari') . '?q=' . urlencode($q)) ?>
  <?php else: ?>
  <p class="empty-state">Belum ada cerita yang cocok dengan “<?= e($q) ?>”. Coba kata “kuliner”, “komunitas”, atau “Depok”.</p>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>