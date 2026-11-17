<?php
require_once __DIR__ . '/../config/config.php';

if (current_user()) redirect('redaksi/index.php');

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($email === '' || $password === '') {
        $error = 'Email dan kata sandi wajib diisi.';
    } else {
        $error = login_coba($email, $password);
        if ($error === null) redirect('redaksi/index.php');
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title>Masuk redaksi — depokin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= url('redaksi/assets/admin.css') ?>">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="brand">depokin<span>.</span></div>
    <div class="tag">PANEL REDAKSI</div>
    <h1>Masuk ke redaksi</h1>
    <?php if ($error): ?><div class="flash error" role="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post">
      <?= csrf_field() ?>
      <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" required autocomplete="username" value="<?= e((string)($_POST['email'] ?? '')) ?>"></div>
      <div class="field"><label for="password">Kata sandi</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
      <button class="btn btn-primary" type="submit" style="width:100%">Masuk</button>
    </form>
    <p class="login-hint">Akun demo:<br><code>admin@depokin.test / Admin#2026</code><br><code>editor@depokin.test / Editor#2026</code><br><code>penulis@depokin.test / Penulis#2026</code></p>
  </div>
</div>
</body>
</html>