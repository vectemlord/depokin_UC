# depokin — portal berita lokal Depok

CMS berita berbasis web untuk media lokal Depok: **administrator**, **editor**, dan **penulis**, dengan alur kerja redaksi (draf → tinjauan → revisi → terbit → arsip). Frontend publik mengikuti mockup editorial mobile-first (putih hangat, hijau gelap, aksen lime) dengan tagline **Dekat kotanya. Dekat ceritanya.**

Stack: PHP 8.2 native (PDO), MySQL/MariaDB, HTML/CSS/JS vanilla. Tanpa framework, tanpa proses build — siap dijalankan di XAMPP maupun hosting cPanel.

## Menjalankan lokal (XAMPP)

1. Letakkan folder ini di `htdocs/depokin` (sudah sesuai bila memakai XAMPP bawaan).
2. Impor database sekali saja:
   ```bash
   /Applications/XAMPP/xamppfiles/bin/mysql -uroot < database/schema.sql
   /Applications/XAMPP/xamppfiles/bin/mysql -uroot < database/seed.sql
   ```
   `schema.sql` membuat database `depokin` beserta 9 tabel; `seed.sql` mengisi akun demo, rubrik, lokasi, tag, dan 8 artikel contoh dari mockup.
3. Pastikan Apache aktif, lalu buka **http://localhost/depokin/**.
4. Panel redaksi: **http://localhost/depokin/redaksi/**.

Koneksi database diatur di `config/config.php` (default: user `root`, sandi kosong, host `127.0.0.1`). Ubah konstanta tersebut bila environment berbeda.

### Akun demo

| Peran | Email | Kata sandi |
|---|---|---|
| Administrator | admin@depokin.test | Admin#2026 |
| Editor | editor@depokin.test | Editor#2026 |
| Penulis | penulis@depokin.test | Penulis#2026 |
| Penulis | sari@depokin.test | Penulis#2026 |

Ganti sandi akun demo sebelum dipakai produksi (menu **Profil saya**).

## Fitur publik

- Beranda: pilihan redaksi (featured), kabar terbaru, panel video, ticker topik.
- Jelajah Depok: filter lokasi, chips kategori, filter tag, paginasi.
- Pencarian judul/ringkasan/kategori/lokasi (`/cari?q=…`).
- Detail berita `/berita/{slug}` dan video `/video/{slug}` (embed YouTube, MP4/WebM unggahan, transkrip, kredit & lisensi per media).
- Halaman kategori `/kategori/{slug}`, Tentang & prinsip redaksi + daftar kredit foto.
- SEO: URL bersih, canonical, Open Graph, JSON-LD (NewsArticle/VideoObject/WebSite), `sitemap.xml`, lazy-load gambar, alt text wajib untuk gambar utama.
- Kotak agenda/event, catatan koreksi publik, berita terkait, tombol bagikan (Web Share API → fallback clipboard).
- Responsif 320 px sampai desktop; navigasi bawah khusus mobile.

## Fitur redaksi (CMS)

- **Dashboard** statistik per peran + log aktivitas redaksi.
- **Artikel**: daftar dengan tab status, pencarian, paginasi; form tiga tab (Konten / Media / SEO & Agenda).
  - Editor rich-text aman (paragraf, subjudul, kutipan, daftar, tautan) — HTML disanitasi allowlist di server.
  - Slug otomatis/unik, ringkasan, format foto/video/teks, kategori, lokasi, tag (tag baru dibuat otomatis).
  - Media: gambar utama + galeri multi-unggah, alt/keterangan/kredit/lisensi per gambar; video (embed atau unggah MP4/WebM + poster + durasi + transkrip).
  - SEO: meta title/description; agenda: tanggal mulai/selesai event.
