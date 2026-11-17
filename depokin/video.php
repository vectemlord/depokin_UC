<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';
require_once __DIR__ . '/partials/detail.php';

$slug = trim((string)($_GET['slug'] ?? ''));

if ($slug === '') {
    $hal = max(1, (int)($_GET['hal'] ?? 1));
    $perPage = 9;
    $f = ['format' => 'video'];
    $total = artikel_hitung($f);
    $rows = artikel_terbit($perPage, ($hal - 1) * $perPage, $f);
    $page_title = 'Video — depokin';
    $meta_description = 'Cerita bergerak dari Depok: liputan video pendek tentang kota, orang, dan tempat.';
    $canonical = url('video');
    $active_nav = 'video';
    $noindex = $hal > 1;
    require __DIR__ . '/partials/header.php';
    ?>
    <div class="container">
      <header class="page-head">
        <div class="eyebrow">Depok dalam frame</div>
        <h1>Cerita yang bergerak.</h1>
        <p>Potongan kehidupan kota, orang-orang, dan tempat yang menarik untuk dikenal.</p>
      </header>
      <p class="result-count" aria-live="polite"><?= $total ?> video</p>
      <?php if ($rows): ?>
      <div class="story-grid"><?= implode('', array_map('kartu_grid', $rows)) ?></div>
      <?= render_paginasi($hal, $total, $perPage, url('video')) ?>
      <?php else: ?>
      <p class="empty-state">Belum ada video terbit.</p>
      <?php endif; ?>
    </div>
    <?php
    require __DIR__ . '/partials/footer.php';
    exit;
}

$a = artikel_by_slug($slug);
if (!$a || $a['format'] !== 'video') {
    http_response_code(404);
    $page_title = 'Video tidak ditemukan — depokin';
    $active_nav = 'video';
    $noindex = true;
    require __DIR__ . '/partials/header.php';
    echo '<div class="container"><header class="page-head"><h1>Video belum tersedia.</h1><p><a href="' . url('video') . '">Kembali ke daftar video →</a></p></header></div>';
    require __DIR__ . '/partials/footer.php';
    exit;
}

$media = artikel_media((int)$a['id']);
$video = null;
$utama = null;
foreach ($media as $m) {
    if ($m['kind'] === 'video' && !$video) $video = $m;
    if ($m['kind'] === 'gambar' && $m['is_primary'] && !$utama) $utama = $m;
}
$poster = media_url($video['poster_path'] ?? '') ?: media_url($utama['file_path'] ?? '');
$yt = youtube_id($video['embed_url'] ?? null);
$durasi = (int)($video['duration_seconds'] ?? 0);
$related = artikel_terkait($a, 2);

$page_title = ($a['meta_title'] ?: $a['title']) . ' — depokin';
$meta_description = $a['meta_description'] ?: $a['deck'];
$canonical = url('video/' . $a['slug']);
$active_nav = 'video';
$og = ['type' => 'video.other', 'title' => $a['title'], 'description' => $a['deck'], 'image' => $poster ?: null];
$jsonld = json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'VideoObject',
    'name' => $a['title'],
    'description' => $a['deck'],
    'thumbnailUrl' => $poster ? [$poster] : null,
    'uploadDate' => date('c', strtotime((string)$a['published_at'])),
    'duration' => $durasi ? 'PT' . $durasi . 'S' : null,
    'embedUrl' => $yt ? 'https://www.youtube-nocookie.com/embed/' . $yt : null,
    'contentUrl' => (!$yt && !empty($video['file_path'])) ? media_url($video['file_path']) : null,
    'inLanguage' => 'id',
    'publisher' => ['@type' => 'Organization', 'name' => setting('site_name', 'depokin')],
]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

require __DIR__ . '/partials/header.php';
?>
<article class="article-shell">
  <nav class="breadcrumb" aria-label="Breadcrumb"><a href="<?= url() ?>">Beranda</a><span>/</span><a href="<?= url('video') ?>">Video</a><span>/</span><span><?= e(excerpt($a['title'], 40)) ?></span></nav>
  <header class="article-head">
    <span class="pill-label">VIDEO · <?= e(strtoupper($a['category_name'])) ?></span>
    <h1><?= e($a['title']) ?></h1>
    <p class="article-deck"><?= e($a['deck']) ?></p>
    <?= detail_byline($a, true) ?>
  </header>
  <?php if ($yt): ?>
  <div class="video-embed"><iframe src="https://www.youtube-nocookie.com/embed/<?= e($yt) ?>" title="<?= e($a['title']) ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"></iframe></div>
  <?php elseif (!empty($video['file_path'])): ?>
  <div class="video-embed"><video controls preload="metadata" <?= $poster ? 'poster="' . e($poster) . '"' : '' ?>><source src="<?= e(media_url($video['file_path'])) ?>">Browser Anda tidak mendukung pemutar video.</video></div>
  <?php else: ?>
  <div class="video-placeholder">
    <?php if ($poster): ?><img src="<?= e($poster) ?>" alt="" aria-hidden="true"><?php endif; ?>
    <div class="placeholder-copy"><span data-icon="video"></span><strong>Ruang untuk video liputan</strong><p>Poster konsep<?= $durasi ? ' · ' . e(durasi_video($durasi)) : '' ?><br>File video belum dilampirkan.</p></div>
  </div>
  <?php endif; ?>
  <?php if ($utama): ?><figcaption class="video-credit"><?= detail_figcaption($utama) ?></figcaption><?php endif; ?>
  <div class="article-body">
    <?= detail_transkrip($video ?? []) ?>
    <?= detail_event($a) ?>
    <?= $a['content'] ?>
    <?= detail_tags((int)$a['id']) ?>
    <?= detail_koreksi((int)$a['id']) ?>
  </div>
  <section class="related">
    <div class="section-head"><h2>Cerita lainnya</h2><a href="<?= url('video') ?>">Video lain <span data-icon="arrow"></span></a></div>
    <div class="story-grid"><?= implode('', array_map('kartu_grid', $related)) ?></div>
  </section>
</article>
<?php require __DIR__ . '/partials/footer.php'; ?>