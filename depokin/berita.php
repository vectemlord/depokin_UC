<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';
require_once __DIR__ . '/partials/detail.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$a = $slug !== '' ? artikel_by_slug($slug) : null;
if ($a && $a['format'] === 'video') redirect('video/' . $a['slug']);

if (!$a) {
    http_response_code(404);
    $page_title = 'Cerita tidak ditemukan — depokin';
    $active_nav = 'beranda';
    $noindex = true;
    require __DIR__ . '/partials/header.php';
    echo '<div class="container"><header class="page-head"><h1>Cerita belum tersedia.</h1><p><a href="' . url() . '">Kembali ke beranda →</a></p></header></div>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

$media = artikel_media((int)$a['id']);
$utama = null;
$galeri = [];
foreach ($media as $m) {
    if ($m['kind'] !== 'gambar') continue;
    if ($m['is_primary'] && !$utama) $utama = $m;
    else $galeri[] = $m;
}
$related = artikel_terkait($a, 2);

$page_title = ($a['meta_title'] ?: $a['title']) . ' — depokin';
$meta_description = $a['meta_description'] ?: $a['deck'];
$canonical = url('berita/' . $a['slug']);
$active_nav = 'beranda';
$og = ['type' => 'article', 'title' => $a['title'], 'description' => $a['deck'], 'image' => $utama ? media_url($utama['file_path']) : null];
$jsonld = json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'NewsArticle',
    'headline' => $a['title'],
    'description' => $a['deck'],
    'datePublished' => date('c', strtotime((string)$a['published_at'])),
    'dateModified' => date('c', strtotime((string)$a['updated_at'])),
    'inLanguage' => 'id',
    'author' => ['@type' => 'Person', 'name' => $a['author_name']],
    'editor' => $a['editor_name'] ? ['@type' => 'Person', 'name' => $a['editor_name']] : null,
    'image' => $utama ? [media_url($utama['file_path'])] : null,
    'publisher' => ['@type' => 'Organization', 'name' => setting('site_name', 'depokin')],
    'mainEntityOfPage' => $canonical,
    'articleSection' => $a['category_name'],
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

require __DIR__ . '/partials/header.php';
?>
<article class="article-shell">
  <nav class="breadcrumb" aria-label="Breadcrumb"><a href="<?= url() ?>">Beranda</a><span>/</span><a href="<?= url('kategori/' . $a['category_slug']) ?>"><?= e($a['category_name']) ?></a><span>/</span><span><?= e(excerpt($a['title'], 40)) ?></span></nav>
  <header class="article-head">
    <span class="pill-label"><?= e(strtoupper($a['category_name'])) ?></span>
    <h1><?= e($a['title']) ?></h1>
    <p class="article-deck"><?= e($a['deck']) ?></p>
    <?= detail_byline($a) ?>
  </header>
  <?= $utama ? detail_figure($utama) : '' ?>
  <div class="article-body">
    <?= detail_event($a) ?>
    <?= $a['content'] ?>
    <?php foreach ($galeri as $g): ?>
    <?= detail_figure($g, true) ?>
    <?php endforeach; ?>
    <?= detail_tags((int)$a['id']) ?>
    <?= detail_koreksi((int)$a['id']) ?>
  </div>
  <section class="related">
    <div class="section-head"><h2>Cerita lainnya</h2><a href="<?= url('jelajah') ?>">Jelajahi <span data-icon="arrow"></span></a></div>
    <div class="story-grid"><?= implode('', array_map('kartu_grid', $related)) ?></div>
  </section>
</article>
<?php require __DIR__ . '/partials/footer.php'; ?>