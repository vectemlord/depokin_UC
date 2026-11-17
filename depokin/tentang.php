<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/partials/kartu.php';

$prinsip = array_filter(array_map('trim', explode("\n", setting('about_principles'))));
$kredit = db()->query('SELECT DISTINCT credit_name, credit_url, license, license_url FROM media WHERE credit_name IS NOT NULL AND credit_name != \'\' ORDER BY credit_name')->fetchAll();

$page_title = 'Tentang kami — depokin';
$meta_description = 'Identitas media, prinsip redaksi, kontak, dan kredit foto Depokin.';
$canonical = url('tentang');
$active_nav = 'tentang';

require __DIR__ . '/partials/header.php';
?>
<div class="container">
  <header class="page-head">
    <div class="eyebrow">Cerita kota kita</div>
    <h1>Lebih dekat dengan Depok.</h1>
    <p><?= e(setting('tagline')) ?></p>
  </header>
  <section class="about-layout">
    <div class="about-mark">
      <span class="brand">depokin.</span>
      <h2>Dekat kotanya.<br>Dekat ceritanya.</h2>
      <span>DEPOK, DARI DEKAT.</span>
    </div>
    <div class="about-copy">
      <h2>Berangkat dari sekitar kita.</h2>
      <p><?= e(setting('about_intro')) ?></p>
      <h2>Prinsip redaksi</h2>
      <ul><?php foreach ($prinsip as $p): ?><li><?= e($p) ?></li><?php endforeach; ?></ul>
      <?php if (setting('contact_email')): ?>
      <h2>Kontak redaksi</h2>
      <p>Pertanyaan, koreksi, dan kerja sama: <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a><?php if (setting('instagram_url')): ?> · <a href="<?= e(setting('instagram_url')) ?>" target="_blank" rel="noreferrer">Instagram ↗</a><?php endif; ?></p>
      <?php endif; ?>
      <h2>Kredit foto &amp; media</h2>
      <ul class="credit-list">
        <?php foreach ($kredit as $k): ?>
        <li><?php if ($k['credit_url']): ?><a href="<?= e($k['credit_url']) ?>" target="_blank" rel="noreferrer"><?= e($k['credit_name']) ?> ↗</a><?php else: ?><?= e($k['credit_name']) ?><?php endif; ?><?php if ($k['license']): ?> — <?php if ($k['license_url']): ?><a href="<?= e($k['license_url']) ?>" target="_blank" rel="noreferrer"><?= e($k['license']) ?></a><?php else: ?><?= e($k['license']) ?><?php endif; ?><?php endif; ?></li>
        <?php endforeach; ?>
      </ul>
      <p class="sample-note">Konten contoh untuk pengembangan. Foto arsip bukan bukti peristiwa terkini; foto ilustrasi tidak mendokumentasikan lokasi atau peristiwa dalam berita.</p>
    </div>
  </section>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>