<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$kategori = null;
foreach (kategori_semua() as $c) if ($c['slug'] === $slug) $kategori = $c;

if (!$kategori) {
    http_response_code(404);
    $page_title = 'Kategori tidak ditemukan — depokin';
    $active_nav = 'jelajah';
    $noindex = true;
    require __DIR__ . '/partials/header.php';
    echo '<div class="container"><header class="page-head"><h1>Kategori belum tersedia.</h1><p><a href="' . url('jelajah') . '">Kembali ke Jelajah Depok →</a></p></header></div>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

$hal = max(1, (int)($_GET['hal'] ?? 1));
$perPage = 9;
$f = ['kategori' => $slug];
$total = artikel_hitung($f);
$rows = artikel_terbit($perPage, ($hal - 1) * $perPage, $f);

$page_title = $kategori['name'] . ' — depokin';
$meta_description = $kategori['description'] ?: ('Kumpulan cerita dan kabar rubrik ' . $kategori['name'] . ' di Depok.');
$canonical = url('kategori/' . $slug);
$active_nav = 'jelajah';
$noindex = $hal > 1;

require __DIR__ . '/partials/header.php';
?>
<div class="container">
  <header class="page-head">
    <div class="eyebrow">Rubrik</div>
    <h1><?= e($kategori['name']) ?>.</h1>
    <p><?= e($kategori['description']) ?></p>
  </header>
  <?= chips_kategori($slug) ?>
  <p class="result-count" aria-live="polite"><?= $total ?> cerita</p>
  <?php if ($rows): ?>
  <div class="story-grid"><?= implode('', array_map('kartu_grid', $rows)) ?></div>
  <?= render_paginasi($hal, $total, $perPage, url('kategori/' . $slug)) ?>
  <?php else: ?>
  <p class="empty-state">Belum ada cerita terbit di rubrik ini.</p>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>