<?php
require_once __DIR__ . '/../config/config.php';
$user = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('redaksi/artikel.php');
csrf_check();

$aksi = (string)($_POST['aksi'] ?? '');
$id = (int)($_POST['id'] ?? 0);
$catatan = trim((string)($_POST['catatan'] ?? ''));
$kembali = (string)($_POST['kembali'] ?? 'artikel.php');
if (!preg_match('#^(artikel|tinjauan|artikel-form)\.php(\?id=\d+)?$#', $kembali)) $kembali = 'artikel.php';

$a = $id ? artikel_by_id($id) : null;
if (!$a) { flash('error', 'Artikel tidak ditemukan.'); redirect('redaksi/' . $kembali); }

$pdo = db();
$uid = (int)$user['id'];

switch ($aksi) {
    case 'hapus':
        if (!boleh_hapus($a, $user)) { http_response_code(403); exit('Tidak diizinkan menghapus artikel ini.'); }
        foreach (artikel_media($id) as $m) {
            foreach (['file_path', 'poster_path'] as $k) {
                if (!empty($m[$k]) && str_starts_with($m[$k], 'uploads/') && is_file(APP_ROOT . '/' . $m[$k])) unlink(APP_ROOT . '/' . $m[$k]);
            }
        }
        $pdo->prepare('DELETE FROM articles WHERE id = ?')->execute([$id]);
        flash('sukses', 'Artikel dihapus.');
        redirect('redaksi/artikel.php');

    case 'featured':
        if (!is_editor() || $a['status'] !== 'terbit') { http_response_code(403); exit('Hanya editor yang dapat mengatur pilihan redaksi pada artikel terbit.'); }
        if ($a['is_featured']) {
            $pdo->prepare('UPDATE articles SET is_featured = 0 WHERE id = ?')->execute([$id]);
            flash('sukses', 'Artikel dilepas dari pilihan redaksi.');
        } else {
            $pdo->exec('UPDATE articles SET is_featured = 0');
            $pdo->prepare('UPDATE articles SET is_featured = 1 WHERE id = ?')->execute([$id]);
            catat_log($id, $uid, 'featured');
            flash('sukses', 'Artikel dijadikan pilihan redaksi di beranda.');
        }
        break;

    case 'koreksi':
        if (!is_editor() || $a['status'] !== 'terbit') { http_response_code(403); exit('Hanya editor yang dapat menerbitkan koreksi.'); }
        if ($catatan === '') { flash('error', 'Catatan koreksi wajib diisi.'); redirect('redaksi/' . $kembali); }
        catat_log($id, $uid, 'koreksi', $catatan);
        $pdo->prepare('UPDATE articles SET updated_at = NOW() WHERE id = ?')->execute([$id]);
        flash('sukses', 'Catatan koreksi dipublikasikan di halaman artikel.');
        break;

    case 'submit':
    case 'revisi':
    case 'terbit':
    case 'arsip':
        $status = $a['status'];
        $boleh = match ($aksi) {
            'submit' => (int)$a['author_id'] === $uid && in_array($status, ['draf', 'revisi'], true),
            'revisi' => is_editor() && $status === 'ditinjau',
            'terbit' => is_editor() && $status !== 'arsip',
            'arsip' => is_editor() && $status === 'terbit',
        };
        if (!$boleh) { http_response_code(403); exit('Transisi status tidak diizinkan.'); }
        if ($aksi === 'revisi' && $catatan === '') { flash('error', 'Catatan revisi wajib diisi.'); redirect('redaksi/' . $kembali); }
        $baru = ['submit' => 'ditinjau', 'revisi' => 'revisi', 'terbit' => 'terbit', 'arsip' => 'arsip'][$aksi];
        if ($aksi === 'terbit') {
            $pdo->prepare('UPDATE articles SET status=?, published_at = COALESCE(published_at, NOW()), editor_id=? WHERE id=?')->execute([$baru, $uid, $id]);
        } else {
            $pdo->prepare('UPDATE articles SET status=? WHERE id=?')->execute([$baru, $id]);
        }
        catat_log($id, $uid, $aksi, $catatan ?: null);
        flash('sukses', 'Status artikel: ' . status_label($baru) . '.');
        break;

    default:
        flash('error', 'Aksi tidak dikenal.');
}
redirect('redaksi/' . $kembali);