<?php
declare(strict_types=1);

function artikel_url(array $a): string
{
    return url(($a['format'] === 'video' ? 'video/' : 'berita/') . $a['slug']);
}

function kartu_meta(array $a): string
{
    $out = '<div class="meta">';
    if (!empty($a['location_name'])) $out .= '<span>' . e($a['location_name']) . '</span><span class="dot"></span>';
    $out .= '<time datetime="' . e(date('Y-m-d', strtotime((string)$a['published_at']))) . '">' . e(tanggal_id($a['published_at'])) . '</time><span class="dot"></span>';
    $out .= '<span>' . e($a['format'] === 'video' ? durasi_video((int)($a['video_duration'] ?? 0)) ?: 'Video' : baca_menit((string)$a['content'])) . '</span></div>';
    return $out;
}

function kartu_thumb(array $a, bool $lazy = true): string
{
    $src = media_url($a['thumb_path'] ?? '');
    if ($src === '') return '<div class="thumb thumb-empty" aria-hidden="true"></div>';
    return '<img src="' . e($src) . '" alt="' . e($a['thumb_alt'] ?? $a['title']) . '" ' . ($lazy ? 'loading="lazy"' : 'fetchpriority="high"') . ' decoding="async">';
}

function kartu_row(array $a): string
{
    return '<a class="news-row" href="' . e(artikel_url($a)) . '"><div><span class="category">' . e($a['category_name']) . '</span><h3>' . e($a['title']) . '</h3><p>' . e($a['deck']) . '</p>' . kartu_meta($a) . '</div><div class="thumb">' . kartu_thumb($a) . '</div></a>';
}

function kartu_grid(array $a): string
{
    return '<article class="grid-story"><a href="' . e(artikel_url($a)) . '"><div class="thumb">' . kartu_thumb($a) . '</div><span class="category">' . ($a['format'] === 'video' ? 'VIDEO · ' : '') . e($a['category_name']) . '</span><h2>' . e($a['title']) . '</h2><p>' . e($a['deck']) . '</p>' . kartu_meta($a) . '</a></article>';
}

function kartu_side(array $a): string
{
    return '<a class="side-story" href="' . e(artikel_url($a)) . '"><div class="thumb">' . kartu_thumb($a, false) . '</div><div><span class="category">' . e($a['category_name']) . '</span><h2>' . e($a['title']) . '</h2>' . kartu_meta($a) . '</div></a>';
}

function kartu_video(array $a): string
{
    return '<a class="video-card" href="' . e(artikel_url($a)) . '">' . kartu_thumb($a) . '<span class="play-dot"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="url(#dg-sun) #FBBC05" stroke="rgba(255,255,255,.85)" stroke-width="1.2" stroke-linejoin="round" d="M9.4 7.6c0-.8.9-1.3 1.6-.9l7 4.4c.7.4.7 1.4 0 1.8l-7 4.4c-.7.4-1.6-.1-1.6-.9V7.6Z"/></svg></span><div class="video-copy"><h3>' . e($a['title']) . '</h3><span>' . e(durasi_video((int)($a['video_duration'] ?? 0)) ?: 'Video') . '</span></div></a>';
}

function chips_kategori(string $selected = ''): string
{
    $out = '<div class="topic-nav" aria-label="Kategori berita"><a class="chip ' . ($selected === '' ? 'active' : '') . '" href="' . url('jelajah') . '">Semua</a>';
    foreach (kategori_semua() as $c) {
        $aktif = $selected === $c['slug'];
        $out .= '<a class="chip ' . ($aktif ? 'active' : '') . '" href="' . url('kategori/' . $c['slug']) . '" ' . ($aktif ? 'aria-current="page"' : '') . '>' . e($c['name']) . '</a>';
    }
    return $out . '</div>';
}