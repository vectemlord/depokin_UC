<?php
declare(strict_types=1);

const ARTIKEL_SELECT = 'SELECT a.*, c.name AS category_name, c.slug AS category_slug,
    l.name AS location_name, l.slug AS location_slug,
    u.name AS author_name, ed.name AS editor_name,
    m.file_path AS thumb_path, m.alt_text AS thumb_alt,
    mv.duration_seconds AS video_duration
  FROM articles a
  JOIN categories c ON c.id = a.category_id
  LEFT JOIN locations l ON l.id = a.location_id
  JOIN users u ON u.id = a.author_id
  LEFT JOIN users ed ON ed.id = a.editor_id
  LEFT JOIN media m ON m.article_id = a.id AND m.is_primary = 1 AND m.kind = \'gambar\'
  LEFT JOIN media mv ON mv.article_id = a.id AND mv.kind = \'video\'';

function artikel_filter_sql(array $f): array
{
    $where = ["a.status = 'terbit'", 'a.published_at <= NOW()'];
    $params = [];
    if (!empty($f['kategori'])) { $where[] = 'c.slug = ?'; $params[] = $f['kategori']; }
    if (!empty($f['lokasi'])) { $where[] = 'l.slug = ?'; $params[] = $f['lokasi']; }
    if (!empty($f['format'])) { $where[] = 'a.format = ?'; $params[] = $f['format']; }
    if (!empty($f['q'])) {
        $where[] = '(a.title LIKE ? OR a.deck LIKE ? OR a.content LIKE ? OR c.name LIKE ? OR l.name LIKE ?)';
        $like = '%' . $f['q'] . '%';
        array_push($params, $like, $like, $like, $like, $like);
    }
    if (!empty($f['tag'])) {
        $where[] = 'EXISTS (SELECT 1 FROM article_tags at2 JOIN tags t2 ON t2.id = at2.tag_id WHERE at2.article_id = a.id AND t2.slug = ?)';
        $params[] = $f['tag'];
    }
    return [' WHERE ' . implode(' AND ', $where), $params];
}

function artikel_terbit(int $limit = 10, int $offset = 0, array $f = []): array
{
    [$where, $params] = artikel_filter_sql($f);
    $stmt = db()->prepare(ARTIKEL_SELECT . $where . ' ORDER BY a.published_at DESC LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function artikel_hitung(array $f = []): int
{
    [$where, $params] = artikel_filter_sql($f);
    $stmt = db()->prepare('SELECT COUNT(*) FROM articles a JOIN categories c ON c.id = a.category_id LEFT JOIN locations l ON l.id = a.location_id' . $where);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function artikel_by_slug(string $slug, bool $hanyaTerbit = true): ?array
{
    $sql = ARTIKEL_SELECT . ' WHERE a.slug = ?';
    if ($hanyaTerbit) $sql .= " AND a.status = 'terbit' AND a.published_at <= NOW()";
    $stmt = db()->prepare($sql);
    $stmt->execute([$slug]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function artikel_by_id(int $id): ?array
{
    $stmt = db()->prepare(ARTIKEL_SELECT . ' WHERE a.id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

function artikel_featured(): ?array
{
    $rows = artikel_terbit(1, 0, []);
    $stmt = db()->prepare(ARTIKEL_SELECT . " WHERE a.status = 'terbit' AND a.published_at <= NOW() AND a.is_featured = 1 ORDER BY a.published_at DESC LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();
    return $row ?: ($rows[0] ?? null);
}

function artikel_media(int $articleId): array
{
    $stmt = db()->prepare('SELECT * FROM media WHERE article_id = ? ORDER BY sort_order, id');
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

function artikel_tags(int $articleId): array
{
    $stmt = db()->prepare('SELECT t.* FROM tags t JOIN article_tags at2 ON t.id = at2.tag_id WHERE at2.article_id = ? ORDER BY t.name');
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

function artikel_terkait(array $artikel, int $limit = 2): array
{
    $stmt = db()->prepare(ARTIKEL_SELECT . " WHERE a.status = 'terbit' AND a.published_at <= NOW() AND a.id != ? ORDER BY (a.category_id = ?) DESC, a.published_at DESC LIMIT " . (int)$limit);
    $stmt->execute([(int)$artikel['id'], (int)$artikel['category_id']]);
    return $stmt->fetchAll();
}

function artikel_koreksi(int $articleId): array
{
    $stmt = db()->prepare("SELECT lg.*, u.name AS user_name FROM article_logs lg LEFT JOIN users u ON u.id = lg.user_id WHERE lg.article_id = ? AND lg.action = 'koreksi' ORDER BY lg.created_at DESC");
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

function artikel_log(int $articleId): array
{
    $stmt = db()->prepare('SELECT lg.*, u.name AS user_name FROM article_logs lg LEFT JOIN users u ON u.id = lg.user_id WHERE lg.article_id = ? ORDER BY lg.created_at DESC, lg.id DESC');
    $stmt->execute([$articleId]);
    return $stmt->fetchAll();
}

function kategori_semua(): array
{
    return db()->query("SELECT c.*, (SELECT COUNT(*) FROM articles a WHERE a.category_id = c.id AND a.status = 'terbit' AND a.published_at <= NOW()) AS jumlah FROM categories c ORDER BY c.id")->fetchAll();
}

function lokasi_semua(): array
{
    return db()->query("SELECT l.*, (SELECT COUNT(*) FROM articles a WHERE a.location_id = l.id AND a.status = 'terbit' AND a.published_at <= NOW()) AS jumlah FROM locations l ORDER BY l.name")->fetchAll();
}

function tags_semua(): array
{
    return db()->query('SELECT * FROM tags ORDER BY name')->fetchAll();
}