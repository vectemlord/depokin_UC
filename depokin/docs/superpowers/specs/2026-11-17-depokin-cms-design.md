# depokin — CMS portal kabar Depok (spec desain)

Disetujui user: 17 Nov 2026. Bangun seluruh cakupan sekaligus.

## Stack
PHP 8 native (PDO MySQL/MariaDB), vanilla HTML/CSS/JS, XAMPP `http://localhost/depokin/`.
Tampilan publik mengikuti mockup editorial yang ada (`styles.css`): putih hangat, hijau gelap, aksen lime, mobile-first, bottom nav 4 item. Panel redaksi memakai bahasa desain sama. Deploy target: cPanel File Manager (tanpa build step).

## Database (3NF) — `database/schema.sql`
- `users` — identitas tunggal; role ENUM(admin,editor,penulis); password_hash; is_active.
- `categories`, `locations`, `tags` — nama/slug tidak diduplikasi di artikel.
- `articles` — FK category_id, location_id, author_id, editor_id; status ENUM(draf,ditinjau,revisi,terbit,arsip); format ENUM(foto,video,teks); is_featured; meta SEO; event_start/end untuk Agenda; published_at.
- `article_tags` — PK gabungan (article_id, tag_id).
- `media` — milik artikel; kind gambar/video; file_path atau embed_url; alt, caption, kredit (nama/url), lisensi (nama/url) per media; poster_path, duration_seconds, transcript; is_primary, sort_order.
- `article_logs` — jejak workflow (buat/simpan/submit/revisi/terbit/arsip/koreksi/featured) + catatan; koreksi tampil di halaman publik.
- `settings` — key/value: identitas situs, isi Tentang, kontak.

Tidak ada redundansi: kredit melekat per media, nama kategori/lokasi/tag hanya di tabel master, penulis/editor FK ke users.

## Peran & workflow
- Penulis: CRUD artikel miliknya (draf/revisi), unggah media, submit tinjauan. Tidak bisa terbit.
- Editor: edit semua artikel, minta revisi + catatan, terbitkan/arsipkan, featured, kelola kategori/tag/lokasi, koreksi.
- Administrator: hak editor + kelola pengguna + pengaturan situs + hapus artikel apa pun.

Alur: draf → ditinjau → (revisi ↔ ditinjau) → terbit → arsip. Semua transisi tercatat di article_logs. Artikel terbit terkunci bagi penulis; koreksi oleh editor/admin memperbarui updated_at dan tampil sebagai catatan pembaruan.

## Fitur publik
Beranda (pilihan redaksi/featured, hero + 2 samping, kabar terbaru, video pilihan, chips kategori), Jelajah (filter kategori + lokasi, paginasi), pencarian server-side (judul/ringkasan/isi/kategori/lokasi), detail berita (byline penulis & editor, tanggal terbit/perbaruan, kredit & lisensi media, tag, berita terkait, catatan koreksi, agenda event), halaman video (embed YouTube atau mp4 unggahan + poster + durasi + transkrip), kategori, Tentang (dari settings). SEO: URL slug bersih (.htaccess), meta + OG + canonical, JSON-LD NewsArticle/VideoObject, sitemap.xml, gambar lazy + alt. Share: Web Share API fallback salin tautan.

## Fitur CMS (`redaksi/`)
Login (password_hash, session regenerate, throttle gagal login), CSRF semua form, guard role per halaman. Dashboard statistik per peran. Artikel: list + filter status, form dengan tab (Konten / Media / SEO & Agenda), editor format ringan contenteditable (p, h2, h3, bold, italic, kutipan, list, tautan) + sanitizer allowlist server-side. Upload gambar (jpeg/png/webp ≤5MB) & video mp4 (≤64MB) nama acak + validasi MIME finfo; embed YouTube. Tinjauan: antrean editor dengan catatan revisi. Taxonomi (kategori/tag/lokasi) dan pengguna: form di modal `<dialog>`, list tetap terlihat. Pengaturan situs + profil (ganti password/bio).

## Keamanan
Prepared statements, escape output (e()), sanitizer HTML allowlist, validasi upload MIME + ekstensi, uploads tanpa eksekusi PHP, session cookie httponly/SameSite=Lax, slug unik otomatis, htmlspecialchars semua data DB di view.

## Seed & akun demo
8 artikel contoh dari mockup (status terbit, berlabel contoh di catatan), 3 lokasi + 5 kategori + tag, kredit foto arsip sesuai assets/credits.json.
- admin@depokin.test / Admin#2026
- editor@depokin.test / Editor#2026
- penulis@depokin.test / Penulis#2026

## Verifikasi
Browser via XAMPP: alur penuh tiap peran (tulis→submit→revisi→terbit→publik), pencarian/filter/video/upload, sitemap & JSON-LD, responsif 320/390/1440, tanpa error console/PHP.