- **Alur kerja**: penulis menyimpan draf & mengirim ke tinjauan; editor meninjau, meminta revisi dengan catatan wajib, menerbitkan, mengarsipkan, menjadikan pilihan redaksi, dan menulis koreksi publik. Semua transisi dicatat di `article_logs`.
- **Tinjauan**: antrean artikel masuk + aksi cepat terbitkan / minta revisi (modal).
- **Taksonomi** (editor/admin): CRUD kategori, lokasi, tag; hapus ditolak bila masih dipakai artikel.
- **Pengguna** (admin): CRUD peran, aktif/nonaktif akun, proteksi admin terakhir & proteksi diri sendiri.
- **Pengaturan situs** (admin): nama, tagline, deskripsi, tentang, prinsip redaksi, kontak, catatan footer, Instagram.
- **Profil**: nama, bio, ganti sandi (verifikasi sandi lama).
- **Pratinjau** artikel status apa pun sebelum terbit, dengan tampilan publik.
- Unggahan divalidasi MIME sebenarnya (finfo): jpg/png/webp ≤ 5 MB, mp4/webm ≤ 64 MB; nama file acak; folder `uploads/` menolak eksekusi PHP.

## Keamanan

- PDO prepared statements di semua query; output di-escape (`htmlspecialchars`).
- CSRF token di setiap POST; session cookie httponly + SameSite=Lax.
- Throttle login (5 gagal → kunci 5 menit); guard peran di setiap halaman/aksi.
- Sanitasi HTML konten artikel (allowlist DOMDocument); URL embed/video dibatasi skema http(s).
- `.htaccess`: tolak indeks direktori, tolak akses file `.sql`/`.md`, rewrite URL bersih.

## Struktur singkat

```
config/            konfigurasi & bootstrap
app/               db, auth, helpers, query artikel
database/          schema.sql (3NF) + seed.sql
partials/          header/footer/kartu/detail publik
redaksi/           panel CMS (login, artikel, tinjauan, pengguna, taxonomi, pengaturan, profil)
uploads/           media unggahan (dilindungi .htaccess)
assets/            app.js publik + foto arsip
styles.css         gaya publik (mockup + komponen produksi)
mockup.html        wireframe asli, tetap dapat dibuka
docs/              spesifikasi desain & catatan mockup
```

## Deploy ke hosting (cPanel)

1. Unggah seluruh folder ke `public_html/` (atau subfolder); sesuaikan konstanta DB di `config/config.php`.
2. Impor `database/schema.sql` lalu `database/seed.sql` lewat phpMyAdmin.
3. Pastikan folder `uploads/` dapat ditulis oleh proses PHP (di shared hosting biasanya cukup `755` dengan pemilik akun hosting; di XAMPP macOS lokal dipakai `777` karena Apache berjalan sebagai user `daemon`).
4. `mod_rewrite` harus aktif; blok `php_value` di `.htaccess` hanya berlaku bila PHP berjalan sebagai modul Apache — bila memakai PHP-FPM/LSAPI, atur `upload_max_filesize`/`post_max_size` lewat panel hosting atau `.user.ini`.
5. Ganti sandi akun demo dan perbarui pengaturan situs.

## Verifikasi — 17 November 2026

- Uji curl seluruh alur: buat draf → submit → revisi → submit ulang → terbit → koreksi → featured → hapus; guard peran (penulis ditolak 403 untuk halaman/aksi editor-admin), POST tanpa CSRF ditolak 403, unggahan file berbahaya ditolak.
- Uji browser end-to-end (Chrome headless, puppeteer-core) pada 320/390/1440 px: 9 halaman publik × 3 viewport tanpa overflow horizontal dan tanpa error konsol; dialog pencarian, filter lokasi, share fallback clipboard+toast.
- UI CMS di browser: login tiga peran, tab form, editor rich-text, simpan/submit penulis, modal revisi editor, terbit dari antrean tinjauan, artikel tayang publik, modal pengguna (tambah/edit/toggle), CRUD taksonomi via modal, pengaturan, profil, drawer mobile; artikel uji dihapus dan halaman publiknya kembali 404.
- Database dikembalikan ke kondisi seed (8 artikel, 4 pengguna, 5 kategori) setelah pengujian.
- Restyle glassmorphism ala Instagram (ikon berwarna ala Google) diverifikasi ulang lewat screenshot pada 320/390/1440 px untuk situs publik dan CMS (login, dashboard, form, modal, drawer): tanpa overflow horizontal dan tanpa error konsol; regresi end-to-end serta CRUD modal dijalankan ulang setelah restyle dan lulus. Mockup wireframe asli tetap utuh di `mockup.html` dengan stylesheet terpisah `assets/mockup.css`.

Catatan: pengujian memakai browser desktop/headless, bukan perangkat fisik; kredensial demo adalah data seed untuk pengembangan lokal.