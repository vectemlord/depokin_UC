<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => BASE_URL . '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('DEPOKIN_SESS');
    session_start();
}

function current_user(): ?array
{
    static $user = false;
    if ($user !== false) return $user;
    $user = null;
    if (!empty($_SESSION['user_id'])) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ? AND is_active = 1');
        $stmt->execute([(int)$_SESSION['user_id']]);
        $row = $stmt->fetch();
        if ($row) $user = $row;
        else unset($_SESSION['user_id']);
    }
    return $user;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? '') === 'admin';
}

function is_editor(): bool
{
    return in_array(current_user()['role'] ?? '', ['admin', 'editor'], true);
}

function login_coba(string $email, string $password): ?string
{
    $terkunci = $_SESSION['login_kunci'] ?? 0;
    if ($terkunci > time()) {
        return 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . ceil(($terkunci - time()) / 60) . ' menit.';
    }
    $stmt = db()->prepare('SELECT * FROM users WHERE email = ? AND is_active = 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        unset($_SESSION['login_gagal'], $_SESSION['login_kunci']);
        return null;
    }
    $_SESSION['login_gagal'] = ($_SESSION['login_gagal'] ?? 0) + 1;
    if ($_SESSION['login_gagal'] >= 5) {
        $_SESSION['login_kunci'] = time() + 300;
        $_SESSION['login_gagal'] = 0;
        return 'Terlalu banyak percobaan gagal. Coba lagi dalam 5 menit.';
    }
    return 'Email atau kata sandi salah.';
}

function logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        redirect('redaksi/login.php');
    }
    return $user;
}

function require_role(string ...$roles): array
{
    $user = require_login();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        exit('Anda tidak memiliki akses ke halaman ini.');
    }
    return $user;
}

function boleh_edit(array $artikel, array $user): bool
{
    if (in_array($user['role'], ['admin', 'editor'], true)) return true;
    return (int)$artikel['author_id'] === (int)$user['id']
        && in_array($artikel['status'], ['draf', 'revisi'], true);
}

function boleh_hapus(array $artikel, array $user): bool
{
    if ($user['role'] === 'admin') return true;
    return (int)$artikel['author_id'] === (int)$user['id'] && $artikel['status'] === 'draf';
}