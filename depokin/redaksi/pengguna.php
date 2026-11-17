<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_role('admin');

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = (string)($_POST['aksi'] ?? '');
    $pdo = db();
    if ($aksi === 'simpan') {
        $tid = (int)($_POST['id'] ?? 0);
        $nama = trim((string)($_POST['name'] ?? ''));
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $peran = (string)($_POST['role'] ?? 'penulis');
        $bio = trim((string)($_POST['bio'] ?? '')) ?: null;
        $pass = (string)($_POST['password'] ?? '');
        $aktif = isset($_POST['is_active']) ? 1 : 0;
        if ($nama === '') $errors[] = 'Nama wajib diisi.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email tidak valid.';
        if (!in_array($peran, ['admin', 'editor', 'penulis'], true)) $errors[] = 'Peran tidak valid.';
        if (!$tid && strlen($pass) < 8) $errors[] = 'Kata sandi minimal 8 karakter.';
        if ($tid && $pass !== '' && strlen($pass) < 8) $errors[] = 'Kata sandi baru minimal 8 karakter.';
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?' . ($tid ? ' AND id != ?' : ''));
        $stmt->execute($tid ? [$email, $tid] : [$email]);
        if ($stmt->fetch()) $errors[] = 'Email sudah dipakai akun lain.';
        if ($tid === (int)$user['id'] && (!$aktif || $peran !== 'admin')) $errors[] = 'Tidak dapat menurunkan peran atau menonaktifkan akun sendiri.';
        if ($tid && $peran !== 'admin') {
            $lain = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1 AND id != " . $tid)->fetchColumn();
            if ((int)$lain === 0) $errors[] = 'Minimal harus ada satu administrator aktif.';
        }
        if (!$errors) {
            if ($tid) {
                $pdo->prepare('UPDATE users SET name=?, email=?, role=?, bio=?, is_active=? WHERE id=?')->execute([$nama, $email, $peran, $bio, $aktif, $tid]);
                if ($pass !== '') $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($pass, PASSWORD_DEFAULT), $tid]);
                flash('sukses', 'Pengguna diperbarui.');
            } else {
                $pdo->prepare('INSERT INTO users (name, email, password_hash, role, bio, is_active) VALUES (?,?,?,?,?,?)')->execute([$nama, $email, password_hash($pass, PASSWORD_DEFAULT), $peran, $bio, $aktif]);
                flash('sukses', 'Pengguna baru dibuat.');
            }
            redirect('redaksi/pengguna.php');
        }
    } elseif ($aksi === 'toggle') {
        $tid = (int)($_POST['id'] ?? 0);
        if ($tid === (int)$user['id']) { flash('error', 'Tidak dapat menonaktifkan akun sendiri.'); redirect('redaksi/pengguna.php'); }
        $target = $pdo->prepare('SELECT * FROM users WHERE id = ?');
        $target->execute([$tid]);
        $t = $target->fetch();
        if ($t) {
            if ($t['role'] === 'admin' && $t['is_active']) {
                $lain = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin' AND is_active = 1 AND id != " . $tid)->fetchColumn();
                if ((int)$lain === 0) { flash('error', 'Minimal harus ada satu administrator aktif.'); redirect('redaksi/pengguna.php'); }
            }
            $pdo->prepare('UPDATE users SET is_active = ? WHERE id = ?')->execute([$t['is_active'] ? 0 : 1, $tid]);
            flash('sukses', $t['is_active'] ? 'Pengguna dinonaktifkan.' : 'Pengguna diaktifkan.');
        }
        redirect('redaksi/pengguna.php');
    }
}

$rows = db()->query('SELECT u.*, (SELECT COUNT(*) FROM articles a WHERE a.author_id = u.id) AS jumlah_artikel FROM users u ORDER BY u.role, u.name')->fetchAll();
layout_mulai('Pengguna', 'pengguna');
?>
<?php if ($errors): ?><div class="flash error" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
<div class="page-actions">
  <button class="btn btn-primary" type="button" data-modal="user-dialog" data-dialog-title-edit="Edit pengguna">+ Tambah pengguna</button>
</div>
<div class="panel">
  <div class="row-list">
    <?php foreach ($rows as $r): ?>
    <div class="row-item">
      <span class="avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($r['name'], 0, 1))) ?></span>
      <div class="grow">
        <span class="title"><?= e($r['name']) ?><?= (int)$r['id'] === (int)$user['id'] ? ' (Anda)' : '' ?></span>
        <span class="sub"><?= e($r['email']) ?> · <?= (int)$r['jumlah_artikel'] ?> artikel</span>
      </div>
      <span class="badge <?= e($r['role']) ?>"><?= e($r['role']) ?></span>
      <?php if (!$r['is_active']): ?><span class="badge nonaktif">Nonaktif</span><?php endif; ?>
      <div class="actions">
        <button class="btn btn-outline btn-sm" type="button" data-modal="user-dialog"
          data-id="<?= (int)$r['id'] ?>" data-name="<?= e($r['name']) ?>" data-email="<?= e($r['email']) ?>"
          data-role="<?= e($r['role']) ?>" data-bio="<?= e($r['bio'] ?? '') ?>" data-is_active="<?= (int)$r['is_active'] ?>">Edit</button>
        <?php if ((int)$r['id'] !== (int)$user['id']): ?>
        <form method="post" data-confirm="<?= $r['is_active'] ? 'Nonaktifkan akun ini? Pengguna tidak akan bisa masuk.' : 'Aktifkan kembali akun ini?' ?>">
          <?= csrf_field() ?><input type="hidden" name="aksi" value="toggle"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
          <button class="btn <?= $r['is_active'] ? 'btn-danger' : 'btn-outline' ?> btn-sm" type="submit"><?= $r['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?></button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<dialog id="user-dialog" aria-labelledby="user-dialog-title">
  <div class="dialog-head"><h2 id="user-dialog-title" data-dialog-title data-create="Tambah pengguna" data-edit="Edit pengguna">Tambah pengguna</h2><button class="dialog-close" type="button" data-close aria-label="Tutup"><span data-icon="close"></span></button></div>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="aksi" value="simpan"><input type="hidden" name="id" value="">
    <div class="field"><label for="u-nama">Nama</label><input id="u-nama" type="text" name="name" data-fill="name" required></div>
    <div class="field"><label for="u-email">Email</label><input id="u-email" type="email" name="email" data-fill="email" required></div>
    <div class="field-row">
      <div class="field"><label for="u-peran">Peran</label><select id="u-peran" name="role" data-fill="role">
        <option value="penulis">Penulis</option><option value="editor">Editor</option><option value="admin">Administrator</option>
      </select></div>
      <div class="field"><label for="u-pass">Kata sandi</label><input id="u-pass" type="password" name="password" autocomplete="new-password"><p class="hint">Kosongkan saat edit bila tidak diganti.</p></div>
    </div>
    <div class="field"><label for="u-bio">Bio singkat</label><textarea id="u-bio" name="bio" rows="2" data-fill="bio"></textarea></div>
    <div class="field"><label class="check"><input type="checkbox" name="is_active" data-fill="is_active" checked> Akun aktif</label></div>
    <div class="dialog-actions"><button class="btn btn-outline" type="button" data-close>Batal</button><button class="btn btn-primary" type="submit">Simpan</button></div>
  </form>
</dialog>
<?php layout_selesai(); ?>