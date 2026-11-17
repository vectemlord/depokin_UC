<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../partials/kartu.php';
require_once __DIR__ . '/../partials/detail.php';
$user = require_login();

$id = (int)($_GET['id'] ?? 0);
$a = $id ? artikel_by_id($id) : null;
if (!$a) { http_response_code(404); exit('Artikel tidak ditemukan.'); }
if (!boleh_edit($a, $user) && !is_editor()) { http_response_code(403); exit('Tidak diizinkan melihat pratinjau artikel ini.'); }

$media = artikel_media($id);
$utama = null; $video = null; $galeri = [];
foreach ($media as $m) {
    if ($m['kind'] === 'video') { $video = $video ?: $m; continue; }
    if ($m['is_primary'] && !$utama) $utama = $m; else $galeri[] = $m;
}
$yt = youtube_id($video['embed_url'] ?? null);
$poster = media_url($video['poster_path'] ?? '') ?: media_url($utama['file_path'] ?? '');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title>Pratinjau: <?= e($a['title']) ?> — depokin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= url('styles.css') ?>">
</head>
<body>
<div class="preview-bar"><strong>PRATINJAU · <?= e(status_label($a['status'])) ?></strong><span>Belum tampil publik<?= $a['status'] === 'terbit' ? ' (sudah terbit)' : '' ?>.</span><a href="<?= url('redaksi/artikel-form.php?id=' . $id) ?>">← Kembali edit</a></div>
<article class="article-shell">
  <header class="article-head">
    <span class="pill-label"><?= e(strtoupper($a['category_name'])) ?></span>
    <h1><?= e($a['title']) ?></h1>
    <p class="article-deck"><?= e($a['deck']) ?></p>
    <?= detail_byline($a, $a['format'] === 'video') ?>
  </header>
  <?php if ($a['format'] === 'video'): ?>
    <?php if ($yt): ?>
    <div class="video-embed"><iframe src="https://www.youtube-nocookie.com/embed/<?= e($yt) ?>" title="<?= e($a['title']) ?>" allowfullscreen></iframe></div>
    <?php elseif (!empty($video['file_path'])): ?>
    <div class="video-embed"><video controls preload="metadata" <?= $poster ? 'poster="' . e($poster) . '"' : '' ?>><source src="<?= e(media_url($video['file_path'])) ?>"></video></div>
    <?php else: ?>
    <div class="video-placeholder"><?php if ($poster): ?><img src="<?= e($poster) ?>" alt="" aria-hidden="true"><?php endif; ?><div class="placeholder-copy"><strong>Ruang untuk video liputan</strong><p>File video belum dilampirkan.</p></div></div>
    <?php endif; ?>
  <?php elseif ($utama): ?>
    <?= detail_figure($utama) ?>
  <?php endif; ?>
  <div class="article-body">
    <?= detail_transkrip($video ?? []) ?>
    <?= detail_event($a) ?>
    <?= $a['content'] ?>
    <?php foreach ($galeri as $g): ?><?= detail_figure($g, true) ?><?php endforeach; ?>
    <?= detail_tags($id) ?>
    <?= detail_koreksi($id) ?>
  </div>
</article>
<script src="<?= url('assets/app.js') ?>"></script>
</body>
</html>