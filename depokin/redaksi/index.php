<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_login();

$mine = (int)$user['id'];
$stats = [];
if ($user['role'] === 'penulis') {
    $stmt = db()->prepare('SELECT status, COUNT(*) n FROM articles WHERE author_id = ? GROUP BY status');
    $stmt->execute([$mine]);
    $perStatus = array_column($stmt->fetchAll(), 'n', 'status');
    $stats[] = [array_sum($perStatus), 'Total artikel saya', 'doc'];
    $stats[] = [(int)($perStatus['draf'] ?? 0) + (int)($perStatus['revisi'] ?? 0), 'Perlu dikerjakan', 'edit'];
    $stats[] = [(int)($perStatus['ditinjau'] ?? 0), 'Menunggu tinjauan', 'eye'];
    $stats[] = [(int)($perStatus['terbit'] ?? 0), 'Sudah terbit', 'check'];
    $stmt = db()->prepare('SELECT a.*, c.name AS category_name FROM articles a JOIN categories c ON c.id = a.category_id WHERE a.author_id = ? ORDER BY a.updated_at DESC LIMIT 5');
    $stmt->execute([$mine]);
    $recent = $stmt->fetchAll();
} else {
    $stats[] = [(int)db()->query("SELECT COUNT(*) FROM articles WHERE status = 'ditinjau'")->fetchColumn(), 'Antrean tinjauan', 'eye'];
    $stats[] = [(int)db()->query("SELECT COUNT(*) FROM articles WHERE status = 'terbit'")->fetchColumn(), 'Artikel terbit', 'check'];
    $stats[] = [(int)db()->query("SELECT COUNT(*) FROM articles WHERE status = 'terbit' AND published_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetchColumn(), 'Terbit 30 hari terakhir', 'clock'];
    if (is_admin()) {
        $stats[] = [(int)db()->query('SELECT COUNT(*) FROM users WHERE is_active = 1')->fetchColumn(), 'Pengguna aktif', 'users'];
    }
    $recent = db()->query('SELECT a.*, c.name AS category_name FROM articles a JOIN categories c ON c.id = a.category_id ORDER BY a.updated_at DESC LIMIT 5')->fetchAll();
}
$logs = db()->query('SELECT lg.*, u.name AS user_name, a.title AS article_title, a.id AS article_id FROM article_logs lg LEFT JOIN users u ON u.id = lg.user_id JOIN articles a ON a.id = lg.article_id ORDER BY lg.created_at DESC, lg.id DESC LIMIT 8')->fetchAll();

layout_mulai('Dashboard', 'dashboard');
?>
<div class="page-actions">
  <a class="btn btn-primary" href="<?= url('redaksi/artikel-form.php') ?>">+ Tulis artikel</a>
  <?php if (is_editor() && (int)$stats[0][0] > 0): ?><a class="btn btn-outline" href="<?= url('redaksi/tinjauan.php') ?>">Tinjau antrean (<?= (int)$stats[0][0] ?>)</a><?php endif; ?>
</div>
<div class="stat-grid">
  <?php foreach ($stats as [$n, $lbl, $ikon]): ?>
  <div class="stat-card"><span class="stat-ico" data-icon="<?= e($ikon) ?>"></span><div class="num"><?= $n ?></div><div class="lbl"><?= e($lbl) ?></div></div>
  <?php endforeach; ?>
</div>
<div class="panel">
  <h2>Artikel terbaru <a href="<?= url('redaksi/artikel.php') ?>">Semua artikel →</a></h2>
  <?php if ($recent): ?>
  <div class="row-list">
    <?php foreach ($recent as $a): ?>
    <div class="row-item">
      <div class="grow">
        <a class="title" href="<?= url('redaksi/artikel-form.php?id=' . $a['id']) ?>"><?= e($a['title']) ?></a>
        <span class="sub"><?= e($a['category_name']) ?> · diperbarui <?= e(tanggal_id($a['updated_at'], false, true)) ?></span>
      </div>
      <span class="badge <?= e($a['status']) ?>"><?= e(status_label($a['status'])) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php else: ?><p class="empty-state">Belum ada artikel. Mulai dengan tombol “Tulis artikel”.</p><?php endif; ?>
</div>
<div class="panel">
  <h2>Aktivitas redaksi</h2>
  <div class="log-list">
    <?php foreach ($logs as $lg): ?>
    <div class="log-item">
      <b><?= e($lg['user_name'] ?? 'Sistem') ?> · <?= e($lg['action']) ?></b>
      <span><?= e(tanggal_id($lg['created_at'], false, true)) ?> · <a href="<?= url('redaksi/artikel-form.php?id=' . $lg['article_id']) ?>"><?= e(excerpt($lg['article_title'], 60)) ?></a></span>
      <?php if ($lg['note']): ?><p><?= e($lg['note']) ?></p><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php layout_selesai(); ?>