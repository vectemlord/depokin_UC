<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_role('admin', 'editor');

$rows = db()->query("SELECT a.*, c.name AS category_name, u.name AS author_name,
    (SELECT MAX(lg.created_at) FROM article_logs lg WHERE lg.article_id = a.id AND lg.action = 'submit') AS submitted_at
  FROM articles a JOIN categories c ON c.id = a.category_id JOIN users u ON u.id = a.author_id
  WHERE a.status = 'ditinjau' ORDER BY submitted_at ASC, a.updated_at ASC")->fetchAll();
$revisi = db()->query("SELECT a.*, u.name AS author_name FROM articles a JOIN users u ON u.id = a.author_id WHERE a.status = 'revisi' ORDER BY a.updated_at DESC LIMIT 10")->fetchAll();

layout_mulai('Tinjauan', 'tinjauan');
?>
<div class="panel">
  <h2>Antrean tinjauan (<?= count($rows) ?>)</h2>
  <?php if ($rows): ?>
  <div class="row-list">
    <?php foreach ($rows as $a): ?>
    <div class="row-item">
      <div class="grow">
        <span class="title"><?= e($a['title']) ?></span>
        <span class="sub"><?= e($a['category_name']) ?> · <?= e($a['author_name']) ?> · dikirim <?= $a['submitted_at'] ? e(tanggal_id($a['submitted_at'], false, true)) : '—' ?></span>
      </div>
      <div class="actions">
        <a class="btn btn-outline btn-sm" href="<?= url('redaksi/artikel-form.php?id=' . $a['id']) ?>">Tinjau &amp; edit</a>
        <form method="post" action="<?= url('redaksi/artikel-aksi.php') ?>">
          <?= csrf_field() ?><input type="hidden" name="aksi" value="terbit"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><input type="hidden" name="kembali" value="tinjauan.php">
          <button class="btn btn-lime btn-sm" type="submit">Terbitkan</button>
        </form>
        <button class="btn btn-outline btn-sm" type="button" data-revisi="<?= (int)$a['id'] ?>" data-kembali="tinjauan.php">Minta revisi</button>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?><p class="empty-state">Tidak ada artikel yang menunggu tinjauan. Kerja bagus.</p><?php endif; ?>
</div>
<div class="panel">
  <h2>Sedang direvisi penulis</h2>
  <?php if ($revisi): ?>
  <div class="row-list">
    <?php foreach ($revisi as $a): ?>
    <div class="row-item">
      <div class="grow"><span class="title"><?= e($a['title']) ?></span><span class="sub"><?= e($a['author_name']) ?> · <?= e(tanggal_id($a['updated_at'], false, true)) ?></span></div>
      <span class="badge revisi">Perlu revisi</span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?><p class="empty-state">Tidak ada artikel dalam status revisi.</p><?php endif; ?>
</div>
<dialog id="revisi-dialog" aria-labelledby="revisi-title">
  <div class="dialog-head"><h2 id="revisi-title">Minta revisi</h2><button class="dialog-close" type="button" data-close aria-label="Tutup"><span data-icon="close"></span></button></div>
  <form method="post" action="<?= url('redaksi/artikel-aksi.php') ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="aksi" value="revisi"><input type="hidden" name="id" value=""><input type="hidden" name="kembali" value="tinjauan.php">
    <div class="field"><label for="revisi-note">Catatan untuk penulis (wajib)</label><textarea id="revisi-note" name="catatan" rows="4" required placeholder="Bagian mana yang perlu diperbaiki dan mengapa…"></textarea></div>
    <div class="dialog-actions"><button class="btn btn-outline" type="button" data-close>Batal</button><button class="btn btn-primary" type="submit">Kirim catatan revisi</button></div>
  </form>
</dialog>
<?php layout_selesai(); ?>