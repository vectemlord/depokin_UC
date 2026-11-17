<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/partials/layout.php';
$user = require_login();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$a = $id ? artikel_by_id($id) : null;
if ($id && !$a) { http_response_code(404); exit('Artikel tidak ditemukan.'); }
if ($a && !boleh_edit($a, $user)) { http_response_code(403); exit('Anda tidak dapat mengedit artikel ini.'); }

$errors = [];
$kategoriList = kategori_semua();
$lokasiList = lokasi_semua();

$nilai = [
    'title' => $a['title'] ?? '', 'slug' => $a['slug'] ?? '', 'deck' => $a['deck'] ?? '',
    'content' => $a['content'] ?? '', 'format' => $a['format'] ?? 'foto',
    'category_id' => (int)($a['category_id'] ?? 0), 'location_id' => (int)($a['location_id'] ?? 0),
    'meta_title' => $a['meta_title'] ?? '', 'meta_description' => $a['meta_description'] ?? '',
    'event_start' => !empty($a['event_start']) ? date('Y-m-d\TH:i', strtotime($a['event_start'])) : '',
    'event_end' => !empty($a['event_end']) ? date('Y-m-d\TH:i', strtotime($a['event_end'])) : '',
];
$tagInput = $a ? implode(', ', array_column(artikel_tags($id), 'name')) : '';
$media = $a ? artikel_media($id) : [];
$utama = null; $video = null; $galeri = [];
foreach ($media as $m) {
    if ($m['kind'] === 'video') { $video = $video ?: $m; continue; }
    if ($m['is_primary'] && !$utama) $utama = $m; else $galeri[] = $m;
}
$catatan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = (string)($_POST['aksi'] ?? 'simpan');
    $catatan = trim((string)($_POST['catatan'] ?? ''));
    foreach (array_keys($nilai) as $k) {
        $nilai[$k] = trim((string)($_POST[$k] ?? ''));
    }
    $nilai['category_id'] = (int)$nilai['category_id'];
    $nilai['location_id'] = (int)$nilai['location_id'];
    $tagInput = trim((string)($_POST['tags'] ?? ''));
    $nilai['content'] = sanitize_html((string)($_POST['content'] ?? ''));

    if ($nilai['title'] === '') $errors[] = 'Judul wajib diisi.';
    if (mb_strlen($nilai['title']) > 200) $errors[] = 'Judul maksimal 200 karakter.';
    if (mb_strlen($nilai['deck']) > 500) $errors[] = 'Ringkasan maksimal 500 karakter.';
    if (!in_array($nilai['format'], ['foto', 'video', 'teks'], true)) $errors[] = 'Format tidak valid.';
    $katAda = false;
    foreach ($kategoriList as $c) if ((int)$c['id'] === $nilai['category_id']) $katAda = true;
    if (!$katAda) $errors[] = 'Kategori wajib dipilih.';
    if ($nilai['location_id']) {
        $lokAda = false;
        foreach ($lokasiList as $l) if ((int)$l['id'] === $nilai['location_id']) $lokAda = true;
        if (!$lokAda) $errors[] = 'Lokasi tidak valid.';
    }
    foreach (['event_start', 'event_end'] as $k) {
        if ($nilai[$k] !== '' && !DateTime::createFromFormat('Y-m-d\TH:i', $nilai[$k])) $errors[] = 'Format waktu acara tidak valid.';
    }

    $statusAwal = $a['status'] ?? 'draf';
    $bolehAksi = match ($aksi) {
        'simpan' => true,
        'submit' => in_array($statusAwal, ['draf', 'revisi'], true),
        'revisi' => is_editor() && $statusAwal === 'ditinjau',
        'terbit' => is_editor() && $statusAwal !== 'arsip',
        'arsip' => is_editor() && $statusAwal === 'terbit',
        default => false,
    };
    if (!$bolehAksi) $errors[] = 'Aksi tidak diizinkan untuk status/peran saat ini.';
    if ($aksi === 'revisi' && $catatan === '') $errors[] = 'Catatan revisi wajib diisi agar penulis tahu apa yang diperbaiki.';

    $fileUtama = null; $fileVideo = null; $filePoster = null; $fileGaleri = [];
    try {
        if (!empty($_FILES['foto_utama']['name'])) $fileUtama = upload_file($_FILES['foto_utama'], 'gambar');
        if (!empty($_FILES['video_file']['name'])) $fileVideo = upload_file($_FILES['video_file'], 'video');
        if (!empty($_FILES['poster']['name'])) $filePoster = upload_file($_FILES['poster'], 'gambar');
        if (!empty($_FILES['galeri']['name']) && is_array($_FILES['galeri']['name'])) {
            foreach ($_FILES['galeri']['name'] as $i => $nama) {
                if ($nama === '' || $_FILES['galeri']['error'][$i] === UPLOAD_ERR_NO_FILE) continue;
                $fileGaleri[] = upload_file([
                    'name' => $nama, 'type' => $_FILES['galeri']['type'][$i],
                    'tmp_name' => $_FILES['galeri']['tmp_name'][$i], 'error' => $_FILES['galeri']['error'][$i],
                    'size' => $_FILES['galeri']['size'][$i],
                ], 'gambar');
            }
        }
    } catch (RuntimeException $ex) {
        $errors[] = $ex->getMessage();
    }

    if (!$errors) {
        $slug = unique_slug('articles', $nilai['slug'] !== '' ? slugify($nilai['slug']) : slugify($nilai['title']), $id ?: null);
        $eventStart = $nilai['event_start'] !== '' ? str_replace('T', ' ', $nilai['event_start']) . ':00' : null;
        $eventEnd = $nilai['event_end'] !== '' ? str_replace('T', ' ', $nilai['event_end']) . ':00' : null;
        $pdo = db();

        if (!$a) {
            $stmt = $pdo->prepare('INSERT INTO articles (title, slug, deck, content, format, category_id, location_id, author_id, status, meta_title, meta_description, event_start, event_end) VALUES (?,?,?,?,?,?,?,?,\'draf\',?,?,?,?)');
            $stmt->execute([$nilai['title'], $slug, $nilai['deck'], $nilai['content'], $nilai['format'], $nilai['category_id'], $nilai['location_id'] ?: null, (int)$user['id'], $nilai['meta_title'] ?: null, $nilai['meta_description'] ?: null, $eventStart, $eventEnd]);
            $id = (int)$pdo->lastInsertId();
            catat_log($id, (int)$user['id'], 'buat');
        } else {
            $stmt = $pdo->prepare('UPDATE articles SET title=?, slug=?, deck=?, content=?, format=?, category_id=?, location_id=?, meta_title=?, meta_description=?, event_start=?, event_end=? WHERE id=?');
            $stmt->execute([$nilai['title'], $slug, $nilai['deck'], $nilai['content'], $nilai['format'], $nilai['category_id'], $nilai['location_id'] ?: null, $nilai['meta_title'] ?: null, $nilai['meta_description'] ?: null, $eventStart, $eventEnd, $id]);
        }

        $pdo->prepare('DELETE FROM article_tags WHERE article_id = ?')->execute([$id]);
        foreach (array_filter(array_map('trim', explode(',', $tagInput))) as $namaTag) {
            $stmt = $pdo->prepare('SELECT id FROM tags WHERE name = ?');
            $stmt->execute([$namaTag]);
            $tagId = $stmt->fetchColumn();
            if (!$tagId) {
                $pdo->prepare('INSERT INTO tags (name, slug) VALUES (?, ?)')->execute([$namaTag, unique_slug('tags', slugify($namaTag))]);
                $tagId = (int)$pdo->lastInsertId();
            }
            $pdo->prepare('INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (?, ?)')->execute([$id, (int)$tagId]);
        }

        $hapusMedia = array_map('intval', (array)($_POST['hapus_media'] ?? []));
        $utamaPilih = (int)($_POST['utama_id'] ?? 0);
        foreach ($media as $m) {
            if (!in_array((int)$m['id'], $hapusMedia, true)) continue;
            if (!empty($m['file_path']) && str_starts_with($m['file_path'], 'uploads/') && is_file(APP_ROOT . '/' . $m['file_path'])) unlink(APP_ROOT . '/' . $m['file_path']);
            if (!empty($m['poster_path']) && str_starts_with($m['poster_path'], 'uploads/') && is_file(APP_ROOT . '/' . $m['poster_path'])) unlink(APP_ROOT . '/' . $m['poster_path']);
            $pdo->prepare('DELETE FROM media WHERE id = ?')->execute([(int)$m['id']]);
        }
        $media = artikel_media($id);
        $utama = null; $video = null;
        foreach ($media as $m) {
            if ($m['kind'] === 'video') $video = $video ?: $m;
            elseif ($m['is_primary'] && !$utama) $utama = $m;
        }

        $metaUtama = [
            'alt_text' => trim((string)($_POST['alt_text'] ?? '')) ?: null,
            'caption' => trim((string)($_POST['caption'] ?? '')) ?: null,
            'credit_name' => trim((string)($_POST['credit_name'] ?? '')) ?: null,
            'credit_url' => trim((string)($_POST['credit_url'] ?? '')) ?: null,
            'license' => trim((string)($_POST['license'] ?? '')) ?: null,
            'license_url' => trim((string)($_POST['license_url'] ?? '')) ?: null,
        ];
        if ($fileUtama || $utama) {
            if ($fileUtama && $utama && !empty($utama['file_path']) && str_starts_with($utama['file_path'], 'uploads/') && is_file(APP_ROOT . '/' . $utama['file_path'])) unlink(APP_ROOT . '/' . $utama['file_path']);
            $path = $fileUtama ?: $utama['file_path'];
            if ($utama) {
                $stmt = $pdo->prepare('UPDATE media SET file_path=?, alt_text=?, caption=?, credit_name=?, credit_url=?, license=?, license_url=?, is_primary=1 WHERE id=?');
                $stmt->execute([$path, $metaUtama['alt_text'], $metaUtama['caption'], $metaUtama['credit_name'], $metaUtama['credit_url'], $metaUtama['license'], $metaUtama['license_url'], (int)$utama['id']]);
                $utamaId = (int)$utama['id'];
            } else {
                $pdo->prepare('INSERT INTO media (article_id, kind, file_path, alt_text, caption, credit_name, credit_url, license, license_url, is_primary, sort_order, uploaded_by) VALUES (?,\'gambar\',?,?,?,?,?,?,?,1,0,?)')
                    ->execute([$id, $path, $metaUtama['alt_text'], $metaUtama['caption'], $metaUtama['credit_name'], $metaUtama['credit_url'], $metaUtama['license'], $metaUtama['license_url'], (int)$user['id']]);
                $utamaId = (int)$pdo->lastInsertId();
            }
            $pdo->prepare('UPDATE media SET is_primary = 0 WHERE article_id = ? AND kind = \'gambar\' AND id != ?')->execute([$id, $utamaId]);
        } elseif ($utamaPilih) {
            $pdo->prepare('UPDATE media SET is_primary = 0 WHERE article_id = ? AND kind = \'gambar\'')->execute([$id]);
            $pdo->prepare('UPDATE media SET is_primary = 1 WHERE id = ? AND article_id = ? AND kind = \'gambar\'')->execute([$utamaPilih, $id]);
        }

        $urut = (int)$pdo->query('SELECT COALESCE(MAX(sort_order),0) FROM media WHERE article_id = ' . $id)->fetchColumn();
        foreach ($fileGaleri as $path) {
            $pdo->prepare('INSERT INTO media (article_id, kind, file_path, is_primary, sort_order, uploaded_by) VALUES (?,\'gambar\',?,0,?,?)')
                ->execute([$id, $path, ++$urut, (int)$user['id']]);
        }

        if ($nilai['format'] === 'video') {
            $embed = trim((string)($_POST['embed_url'] ?? ''));
            $durasi = max(0, (int)($_POST['duration_seconds'] ?? 0));
            $transkrip = trim((string)($_POST['transcript'] ?? '')) ?: null;
            if ($fileVideo && $video && !empty($video['file_path']) && str_starts_with($video['file_path'], 'uploads/') && is_file(APP_ROOT . '/' . $video['file_path'])) unlink(APP_ROOT . '/' . $video['file_path']);
            if ($filePoster && $video && !empty($video['poster_path']) && str_starts_with($video['poster_path'], 'uploads/') && is_file(APP_ROOT . '/' . $video['poster_path'])) unlink(APP_ROOT . '/' . $video['poster_path']);
            $vPath = $fileVideo ?: ($video['file_path'] ?? null);
            $pPath = $filePoster ?: ($video['poster_path'] ?? null);
            if ($video) {
                $pdo->prepare('UPDATE media SET file_path=?, embed_url=?, poster_path=?, duration_seconds=?, transcript=? WHERE id=?')
                    ->execute([$vPath, $embed ?: null, $pPath, $durasi ?: null, $transkrip, (int)$video['id']]);
            } elseif ($fileVideo || $embed !== '' || $transkrip || $durasi) {
                $pdo->prepare('INSERT INTO media (article_id, kind, file_path, embed_url, poster_path, duration_seconds, transcript, is_primary, sort_order, uploaded_by) VALUES (?,\'video\',?,?,?,?,?,0,?,?)')
                    ->execute([$id, $vPath, $embed ?: null, $pPath, $durasi ?: null, $transkrip, ++$urut, (int)$user['id']]);
            }
        }

        $statusBaru = $statusAwal;
        if ($aksi === 'submit') $statusBaru = 'ditinjau';
        elseif ($aksi === 'revisi') $statusBaru = 'revisi';
        elseif ($aksi === 'terbit') $statusBaru = 'terbit';
        elseif ($aksi === 'arsip') $statusBaru = 'arsip';

        if ($statusBaru !== $statusAwal || $aksi === 'terbit') {
            if ($statusBaru === 'terbit') {
                $pdo->prepare('UPDATE articles SET status=?, published_at = COALESCE(published_at, NOW()), editor_id=? WHERE id=?')->execute([$statusBaru, (int)$user['id'], $id]);
            } else {
                $pdo->prepare('UPDATE articles SET status=? WHERE id=?')->execute([$statusBaru, $id]);
            }
            catat_log($id, (int)$user['id'], $aksi === 'simpan' ? 'simpan' : $aksi, $catatan ?: null);
        } elseif ($a) {
            catat_log($id, (int)$user['id'], 'simpan', $catatan ?: null);
        }

        flash('sukses', $aksi === 'terbit' ? 'Artikel diterbitkan.' : ($aksi === 'submit' ? 'Artikel dikirim ke tinjauan editor.' : 'Perubahan disimpan.'));
        $sesudah = array_merge($a ?: ['author_id' => (int)$user['id']], ['status' => $statusBaru]);
        redirect(boleh_edit($sesudah, $user) ? 'redaksi/artikel-form.php?id=' . $id : 'redaksi/artikel.php');
    }
}

