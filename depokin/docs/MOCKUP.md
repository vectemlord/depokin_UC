# depokin — rancangan portal kabar Depok

**Status: mockup interaktif, bukan layanan berita langsung.** Semua judul, tanggal, isi, dan media pada prototipe adalah contoh untuk menilai desain. Belum ada backend, CMS, alur verifikasi redaksi, atau implementasi SEO produksi. Foto ilustrasi tidak membuktikan lokasi atau peristiwa yang disebutkan.

## Arah pengalaman

depokin membantu warga menemukan kabar dan cerita mengenai Depok melalui foto, video, serta berita singkat yang mudah dibaca di ponsel. Identitas media dibangun melalui liputan yang konsisten, atribusi yang jelas, dan informasi lokal yang berguna.

- Satu cerita utama per layar; pembaca langsung memahami apa yang terjadi dan mengapa relevan.
- Foto besar, judul jelas, ringkasan singkat, serta waktu terbit yang mudah ditemukan.
- Tata letak editorial ringan dengan ruang kosong cukup dan satu warna aksen.
- Pembaca dapat menemukan berita lewat kategori, lokasi, atau pencarian.
- Tampilan desktop memperluas tata letak; urutan konten tetap mengikuti prioritas mobile.

## Sitemap dan navigasi

| Navigasi | Isi utama | Rute produksi yang diusulkan |
| --- | --- | --- |
| Beranda | Pilihan redaksi, kabar terbaru, dan video pilihan | `/` |
| Jelajah | Pencarian, kategori, dan lokasi liputan | `/jelajah/` |
| Video | Daftar video dan halaman menonton | `/video/`, `/video/{slug}/` |
| Tentang | Identitas media, redaksi, kontak, dan kebijakan koreksi | `/tentang/` |
| Detail berita | Liputan lengkap dengan foto atau video | `/berita/{slug}/` |
| Kategori | Pengantar topik dan artikel relevan | `/kategori/{slug}/` |

Empat navigasi utama tampil di bagian bawah pada mobile. Logo dan tombol pencarian berada di header. Kategori utama: **Kabar Kota, Kuliner, Komunitas, Agenda, Transportasi**.

## Wireframe beranda

```text
Logo depokin                         Pencarian
Kabar dan cerita dari Depok
Kategori yang dapat digeser
┌──────────────────────────────────────────┐
│ Foto berita utama                        │
│ Kategori · lokasi                        │
│ Judul utama                              │
│ Ringkasan · waktu terbit                  │
└──────────────────────────────────────────┘
Kabar terbaru                         Semua
Thumbnail | Judul · kategori · waktu
Thumbnail | Judul · kategori · waktu
Video pilihan
Poster · tombol putar · durasi · judul
Kabar berdasarkan topik atau wilayah
Identitas penerbit dan tautan informasi media
Beranda       Jelajah       Video       Tentang
```

Utamakan foto 4:3 untuk cerita utama mobile dan poster 16:9 untuk video. Pada desktop, berita utama dapat berdampingan dengan daftar kabar terbaru. Jangan tampilkan angka pembaca atau klaim popularitas tanpa data.

## Wireframe detail berita

```text
Kembali                         Bagikan
Beranda > Kategori
Kategori · lokasi
Judul berita
Ringkasan yang menjelaskan inti informasi
Penulis · waktu terbit · waktu pembaruan
Foto atau video utama
Keterangan media · kredit/sumber
Teks pembuka: apa, siapa, kapan, di mana
Subjudul dan paragraf pendukung
Sumber, konteks, dan koreksi bila diperlukan
Topik terkait
Berita terkait
```

Halaman menonton menempatkan video sebagai konten utama, disertai judul, deskripsi, durasi, tanggal, dan transkrip bila tersedia. Video tidak perlu diputar otomatis. Teks artikel tetap dapat dibaca tanpa menjalankan video.

## Model konten yang disarankan

| Kelompok | Data |
| --- | --- |
| Identitas | ID, judul, slug, ringkasan, isi, jenis konten: berita/foto/video |
| Pengelompokan | Kategori, tag relevan, kecamatan/kelurahan atau lokasi liputan |
| Redaksi | Penulis, editor, sumber informasi, status draf/tinjau/terbit, catatan koreksi |
| Waktu | Tanggal terbit dan pembaruan; tanggal/jam acara khusus konten Agenda |
| Media | File/URL, alt teks, keterangan, kredit, hak penggunaan, poster dan durasi video |
| Penemuan | Judul halaman, deskripsi pencarian, canonical, tautan berita terkait |

Agenda perlu membedakan waktu acara dari waktu artikel diterbitkan. Informasi lokasi harus diverifikasi; jangan menambahkan nama wilayah hanya untuk mengejar kata kunci.

## Rencana SEO lokal untuk tahap produksi

1. Sediakan URL tetap untuk setiap artikel dan video, dengan isi utama yang dapat dirayapi. Buat halaman kategori dan wilayah hanya bila tersedia liputan yang cukup dan pengantar yang berguna.
2. Gunakan judul yang menerangkan peristiwa serta lokasi yang benar, misalnya nama kelurahan atau kecamatan bila relevan. Hindari pengulangan kata “Depok” yang tidak membantu pembaca.
3. Tampilkan identitas penulis, waktu terbit/pembaruan, sumber, dan koreksi. Terapkan `Article` atau `NewsArticle` sesuai jenis konten dengan metadata yang sama dengan informasi yang terlihat. Markup membantu Google memahami artikel; tidak menjamin peringkat atau tampilan khusus. [Panduan Article Google](https://developers.google.com/search/docs/appearance/structured-data/article)
4. Gunakan elemen HTML gambar, alt deskriptif, nama file yang relevan, serta caption dan kredit. Sediakan gambar responsif; gambar utama tidak hanya menjadi CSS background. [Panduan gambar Google](https://developers.google.com/search/docs/appearance/google-images)
5. Buat halaman menonton khusus untuk video utama, dengan thumbnail valid pada URL stabil dan metadata yang sesuai. Kelayakan fitur video bergantung pada persyaratan pengindeksan, bukan sekadar adanya tombol putar. [Panduan video Google](https://developers.google.com/search/docs/appearance/video)
6. Rencanakan sitemap, canonical, preview berbagi, performa mobile, dan pemantauan Google Search Console setelah domain serta sistem penerbitan tersedia. Validasi pengindeksan dan markup pada implementasi nyata.

## Batas dan tahap selanjutnya

Prototipe memvalidasi hierarki informasi, gaya visual, navigasi, dan alur membaca. Konten contoh tidak boleh dipublikasikan sebagai fakta terkini. Sebelum peluncuran, diperlukan sumber berita terverifikasi, aset dengan izin penggunaan, CMS/backend, akses redaksi, halaman kebijakan, domain, pengujian aksesibilitas/performa, serta penerapan dan pengujian SEO produksi.
