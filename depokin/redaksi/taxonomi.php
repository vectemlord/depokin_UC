<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_role('admin', 'editor');

$tab = (string)($_GET['tab'] ?? 'kategori');
if (!in_array($tab, ['kategori', 'lokasi', 'tag'], true)) $tab = 'kategori';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $tipe = (string)($_POST['tipe'] ?? '');
    $aksi = (string)($_POST['aksi'] ?? '');
    $tid = (int)($_POST['id'] ?? 0);
    $pdo = db();
    $tabel = ['kategori' => 'categories', 'lokasi' => 'locations', 'tag' => 'tags'][$tipe] ?? null;
    if (!$tabel) { flash('error', 'Tipe tidak dikenal.'); redirect('redaksi/taxonomi.php'); }
    $tab = $tipe;

    if ($aksi === 'hapus') {
        if ($tipe === 'tag') $stmt = $pdo->prepare('SELECT COUNT(*) FROM article_tags WHERE tag_id = ?');
        else {
            $kolom = $tipe === 'kategori' ? 'category_id' : 'location_id';
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE {$kolom} = ?");
        }
        $stmt->execute([$tid]);
        if ((int)$stmt->fetchColumn() > 0) {
            flash('error', 'Tidak dapat dihapus: masih dipakai artikel. Pindahkan artikel terlebih dahulu.');
        } else {
            try {
                $pdo->prepare("DELETE FROM {$tabel} WHERE id = ?")->execute([$tid]);
                flash('sukses', 'Berhasil dihapus.');
            } catch (PDOException) {
                flash('error', 'Tidak dapat dihapus karena masih dipakai data lain.');
            }
        }
        redirect('redaksi/taxonomi.php?tab=' . $tab);
    }

    $nama = trim((string)($_POST['name'] ?? ''));
    $slugIn = trim((string)($_POST['slug'] ?? ''));
    $desc = trim((string)($_POST['description'] ?? '')) ?: null;
    $kind = (string)($_POST['kind'] ?? 'area');
    if ($nama === '') $errors[] = 'Nama wajib diisi.';
    if ($tipe === 'lokasi' && !in_array($kind, ['kecamatan', 'kelurahan', 'area'], true)) $errors[] = 'Jenis lokasi tidak valid.';
    if (!$errors) {
        $slug = unique_slug($tabel, $slugIn !== '' ? slugify($slugIn) : slugify($nama), $tid ?: null);
        if ($tid) {
            if ($tipe === 'kategori') $pdo->prepare('UPDATE categories SET name=?, slug=?, description=? WHERE id=?')->execute([$nama, $slug, $desc, $tid]);
            elseif ($tipe === 'lokasi') $pdo->prepare('UPDATE locations SET name=?, slug=?, kind=? WHERE id=?')->execute([$nama, $slug, $kind, $tid]);
            else $pdo->prepare('UPDATE tags SET name=?, slug=? WHERE id=?')->execute([$nama, $slug, $tid]);
            flash('sukses', 'Perubahan disimpan.');
        } else {
            if ($tipe === 'kategori') $pdo->prepare('INSERT INTO categories (name, slug, description) VALUES (?,?,?)')->execute([$nama, $slug, $desc]);
            elseif ($tipe === 'lokasi') $pdo->prepare('INSERT INTO locations (name, slug, kind) VALUES (?,?,?)')->execute([$nama, $slug, $kind]);
            else $pdo->prepare('INSERT INTO tags (name, slug) VALUES (?,?)')->execute([$nama, $slug]);
            flash('sukses', 'Berhasil ditambahkan.');
        }
        redirect('redaksi/taxonomi.php?tab=' . $tab);
    }
}

$kategori = kategori_semua();
$lokasi = lokasi_semua();
$tags = db()->query('SELECT t.*, (SELECT COUNT(*) FROM article_tags at2 WHERE at2.tag_id = t.id) AS jumlah FROM tags t ORDER BY t.name')->fetchAll();
layout_mulai('Kategori & Lokasi', 'taxonomi');
?>
<?php if ($errors): ?><div class="flash error" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
<nav class="tabs" aria-label="Jenis taksonomi">
  <a class="<?= $tab === 'kategori' ? 'active' : '' ?>" href="<?= url('redaksi/taxonomi.php?tab=kategori') ?>">Kategori</a>
  <a class="<?= $tab === 'lokasi' ? 'active' : '' ?>" href="<?= url('redaksi/taxonomi.php?tab=lokasi') ?>">Lokasi</a>
  <a class="<?= $tab === 'tag' ? 'active' : '' ?>" href="<?= url('redaksi/taxonomi.php?tab=tag') ?>">Tag</a>
</nav>

<?php if ($tab === 'kategori'): ?>
<div class="page-actions"><button class="btn btn-primary" type="button" data-modal="kat-dialog">+ Tambah kategori</button></div>
<div class="panel"><div class="row-list">
  <?php foreach ($kategori as $c): ?>
  <div class="row-item">
    <div class="grow"><span class="title"><?= e($c['name']) ?></span><span class="sub">/kategori/<?= e($c['slug']) ?> · <?= (int)$c['jumlah'] ?> artikel terbit<?= $c['description'] ? ' · ' . e($c['description']) : '' ?></span></div>
    <div class="actions">
      <button class="btn btn-outline btn-sm" type="button" data-modal="kat-dialog" data-id="<?= (int)$c['id'] ?>" data-name="<?= e($c['name']) ?>" data-slug="<?= e($c['slug']) ?>" data-description="<?= e($c['description'] ?? '') ?>">Edit</button>
      <form method="post" data-confirm="Hapus kategori ini?"><?= csrf_field() ?><input type="hidden" name="tipe" value="kategori"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Hapus</button></form>
    </div>
  </div>
  <?php endforeach; ?>