$logs = $id ? artikel_log($id) : [];
$statusSaatIni = $a['status'] ?? 'draf';
layout_mulai($a ? 'Edit artikel' : 'Tulis artikel', 'artikel');
?>
<?php if ($errors): ?><div class="flash error" role="alert"><?= e(implode(' ', $errors)) ?></div><?php endif; ?>
<div class="form-grid">
<form method="post" enctype="multipart/form-data" id="artikel-form">
  <?= csrf_field() ?>
  <input type="hidden" name="id" value="<?= $id ?>">
  <input type="hidden" name="content" id="content-input" value="<?= e($nilai['content']) ?>">
  <nav class="tabs" data-tabs="#form-tabs" aria-label="Bagian artikel">
    <button type="button" data-tab="konten" aria-selected="true">Konten</button>
    <button type="button" data-tab="media" aria-selected="false">Media</button>
    <button type="button" data-tab="seo" aria-selected="false">SEO &amp; Agenda</button>
  </nav>
  <div id="form-tabs">
  <section class="tab-panel" id="tab-konten">
    <div class="panel">
      <div class="field"><label for="f-judul">Judul</label><input id="f-judul" type="text" name="title" maxlength="200" required value="<?= e($nilai['title']) ?>"></div>
      <div class="field"><label for="f-slug">Slug URL</label><input id="f-slug" type="text" name="slug" value="<?= e($nilai['slug']) ?>"><p class="hint">Otomatis dari judul bila dibiarkan kosong. Menjadi alamat /berita/slug.</p></div>
      <div class="field"><label for="f-deck">Ringkasan</label><textarea id="f-deck" name="deck" maxlength="500" rows="3"><?= e($nilai['deck']) ?></textarea></div>
      <div class="field">
        <label id="lbl-isi">Isi artikel</label>
        <div class="editor-toolbar" role="toolbar" aria-label="Format teks">
          <button type="button" data-cmd="bold"><b>T</b></button>
          <button type="button" data-cmd="italic"><i>M</i></button>
          <button type="button" data-cmd="formatBlock" data-value="h2">Subjudul</button>
          <button type="button" data-cmd="formatBlock" data-value="h3">Sub-bab</button>
          <button type="button" data-cmd="formatBlock" data-value="blockquote">Kutipan</button>
          <button type="button" data-cmd="insertUnorderedList">Daftar</button>
          <button type="button" data-cmd="formatBlock" data-value="p">Paragraf</button>
          <button type="button" data-cmd="createLink">Tautan</button>
        </div>
        <div class="editor-area" id="editor-area" contenteditable="true" aria-labelledby="lbl-isi"><?= $nilai['content'] ?></div>
        <p class="hint">Teks disimpan dengan format aman (paragraf, subjudul, kutipan, daftar, tautan).</p>
      </div>
      <div class="field-row">
        <div class="field"><label for="f-format">Format</label><select id="f-format" name="format">
          <option value="foto" <?= $nilai['format'] === 'foto' ? 'selected' : '' ?>>Foto + teks</option>
          <option value="video" <?= $nilai['format'] === 'video' ? 'selected' : '' ?>>Video</option>
          <option value="teks" <?= $nilai['format'] === 'teks' ? 'selected' : '' ?>>Teks saja</option>
        </select></div>
        <div class="field"><label for="f-kategori">Kategori</label><select id="f-kategori" name="category_id" required>
          <option value="">Pilih kategori…</option>
          <?php foreach ($kategoriList as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $nilai['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
        </select></div>
      </div>
      <div class="field-row">
        <div class="field"><label for="f-lokasi">Lokasi liputan</label><select id="f-lokasi" name="location_id">
          <option value="">Tanpa lokasi</option>
          <?php foreach ($lokasiList as $l): ?><option value="<?= (int)$l['id'] ?>" <?= $nilai['location_id'] === (int)$l['id'] ? 'selected' : '' ?>><?= e($l['name']) ?> (<?= e($l['kind']) ?>)</option><?php endforeach; ?>
        </select></div>
        <div class="field"><label for="f-tags">Tag</label><input id="f-tags" type="text" name="tags" value="<?= e($tagInput) ?>" placeholder="kopi lokal, komuter"><p class="hint">Pisahkan dengan koma. Tag baru dibuat otomatis.</p></div>
      </div>
    </div>
  </section>
  <section class="tab-panel" id="tab-media" hidden>
    <div class="panel">
      <h2>Gambar utama</h2>
      <?php if ($utama && $utama['file_path']): ?><img class="media-thumb" src="<?= e(media_url($utama['file_path'])) ?>" alt="" style="margin-bottom:14px"><?php endif; ?>
      <div class="field"><label for="f-foto">Unggah gambar utama (jpg/png/webp, maks 5 MB)</label><input id="f-foto" type="file" name="foto_utama" accept="image/jpeg,image/png,image/webp"></div>
      <div class="field"><label for="f-alt">Alt text (deskripsi untuk pembaca layar & SEO)</label><input id="f-alt" type="text" name="alt_text" value="<?= e($utama['alt_text'] ?? '') ?>"></div>
      <div class="field"><label for="f-caption">Keterangan foto</label><textarea id="f-caption" name="caption" rows="2"><?= e($utama['caption'] ?? '') ?></textarea></div>
      <div class="field-row">
        <div class="field"><label for="f-kredit">Kredit / fotografer</label><input id="f-kredit" type="text" name="credit_name" value="<?= e($utama['credit_name'] ?? '') ?>"></div>
        <div class="field"><label for="f-kredit-url">URL sumber kredit</label><input id="f-kredit-url" type="url" name="credit_url" value="<?= e($utama['credit_url'] ?? '') ?>"></div>
      </div>
      <div class="field-row">
        <div class="field"><label for="f-lisensi">Lisensi (mis. CC BY-SA 4.0)</label><input id="f-lisensi" type="text" name="license" value="<?= e($utama['license'] ?? '') ?>"></div>
        <div class="field"><label for="f-lisensi-url">URL lisensi</label><input id="f-lisensi-url" type="url" name="license_url" value="<?= e($utama['license_url'] ?? '') ?>"></div>
      </div>
    </div>
    <div class="panel">
      <h2>Galeri tambahan</h2>
      <div class="field"><label for="f-galeri">Unggah beberapa gambar sekaligus</label><input id="f-galeri" type="file" name="galeri[]" accept="image/jpeg,image/png,image/webp" multiple></div>
      <?php if ($galeri || $utama): ?>
      <div class="row-list">
        <?php foreach (array_merge($utama ? [$utama] : [], $galeri) as $m): ?>
        <div class="media-row">
          <?php if ($m['file_path']): ?><img class="media-thumb" src="<?= e(media_url($m['file_path'])) ?>" alt=""><?php else: ?><div class="media-thumb"></div><?php endif; ?>
          <div class="grow"><?= e($m['alt_text'] ?: 'Tanpa alt text') ?><br><?= e($m['credit_name'] ?? '') ?></div>
          <div class="actions">
            <label class="check"><input type="radio" name="utama_id" value="<?= (int)$m['id'] ?>" <?= $m['is_primary'] ? 'checked' : '' ?>> Utama</label>
            <label class="check"><input type="checkbox" name="hapus_media[]" value="<?= (int)$m['id'] ?>"> Hapus</label>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <p class="hint" style="font-size:11px;color:var(--muted);margin-top:10px">Perubahan utama/hapus diterapkan saat tombol simpan ditekan.</p>
      <?php endif; ?>
    </div>
    <div class="panel" id="video-fields" <?= $nilai['format'] === 'video' ? '' : 'hidden' ?>>
      <h2>Video</h2>
      <div class="field"><label for="f-embed">URL YouTube (opsional bila unggah mp4)</label><input id="f-embed" type="url" name="embed_url" value="<?= e($video['embed_url'] ?? '') ?>" placeholder="https://www.youtube.com/watch?v=…"></div>
      <div class="field"><label for="f-video">Unggah video mp4/webm (maks 64 MB)</label><input id="f-video" type="file" name="video_file" accept="video/mp4,video/webm"><?php if (!empty($video['file_path'])): ?><p class="hint">File saat ini: <?= e($video['file_path']) ?></p><?php endif; ?></div>
      <div class="field"><label for="f-poster">Poster video (opsional; bawaan memakai gambar utama)</label><input id="f-poster" type="file" name="poster" accept="image/jpeg,image/png,image/webp"></div>
      <div class="field"><label for="f-durasi">Durasi (detik)</label><input id="f-durasi" type="number" name="duration_seconds" min="0" value="<?= e((string)($video['duration_seconds'] ?? '')) ?>"></div>
      <div class="field"><label for="f-transkrip">Transkrip (satu segmen per baris)</label><textarea id="f-transkrip" name="transcript" rows="5"><?= e($video['transcript'] ?? '') ?></textarea></div>
    </div>
  </section>
  <section class="tab-panel" id="tab-seo" hidden>
    <div class="panel">
      <h2>SEO</h2>
      <div class="field"><label for="f-meta-title">Judul halaman (opsional, bawaan memakai judul)</label><input id="f-meta-title" type="text" name="meta_title" maxlength="200" value="<?= e($nilai['meta_title']) ?>"></div>
      <div class="field"><label for="f-meta-desc">Deskripsi pencarian (opsional, bawaan memakai ringkasan)</label><textarea id="f-meta-desc" name="meta_description" maxlength="300" rows="3"><?= e($nilai['meta_description']) ?></textarea></div>
    </div>
    <div class="panel">
      <h2>Agenda</h2>
      <p class="hint" style="margin-bottom:14px">Untuk konten acara: waktu acara berbeda dari waktu terbit artikel.</p>
      <div class="field-row">
        <div class="field"><label for="f-event-start">Mulai acara</label><input id="f-event-start" type="datetime-local" name="event_start" value="<?= e($nilai['event_start']) ?>"></div>
        <div class="field"><label for="f-event-end">Selesai acara</label><input id="f-event-end" type="datetime-local" name="event_end" value="<?= e($nilai['event_end']) ?>"></div>
      </div>
    </div>
  </section>
  </div>
  <div class="page-actions">
    <button class="btn btn-primary" type="submit" name="aksi" value="simpan">Simpan<?= $user['role'] === 'penulis' ? ' draf' : '' ?></button>
    <?php if ($user['role'] === 'penulis' && in_array($statusSaatIni, ['draf', 'revisi'], true)): ?>
    <button class="btn btn-lime" type="submit" name="aksi" value="submit">Kirim ke tinjauan</button>
    <?php endif; ?>
    <?php if (is_editor()): ?>
      <?php if ($statusSaatIni !== 'arsip'): ?><button class="btn btn-lime" type="submit" name="aksi" value="terbit">Terbitkan</button><?php endif; ?>
      <?php if ($statusSaatIni === 'ditinjau'): ?><button class="btn btn-outline" type="submit" name="aksi" value="revisi">Minta revisi</button><?php endif; ?>
      <?php if ($statusSaatIni === 'terbit'): ?><button class="btn btn-outline" type="submit" name="aksi" value="arsip">Arsipkan</button><?php endif; ?>
    <?php endif; ?>
    <a class="btn btn-outline" href="<?= url('redaksi/artikel.php') ?>">Kembali</a>
  </div>
  <?php if (is_editor() && $statusSaatIni === 'ditinjau'): ?>
  <div class="field"><label for="f-catatan">Catatan revisi (wajib bila meminta revisi)</label><textarea id="f-catatan" name="catatan" rows="2"><?= e($catatan) ?></textarea></div>
  <?php endif; ?>
</form>
<aside class="side-panel">
  <div class="panel">
    <h2>Status</h2>
    <p style="margin-bottom:12px"><span class="badge <?= e($statusSaatIni) ?>"><?= e(status_label($statusSaatIni)) ?></span><?= !empty($a['is_featured']) ? ' <span class="badge terbit">★ Pilihan redaksi</span>' : '' ?></p>
    <?php if ($a): ?>
    <p class="hint">Dibuat <?= e(tanggal_id($a['created_at'], false, true)) ?><br>Terbit <?= $a['published_at'] ? e(tanggal_id($a['published_at'], false, true)) : '—' ?><br>Penulis: <?= e($a['author_name']) ?><?php if ($a['editor_name']): ?><br>Editor: <?= e($a['editor_name']) ?><?php endif; ?></p>
    <div class="page-actions" style="margin:16px 0 0">
      <a class="btn btn-outline btn-sm" href="<?= url('redaksi/pratinjau.php?id=' . $id) ?>">Pratinjau</a>
      <?php if ($statusSaatIni === 'terbit'): ?><a class="btn btn-outline btn-sm" href="<?= url(($a['format'] === 'video' ? 'video/' : 'berita/') . $a['slug']) ?>" target="_blank" rel="noreferrer">Lihat publik</a><?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php if (is_editor() && $a && $statusSaatIni === 'terbit'): ?>
  <div class="panel">
    <h2>Pilihan redaksi</h2>
    <form method="post" action="<?= url('redaksi/artikel-aksi.php') ?>">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="kembali" value="artikel-form.php?id=<?= $id ?>">
      <button class="btn <?= $a['is_featured'] ? 'btn-outline' : 'btn-lime' ?> btn-sm" type="submit" name="aksi" value="featured"><?= $a['is_featured'] ? 'Lepas dari pilihan redaksi' : 'Jadikan pilihan redaksi' ?></button>
    </form>
  </div>
  <div class="panel">
    <h2>Catatan koreksi</h2>
    <form method="post" action="<?= url('redaksi/artikel-aksi.php') ?>">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>"><input type="hidden" name="aksi" value="koreksi"><input type="hidden" name="kembali" value="artikel-form.php?id=<?= $id ?>">
      <div class="field"><label for="f-koreksi">Koreksi / pembaruan (tampil di halaman publik)</label><textarea id="f-koreksi" name="catatan" rows="3" required></textarea></div>
      <button class="btn btn-outline btn-sm" type="submit">Publikasikan koreksi</button>
    </form>
  </div>
  <?php endif; ?>
  <?php if ($logs): ?>
  <div class="panel">
    <h2>Riwayat</h2>
    <div class="log-list">
      <?php foreach (array_slice($logs, 0, 10) as $lg): ?>
      <div class="log-item"><b><?= e($lg['user_name'] ?? 'Sistem') ?> · <?= e($lg['action']) ?></b><span><?= e(tanggal_id($lg['created_at'], false, true)) ?></span><?php if ($lg['note']): ?><p><?= e($lg['note']) ?></p><?php endif; ?></div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</aside>
</div>
<?php layout_selesai(); ?>