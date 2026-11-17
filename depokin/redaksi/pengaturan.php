<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_role('admin');

$kunci = ['site_name', 'tagline', 'site_description', 'about_intro', 'about_principles', 'contact_email', 'footer_note', 'instagram_url'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach ($kunci as $k) {
        simpan_setting($k, trim((string)($_POST[$k] ?? '')));
    }
    flash('sukses', 'Pengaturan situs disimpan.');
    redirect('redaksi/pengaturan.php');
}
layout_mulai('Pengaturan situs', 'pengaturan');
?>
<form method="post">
  <?= csrf_field() ?>
  <div class="panel">
    <h2>Identitas</h2>
    <div class="field-row">
      <div class="field"><label for="s-name">Nama situs</label><input id="s-name" type="text" name="site_name" value="<?= e(setting('site_name')) ?>"></div>
      <div class="field"><label for="s-tagline">Tagline</label><input id="s-tagline" type="text" name="tagline" value="<?= e(setting('tagline')) ?>"></div>
    </div>
    <div class="field"><label for="s-desc">Deskripsi situs (meta description beranda)</label><textarea id="s-desc" name="site_description" rows="2"><?= e(setting('site_description')) ?></textarea></div>
    <div class="field"><label for="s-footer">Catatan footer</label><input id="s-footer" type="text" name="footer_note" value="<?= e(setting('footer_note')) ?>"></div>
  </div>
  <div class="panel">
    <h2>Halaman Tentang</h2>
    <div class="field"><label for="s-intro">Paragraf pembuka</label><textarea id="s-intro" name="about_intro" rows="3"><?= e(setting('about_intro')) ?></textarea></div>
    <div class="field"><label for="s-prinsip">Prinsip redaksi (satu per baris)</label><textarea id="s-prinsip" name="about_principles" rows="5"><?= e(setting('about_principles')) ?></textarea></div>
    <div class="field-row">
      <div class="field"><label for="s-email">Email redaksi</label><input id="s-email" type="email" name="contact_email" value="<?= e(setting('contact_email')) ?>"></div>
      <div class="field"><label for="s-ig">URL Instagram (opsional)</label><input id="s-ig" type="url" name="instagram_url" value="<?= e(setting('instagram_url')) ?>"></div>
    </div>
  </div>
  <button class="btn btn-primary" type="submit">Simpan pengaturan</button>
</form>
<?php layout_selesai(); ?>