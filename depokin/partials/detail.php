<?php
declare(strict_types=1);

function detail_byline(array $a, bool $video = false): string
{
    $inisial = mb_strtoupper(mb_substr((string)$a['author_name'], 0, 1));
    $out = '<div class="byline"><span class="avatar" aria-hidden="true">' . e($inisial) . '</span><div><strong>' . e($a['author_name']) . '</strong>';
    if (!empty($a['editor_name'])) $out .= ' <span class="byline-editor">· Diedit oleh ' . e($a['editor_name']) . '</span>';
    $out .= '<div class="meta">';
    if (!empty($a['location_name'])) $out .= '<span>' . e($a['location_name']) . '</span><span class="dot"></span>';
    if (!empty($a['published_at'])) {
        $out .= '<time datetime="' . e(date('c', strtotime((string)$a['published_at']))) . '">' . e(tanggal_id($a['published_at'])) . '</time><span class="dot"></span>';
    } else {
        $out .= '<span>Belum terbit</span><span class="dot"></span>';
    }
    $out .= '<span>' . e($video ? (durasi_video((int)($a['video_duration'] ?? 0)) ?: 'Video') : baca_menit((string)$a['content'])) . '</span></div>';
    if (!empty($a['updated_at']) && !empty($a['published_at']) && strtotime((string)$a['updated_at']) > strtotime((string)$a['published_at']) + 60) {
        $out .= '<div class="meta"><span>Diperbarui ' . e(tanggal_id($a['updated_at'], false, true)) . '</span></div>';
    }
    $out .= '</div><button class="share-button" data-share="' . e(artikel_url($a)) . '" data-title="' . e($a['title']) . '" aria-label="Bagikan cerita"><span data-icon="share"></span><span>Bagikan</span></button></div>';
    return $out;
}

function detail_figcaption(array $m): string
{
    $out = e((string)($m['caption'] ?? ''));
    $links = [];
    if (!empty($m['credit_name'])) {
        $links[] = !empty($m['credit_url'])
            ? '<a href="' . e($m['credit_url']) . '" target="_blank" rel="noreferrer">' . e($m['credit_name']) . ' ↗</a>'
            : e($m['credit_name']);
    }
    if (!empty($m['license'])) {
        $links[] = !empty($m['license_url'])
            ? '<a href="' . e($m['license_url']) . '" target="_blank" rel="noreferrer">' . e($m['license']) . '</a>'
            : e($m['license']);
    }
    if ($links) $out .= ($out !== '' ? ' · ' : '') . implode(' · ', $links);
    return $out;
}

function detail_figure(array $m, bool $lazy = false): string
{
    $src = media_url($m['file_path'] ?? '');
    if ($src === '') return '';
    $out = '<figure class="article-figure"><img src="' . e($src) . '" alt="' . e($m['alt_text'] ?? '') . '" ' . ($lazy ? 'loading="lazy"' : 'fetchpriority="high"') . ' decoding="async">';
    $cap = detail_figcaption($m);
    if ($cap !== '') $out .= '<figcaption>' . $cap . '</figcaption>';
    return $out . '</figure>';
}

function detail_event(array $a): string
{
    if (empty($a['event_start'])) return '';
    $out = '<div class="event-box"><strong>Waktu acara</strong><span>' . e(tanggal_id($a['event_start'], true, true));
    if (!empty($a['event_end'])) $out .= ' — ' . e(tanggal_id($a['event_end'], true, true));
    return $out . '</span></div>';
}

function detail_tags(int $articleId): string
{
    $tags = artikel_tags($articleId);
    if (!$tags) return '';
    $out = '<div class="tag-chips" aria-label="Topik terkait">';
    foreach ($tags as $t) {
        $out .= '<a class="chip" href="' . url('jelajah?tag=' . $t['slug']) . '">#' . e($t['name']) . '</a>';
    }
    return $out . '</div>';
}

function detail_koreksi(int $articleId): string
{
    $logs = artikel_koreksi($articleId);
    if (!$logs) return '';
    $out = '<aside class="correction-note"><strong>Catatan koreksi &amp; pembaruan</strong>';
    foreach ($logs as $lg) {
        $out .= '<p>' . e(tanggal_id($lg['created_at'], false, true)) . ' · ' . e($lg['user_name'] ?? 'Redaksi') . ': ' . e((string)$lg['note']) . '</p>';
    }
    return $out . '</aside>';
}

function detail_transkrip(array $mediaVideo): string
{
    if (empty($mediaVideo['transcript'])) return '';
    $out = '<details class="transcript"><summary>Lihat transkrip video</summary>';
    foreach (explode("\n", (string)$mediaVideo['transcript']) as $baris) {
        $baris = trim($baris);
        if ($baris !== '') $out .= '<p>' . e($baris) . '</p>';
    }
    return $out . '</details>';
}