</div></div>
<dialog id="kat-dialog" aria-labelledby="kat-title">
  <div class="dialog-head"><h2 id="kat-title" data-dialog-title data-create="Tambah kategori" data-edit="Edit kategori">Tambah kategori</h2><button class="dialog-close" type="button" data-close aria-label="Tutup"><span data-icon="close"></span></button></div>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="tipe" value="kategori"><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="">
    <div class="field"><label for="k-nama">Nama kategori</label><input id="k-nama" type="text" name="name" data-fill="name" required></div>
    <div class="field"><label for="k-slug">Slug</label><input id="k-slug" type="text" name="slug" data-fill="slug"><p class="hint">Kosongkan untuk otomatis dari nama.</p></div>
    <div class="field"><label for="k-desc">Deskripsi rubrik</label><textarea id="k-desc" name="description" rows="2" data-fill="description"></textarea></div>
    <div class="dialog-actions"><button class="btn btn-outline" type="button" data-close>Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
  </form>
</dialog>

<?php elseif ($tab === 'lokasi'): ?>
<div class="page-actions"><button class="btn btn-primary" type="button" data-modal="lok-dialog">+ Tambah lokasi</button></div>
<div class="panel"><div class="row-list">
  <?php foreach ($lokasi as $l): ?>
  <div class="row-item">
    <div class="grow"><span class="title"><?= e($l['name']) ?></span><span class="sub"><?= e($l['kind']) ?> · <?= (int)$l['jumlah'] ?> artikel terbit</span></div>
    <div class="actions">
      <button class="btn btn-outline btn-sm" type="button" data-modal="lok-dialog" data-id="<?= (int)$l['id'] ?>" data-name="<?= e($l['name']) ?>" data-slug="<?= e($l['slug']) ?>" data-kind="<?= e($l['kind']) ?>">Edit</button>
      <form method="post" data-confirm="Hapus lokasi ini?"><?= csrf_field() ?><input type="hidden" name="tipe" value="lokasi"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Hapus</button></form>
    </div>
  </div>
  <?php endforeach; ?>
</div></div>
<dialog id="lok-dialog" aria-labelledby="lok-title">
  <div class="dialog-head"><h2 id="lok-title" data-dialog-title data-create="Tambah lokasi" data-edit="Edit lokasi">Tambah lokasi</h2><button class="dialog-close" type="button" data-close aria-label="Tutup"><span data-icon="close"></span></button></div>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="tipe" value="lokasi"><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="">
    <div class="field"><label for="l-nama">Nama lokasi</label><input id="l-nama" type="text" name="name" data-fill="name" required></div>
    <div class="field"><label for="l-jenis">Jenis</label><select id="l-jenis" name="kind" data-fill="kind"><option value="area">Area</option><option value="kecamatan">Kecamatan</option><option value="kelurahan">Kelurahan</option></select></div>
    <div class="field"><label for="l-slug">Slug</label><input id="l-slug" type="text" name="slug" data-fill="slug"></div>
    <div class="dialog-actions"><button class="btn btn-outline" type="button" data-close>Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
  </form>
</dialog>

<?php else: ?>
<div class="page-actions"><button class="btn btn-primary" type="button" data-modal="tag-dialog">+ Tambah tag</button></div>
<div class="panel"><div class="row-list">
  <?php foreach ($tags as $t): ?>
  <div class="row-item">
    <div class="grow"><span class="title">#<?= e($t['name']) ?></span><span class="sub"><?= (int)$t['jumlah'] ?> artikel</span></div>
    <div class="actions">
      <button class="btn btn-outline btn-sm" type="button" data-modal="tag-dialog" data-id="<?= (int)$t['id'] ?>" data-name="<?= e($t['name']) ?>" data-slug="<?= e($t['slug']) ?>">Edit</button>
      <form method="post" data-confirm="Hapus tag ini?"><?= csrf_field() ?><input type="hidden" name="tipe" value="tag"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Hapus</button></form>
    </div>
  </div>
  <?php endforeach; ?>
</div></div>
<dialog id="tag-dialog" aria-labelledby="tag-title">
  <div class="dialog-head"><h2 id="tag-title" data-dialog-title data-create="Tambah tag" data-edit="Edit tag">Tambah tag</h2><button class="dialog-close" type="button" data-close aria-label="Tutup"><span data-icon="close"></span></button></div>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="tipe" value="tag"><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="">
    <div class="field"><label for="t-nama">Nama tag</label><input id="t-nama" type="text" name="name" data-fill="name" required></div>
    <div class="field"><label for="t-slug">Slug</label><input id="t-slug" type="text" name="slug" data-fill="slug"></div>
    <div class="dialog-actions"><button class="btn btn-outline" type="button" data-close>Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
  </form>
</dialog>
<?php endif; ?>
<?php layout_selesai(); ?>