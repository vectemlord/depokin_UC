<?php
declare(strict_types=1);

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim((string)$text, '-');
    return $text !== '' ? $text : 'cerita';
}

function unique_slug(string $table, string $slug, ?int $ignoreId = null): string
{
    $allowed = ['articles', 'categories', 'locations', 'tags'];
    if (!in_array($table, $allowed, true)) {
        throw new InvalidArgumentException('Tabel tidak diizinkan untuk slug.');
    }
    $base = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE slug = ?" . ($ignoreId ? ' AND id != ?' : '');
        $stmt = db()->prepare($sql);
        $stmt->execute($ignoreId ? [$slug, $ignoreId] : [$slug]);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . $i++;
    }
}

function tanggal_id(?string $datetime, bool $panjang = false, bool $jam = false): string
{
    if (!$datetime) return '';
    $ts = strtotime($datetime);
    if ($ts === false) return '';
    $bulanPendek = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $bulanPanjang = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $bulan = $panjang ? $bulanPanjang[(int)date('n', $ts)] : $bulanPendek[(int)date('n', $ts)];
    $out = date('j', $ts) . ' ' . $bulan . ' ' . date('Y', $ts);
    if ($jam) $out .= ', ' . date('H.i', $ts);
    return $out;
}

function baca_menit(string $html): string
{
    $kata = str_word_count(strip_tags($html));
    return max(1, (int)ceil($kata / 200)) . ' menit';
}

function durasi_video(?int $detik): string
{
    if (!$detik) return '';
    $menit = intdiv($detik, 60);
    $sisa = $detik % 60;
    return $menit > 0 ? $menit . ' menit' . ($sisa ? ' ' . $sisa . ' dtk' : '') : $sisa . ' detik';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Sesi tidak valid. Muat ulang halaman dan coba lagi.');
    }
}

function flash(string $key, ?string $msg = null): ?string
{
    if ($msg !== null) {
        $_SESSION['flash'][$key] = $msg;
        return null;
    }
    $val = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $val;
}

function sanitize_html(string $html): string
{
    $allow = ['p', 'h2', 'h3', 'strong', 'b', 'em', 'i', 'blockquote', 'ul', 'ol', 'li', 'a', 'br'];
    $doc = new DOMDocument('1.0', 'UTF-8');
    libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8" ?><body>' . $html . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    $body = $doc->getElementsByTagName('body')->item(0);
    if (!$body) return '';
    $walk = function (DOMNode $node) use (&$walk, $allow, $doc): void {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $tag = strtolower($child->nodeName);
                if (!in_array($tag, $allow, true)) {
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }
                foreach (iterator_to_array($child->attributes) as $attr) {
                    if ($tag === 'a' && $attr->name === 'href' && preg_match('#^https?://#i', $attr->value)) {
                        continue;
                    }
                    $child->removeAttribute($attr->name);
                }
                if ($tag === 'a') {
                    $child->setAttribute('rel', 'noreferrer');
                    $child->setAttribute('target', '_blank');
                }
                $walk($child);
            } elseif ($child->nodeType !== XML_TEXT_NODE) {
                $node->removeChild($child);
            }
        }
    };
    $walk($body);
    $out = '';
    foreach ($body->childNodes as $child) {
        $out .= $doc->saveHTML($child);
    }
    return trim($out);
}

function youtube_id(?string $url): ?string
{
    if (!$url) return null;
    if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})#', $url, $m)) {
        return $m[1];
    }
    return null;
}

function media_url(?string $path): string
{
    if (!$path) return '';
    if (preg_match('#^https?://#', $path)) return $path;
    return url($path);
}

function setting(string $key, string $default = ''): string
{
    static $all = null;
    if ($all === null) {
        $all = [];
        foreach (db()->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $all[$row['setting_key']] = (string)$row['setting_value'];
        }
    }
    return $all[$key] ?? $default;
}

function simpan_setting(string $key, string $value): void
{
    $stmt = db()->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    $stmt->execute([$key, $value]);
}

function upload_file(array $file, string $tipe = 'gambar'): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Unggahan gagal. Periksa ukuran file lalu coba lagi.');
    }
    $aturan = [
        'gambar' => ['mime' => ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'], 'max' => 5 * 1024 * 1024],
        'video' => ['mime' => ['video/mp4' => 'mp4', 'video/webm' => 'webm'], 'max' => 64 * 1024 * 1024],
    ];
    if (!isset($aturan[$tipe])) throw new InvalidArgumentException('Tipe unggahan tidak dikenal.');
    $info = $aturan[$tipe];
    if ($file['size'] > $info['max']) {
        throw new RuntimeException('Ukuran file melebihi batas ' . round($info['max'] / 1048576) . ' MB.');
    }
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!isset($info['mime'][$mime])) {
        throw new RuntimeException('Format file tidak diizinkan untuk ' . $tipe . '.');
    }
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
    $nama = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $info['mime'][$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $nama)) {
        throw new RuntimeException('Gagal menyimpan file unggahan.');
    }
    return 'uploads/' . $nama;
}

function excerpt(string $text, int $len = 160): string
{
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $len) return $text;
    return rtrim(mb_substr($text, 0, $len)) . '…';
}

function status_label(string $status): string
{
    return [
        'draf' => 'Draf', 'ditinjau' => 'Ditinjau', 'revisi' => 'Perlu revisi',
        'terbit' => 'Terbit', 'arsip' => 'Arsip',
    ][$status] ?? $status;
}

function render_paginasi(int $page, int $total, int $perPage, string $baseUrl): string
{
    $pages = (int)ceil($total / $perPage);
    if ($pages <= 1) return '';
    $sep = str_contains($baseUrl, '?') ? '&' : '?';
    $out = '<nav class="pagination" aria-label="Halaman">';
    if ($page > 1) $out .= '<a href="' . e($baseUrl . $sep . 'hal=' . ($page - 1)) . '">← Sebelumnya</a>';
    $out .= '<span>Hal ' . $page . ' dari ' . $pages . '</span>';
    if ($page < $pages) $out .= '<a href="' . e($baseUrl . $sep . 'hal=' . ($page + 1)) . '">Berikutnya →</a>';
    return $out . '</nav>';
}

function catat_log(int $articleId, ?int $userId, string $aksi, ?string $note = null): void
{
    $stmt = db()->prepare('INSERT INTO article_logs (article_id, user_id, action, note) VALUES (?, ?, ?, ?)');
    $stmt->execute([$articleId, $userId, $aksi, $note]);
}