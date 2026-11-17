<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_login();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = trim((string)($_POST['name'] ?? ''));
    $bio = trim((string)($_POST['bio'] ?? '')) ?: null;
    $lama = (string)($_POST['password_lama'] ?? '');
    $baru = (string)($_POST['password_baru'] ?? '');
    $ulang = (string)($_POST['password_ulang'] ?? '');
    if ($nama === '') $errors[] = 'Nama wajib diisi.';
    $gantiPass = $lama !== '' || $baru !== '' || $ulang !== '';
    if ($gantiPass) {
        if (!password_verify($lama, $user['password_hash'])) $errors[] = 'Kata sandi lama salah.';
        if (strlen($baru) < 8) $errors[] = 'Kata sandi baru minimal 8 karakter.';
        if ($baru !== $ulang) $errors[] = 'Konfirmasi kata sandi tidak cocok.';
    }
    if (!$errors) {
        db()->prepare('UPDATE users SET name=?, bio=? WHERE id=?')->execute([$nama, $bio, (int)$user['id']]);
        if ($gantiPass) db()->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($baru, PASSWORD_DEFAULT), (int)$user['id']]);
        flash('sukses', 'Profil diperbarui.' . ($gantiPass ? ' Kata sandi diganti.' : ''));
        redirect('redaksi/profil.php');
    }
}
layout_mulai('Profil saya', 'profil');
?>
<?php if ($errors): ?><div class="flash error" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
<form method="post" style="max-width:640px">
  <?= csrf_field() ?>
  <div class="panel">
    <h2>Identitas</h2>
    <div class="field"><label for="p-nama">Nama tampilan</label><input id="p-nama" type="text" name="name" required value="<?= e($user['name']) ?>"></div>
    <div class="field"><label for="p-bio">Bio singkat (tampil di halaman penulis)</label><textarea id="p-bio" name="bio" rows="3"><?= e($user['bio'] ?? '') ?></textarea></div>
    <p class="hint">Email: <?= e($user['email']) ?> · Peran: <?= e($user['role']) ?> (diatur administrator)</p>
  </div>
  <div class="panel">
    <h2>Ganti kata sandi</h2>
    <div class="field"><label for="p-lama">Kata sandi lama</label><input id="p-lama" type="password" name="password_lama" autocomplete="current-password"></div>
    <div class="field-row">
      <div class="field"><label for="p-baru">Kata sandi baru</label><input id="p-baru" type="password" name="password_baru" autocomplete="new-password"></div>
      <div class="field"><label for="p-ulang">Ulangi kata sandi baru</label><input id="p-ulang" type="password" name="password_ulang" autocomplete="new-password"></div>
    </div>
    <p class="hint">Kosongkan semuanya bila tidak ingin mengganti kata sandi.</p>
  </div>
  <button class="btn btn-primary" type="submit">Simpan profil</button>
</form>
<?php layout_selesai(); ?>