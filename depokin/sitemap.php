<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc><?= e(url()) ?></loc><changefreq>daily</changefreq><priority>1.0</priority></url>
  <url><loc><?= e(url('jelajah')) ?></loc><changefreq>daily</changefreq><priority>0.8</priority></url>
  <url><loc><?= e(url('video')) ?></loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
  <url><loc><?= e(url('tentang')) ?></loc><changefreq>monthly</changefreq><priority>0.4</priority></url>
  <?php foreach (kategori_semua() as $c): if ((int)$c['jumlah'] === 0) continue; ?>
  <url><loc><?= e(url('kategori/' . $c['slug'])) ?></loc><changefreq>weekly</changefreq><priority>0.6</priority></url>
  <?php endforeach; ?>
  <?php foreach (artikel_terbit(1000) as $a): ?>
  <url><loc><?= e(artikel_url($a)) ?></loc><lastmod><?= e(date('Y-m-d', strtotime((string)$a['updated_at']))) ?></lastmod><priority>0.9</priority></url>
  <?php endforeach; ?>
</urlset>