<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_login();

$status = (string)($_GET['status'] ?? '');
$q = trim((string)($_GET['q'] ?? ''));
$hal = max(1, (int)($_GET['hal'] ?? 1));
$perPage = 15;

$where = [];
$params = [];
if ($user['role'] === 'penulis') { $where[] = 'a.author_id = ?'; $params[] = (int)$user['id']; }
if (in_array($status, ['draf', 'ditinjau', 'revisi', 'terbit', 'arsip'], true)) { $where[] = 'a.status = ?'; $params[] = $status; }
if ($q !== '') { $where[] = 'a.title LIKE ?'; $params[] = '%' . $q . '%'; }
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$stmt = db()->prepare('SELECT COUNT(*) FROM articles a' . $whereSql);
$stmt->execute($params);
$total = (int)$stmt->fetchColumn();

$stmt = db()->prepare('SELECT a.*, c.name AS category_name, u.name AS author_name FROM articles a JOIN categories c ON c.id = a.category_id JOIN users u ON u.id = a.author_id' . $whereSql . ' ORDER BY a.updated_at DESC LIMIT ' . $perPage . ' OFFSET ' . (($hal - 1) * $perPage));
$stmt->execute($params);
$rows = $stmt->fetchAll();

$baseUrl = url('redaksi/artikel.php') . '?' . http_build_query(array_filter(['status' => $status, 'q' => $q]));
layout_mulai('Artikel', 'artikel');
?>
<div class="page-actions">
  <a class="btn btn-primary" href="<?= url('redaksi/artikel-form.php') ?>">+ Tulis artikel</a>
  <div class="spacer"></div>
  <form method="get" style="display:flex;gap:8px">
    <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
    <input type="search" name="q" value="<?= e($q) ?>" placeholder="Cari judul…" aria-label="Cari judul" style="border:1px solid var(--line);border-radius:12px;padding:10px 14px;font-size:13px;background:var(--white)">
    <button class="btn btn-outline btn-sm" type="submit">Cari</button>
  </form>
</div>
<nav class="tabs" aria-label="Filter status">
  <a class="<?= $status === '' ? 'active' : '' ?>" href="<?= url('redaksi/artikel.php') ?>">Semua</a>
  <?php foreach (['draf', 'ditinjau', 'revisi', 'terbit', 'arsip'] as $s): ?>
  <a class="<?= $status === $s ? 'active' : '' ?>" href="<?= url('redaksi/artikel.php?status=' . $s) ?>"><?= e(status_label($s)) ?></a>
  <?php endforeach; ?>
</nav>
<div class="panel">
  <?php if ($rows): ?>
  <div class="row-list">
    <?php foreach ($rows as $a): ?>
    <div class="row-item">
      <div class="grow">
        <span class="title"><?= e($a['title']) ?></span>
        <span class="sub"><?= e($a['category_name']) ?> · <?= e($a['author_name']) ?> · <?= e(tanggal_id($a['updated_at'], false, true)) ?><?= $a['is_featured'] ? ' · ★ pilihan redaksi' : '' ?></span>
      </div>
      <span class="badge <?= e($a['status']) ?>"><?= e(status_label($a['status'])) ?></span>
      <div class="actions">
        <?php if (boleh_edit($a, $user)): ?><a class="btn btn-outline btn-sm" href="<?= url('redaksi/artikel-form.php?id=' . $a['id']) ?>">Edit</a><?php endif; ?>
        <a class="btn btn-outline btn-sm" href="<?= url('redaksi/pratinjau.php?id=' . $a['id']) ?>">Pratinjau</a>
        <?php if ($a['status'] === 'terbit'): ?><a class="btn btn-outline btn-sm" href="<?= url(($a['format'] === 'video' ? 'video/' : 'berita/') . $a['slug']) ?>" target="_blank" rel="noreferrer">Lihat</a><?php endif; ?>
        <?php if (boleh_hapus($a, $user)): ?>
        <form method="post" action="<?= url('redaksi/artikel-aksi.php') ?>" data-confirm="Hapus artikel ini beserta medianya? Tindakan tidak dapat dibatalkan.">
          <?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><input type="hidden" name="kembali" value="artikel.php">
          <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?= render_paginasi($hal, $total, $perPage, $baseUrl) ?>
  <?php else: ?><p class="empty-state">Tidak ada artikel untuk filter ini.</p><?php endif; ?>
</div>
<?php layout_selesai(); ?>