-- depokin — data contoh dari mockup (bukan berita terverifikasi)
USE depokin;

INSERT INTO users (id, name, email, password_hash, role, bio) VALUES
(1, 'Administrator Depokin', 'admin@depokin.test', '$2y$10$CIasgf6LV7j1vvcUNi6tYeCfhYg6nqlIioc99ih4GBykdDUoKO3Qy', 'admin', 'Pengelola sistem dan akun redaksi Depokin.'),
(2, 'Rani Lestari', 'editor@depokin.test', '$2y$10$nF1v6bob81Hb4xfgYZLrv.xx0W6Ly1oILCBfOci0xtbzF8ix792li', 'editor', 'Editor rubrik kabar kota dan komunitas.'),
(3, 'Bima Saputra', 'penulis@depokin.test', '$2y$10$msxJgGgKrCLzG7TkQZ2OIuKZyFuidfAIg9sInJP70wjV4aht3SLl.', 'penulis', 'Penulis lepas, senang berjalan kaki menyusuri Depok.'),
(4, 'Sari Wulandari', 'sari@depokin.test', '$2y$10$msxJgGgKrCLzG7TkQZ2OIuKZyFuidfAIg9sInJP70wjV4aht3SLl.', 'penulis', 'Penulis rubrik kuliner dan agenda akhir pekan.');

INSERT INTO categories (id, name, slug, description) VALUES
(1, 'Kabar Kota', 'kabar-kota', 'Kabar sehari-hari dan perkembangan kota Depok.'),
(2, 'Kuliner', 'kuliner', 'Cerita rasa, tempat makan, dan pelaku usaha kuliner Depok.'),
(3, 'Komunitas', 'komunitas', 'Kegiatan warga, komunitas, dan ruang bertemu di sekitar kita.'),
(4, 'Agenda', 'agenda', 'Kegiatan, acara, dan rencana akhir pekan di Depok.'),
(5, 'Transportasi', 'transportasi', 'Mobilitas warga: komuter, stasiun, dan jalan kota.');

INSERT INTO locations (id, name, slug, kind) VALUES
(1, 'Depok', 'depok', 'area'),
(2, 'Margonda', 'margonda', 'area'),
(3, 'Pondok Cina', 'pondok-cina', 'kelurahan'),
(4, 'Beji', 'beji', 'kecamatan'),
(5, 'Pancoran Mas', 'pancoran-mas', 'kecamatan');

INSERT INTO tags (id, name, slug) VALUES
(1, 'Ruang hijau', 'ruang-hijau'),
(2, 'Kopi lokal', 'kopi-lokal'),
(3, 'Komuter', 'komuter'),
(4, 'Kuliner lokal', 'kuliner-lokal'),
(5, 'Komunitas warga', 'komunitas-warga'),
(6, 'Akhir pekan', 'akhir-pekan'),
(7, 'Video pendek', 'video-pendek');

INSERT INTO articles (id, title, slug, deck, content, format, category_id, location_id, author_id, editor_id, status, is_featured, published_at, created_at, updated_at) VALUES
(1, 'Mencari jeda di antara ruang hijau Depok', 'ruang-hijau-depok',
 'Di balik ritme kota yang bergerak cepat, selalu ada ruang untuk berjalan pelan dan melihat Depok dari dekat.',
 '<p>Di balik ritme kota yang bergerak cepat, selalu ada ruang untuk berjalan pelan dan melihat Depok dari dekat. Melalui rubrik kabar kota, Depokin ingin mengajak pembaca melihat kisah yang ada di sekitar mereka.</p><p>Di halaman ini, foto menjadi pembuka cerita. Teks memberi konteks: apa yang terjadi, siapa yang terlibat, di mana lokasinya, dan mengapa hal itu penting bagi warga Depok.</p><h2>Cerita yang dekat dengan keseharian</h2><p>Sebuah liputan lokal dapat dimulai dari hal sederhana—tempat yang sering dilewati, aktivitas warga, atau percakapan di lingkungan sekitar. Setiap cerita kemudian dilengkapi informasi yang jelas dan sumber yang bisa diperiksa.</p><blockquote>“Kota bukan hanya tempat tinggal, tetapi juga cerita yang kita bagi.”</blockquote><p>Petikan di atas merupakan contoh gaya editorial, bukan kutipan narasumber. Pada berita yang diterbitkan, bagian ini diisi hasil wawancara yang sudah dikonfirmasi.</p><h2>Mengenal Depok, satu cerita setiap hari</h2><p>Berita terkait di bawah membantu pembaca melanjutkan penelusuran. Kategori dan lokasi yang konsisten membuat cerita tentang Depok lebih mudah ditemukan kembali.</p>',
 'foto', 1, 1, 3, 2, 'terbit', 1, '2026-11-17 08:00:00', '2026-11-16 09:00:00', '2026-11-17 08:00:00'),
(2, 'Secangkir kopi, banyak cerita di Margonda', 'cerita-kopi-margonda',
 'Mengenal tempat singgah dan orang-orang yang membuat sudut kota terasa lebih dekat.',
 '<p>Mengenal tempat singgah dan orang-orang yang membuat sudut kota terasa lebih dekat. Kedai kopi di sepanjang Margonda bukan sekadar tempat singgah, tetapi ruang bertemu warga dari berbagai latar.</p><h2>Tempat singgah yang jadi ruang cerita</h2><p>Di balik meja bar, ada pelaku usaha yang membangun mimpinya perlahan. Setiap cangkir menyimpan percakapan: tugas kuliah, kabar kerja, sampai rencana akhir pekan.</p><blockquote>“Kedai yang baik membuat orang merasa pulang, meski baru pertama datang.”</blockquote><p>Liputan kuliner Depokin menelusuri cerita di balik menu—usaha, keluarga, dan lingkungan yang tumbuh bersama.</p>',
 'foto', 2, 2, 4, 2, 'terbit', 0, '2026-11-17 07:00:00', '2026-11-16 10:00:00', '2026-11-17 07:00:00'),
(3, 'Cerita pagi dari perjalanan komuter Depok', 'perjalanan-komuter-depok',
 'Dari langkah menuju stasiun hingga perjalanan pulang, beginilah sebuah cerita kota bisa dimulai.',
 '<p>Dari langkah menuju stasiun hingga perjalanan pulang, beginilah sebuah cerita kota bisa dimulai. Pagi di Stasiun Pondok Cina adalah potret kecil mobilitas warga Depok.</p><h2>Ritme pagi di peron</h2><p>Antrean yang tertib, langkah yang bergegas, dan percakapan singkat di antara penumpang menjadi denyut yang berulang setiap hari kerja.</p><p>Cerita transportasi bukan hanya soal jadwal kereta, tetapi juga tentang orang-orang yang menggantungkan harapannya pada perjalanan yang lancar.</p>',
 'foto', 5, 3, 3, 2, 'terbit', 0, '2026-11-17 06:00:00', '2026-11-16 11:00:00', '2026-11-17 06:00:00'),
(4, 'Menjelajah rasa, mengenal cerita di balik kuliner Depok', 'kuliner-akhir-pekan',
 'Bukan sekadar soal menu. Ada kisah usaha, keluarga, dan lingkungan yang tumbuh bersama.',
 '<p>Bukan sekadar soal menu. Ada kisah usaha, keluarga, dan lingkungan yang tumbuh bersama. Akhir pekan adalah waktu terbaik untuk menjelajah rasa di Beji dan sekitarnya.</p><h2>Usaha yang tumbuh bersama lingkungan</h2><p>Warung yang bertahan puluhan tahun biasanya menyimpan lebih dari resep: ada hubungan dengan tetangga, pelanggan tetap, dan cerita lintas generasi.</p><blockquote>“Rasa yang diingat orang sering kali adalah rasa kebersamaan.”</blockquote><p>Depokin mencatat tempat-tempat itu beserta orang di baliknya, dengan informasi yang bisa diperiksa pembaca.</p>',
 'foto', 2, 4, 4, 2, 'terbit', 0, '2026-11-16 15:00:00', '2026-11-15 13:00:00', '2026-11-16 15:00:00'),
(5, 'Dari tetangga jadi teman: cerita komunitas di sekitar kita', 'komunitas-di-sekitar-kita',
 'Kegiatan sederhana dapat menjadi awal pertemuan dan ruang untuk saling mengenal.',
 '<p>Kegiatan sederhana dapat menjadi awal pertemuan dan ruang untuk saling mengenal. Di Pancoran Mas, komunitas warga tumbuh dari hal kecil: kerja bakti, kelas membaca, sampai kebun bersama.</p><h2>Ruang bertemu yang tumbuh dari bawah</h2><p>Tidak semua komunitas butuh nama besar. Beberapa cukup berawal dari grup percakapan dan kemauan untuk bertemu rutin.</p><p>Cerita komunitas mengingatkan bahwa kota yang dekat adalah kota yang warganya saling mengenal.</p>',
 'foto', 3, 5, 3, 2, 'terbit', 0, '2026-11-16 10:00:00', '2026-11-15 14:00:00', '2026-11-16 10:00:00'),
(6, 'Menyusun akhir pekan yang lebih dekat dengan kota sendiri', 'ide-akhir-pekan-depok',
 'Sebuah contoh rubrik agenda: ruang untuk menemukan kegiatan, tempat, dan pertemuan di Depok.',
 '<p>Sebuah contoh rubrik agenda: ruang untuk menemukan kegiatan, tempat, dan pertemuan di Depok. Tidak perlu jauh untuk menikmati akhir pekan yang berkesan.</p><h2>Agenda sederhana di sekitar kita</h2><p>Berjalan di ruang hijau, mengunjungi pasar pagi, atau ikut kegiatan komunitas bisa menjadi rencana yang dekat dengan keseharian.</p><p>Rubrik agenda memuat waktu acara yang berbeda dari waktu artikel diterbitkan, agar pembaca tidak keliru merencanakan kunjungan.</p>',
 'foto', 4, 1, 4, 2, 'terbit', 0, '2026-11-15 09:00:00', '2026-11-14 16:00:00', '2026-11-15 09:00:00'),
(7, 'Satu menit melihat sisi hijau kota', 'video-sudut-hijau',
 'Konsep video pendek: suasana ruang hijau, langkah warga, dan jeda dari keramaian.',
 '<p>Konsep video pendek: suasana ruang hijau, langkah warga, dan jeda dari keramaian. Video ini menjadi contoh rubrik Depok dalam frame.</p><h2>Kota kita, lewat cerita</h2><p>Transkrip tersedia di bawah pemutar agar isi video tetap dapat dibaca tanpa menjalankannya.</p>',
 'video', 1, 1, 3, 2, 'terbit', 0, '2026-11-17 09:00:00', '2026-11-16 15:00:00', '2026-11-17 09:00:00'),
(8, 'Di balik secangkir kopi lokal', 'video-kopi-lokal',
 'Konsep liputan video: mengenal pelaku usaha melalui cerita di balik meja bar.',
 '<p>Konsep liputan video: mengenal pelaku usaha melalui cerita di balik meja bar. Dua menit mengenal orang-orang di balik kedai kopi Margonda.</p><h2>Cerita di balik meja bar</h2><p>Transkrip tersedia di bawah pemutar agar isi video tetap dapat dibaca tanpa menjalankannya.</p>',
 'video', 2, 2, 4, 2, 'terbit', 0, '2026-11-16 16:00:00', '2026-11-15 17:00:00', '2026-11-16 16:00:00');

INSERT INTO article_tags (article_id, tag_id) VALUES
(1,1),(1,6),(2,2),(2,4),(3,3),(4,4),(4,6),(5,5),(6,6),(6,1),(7,7),(7,1),(8,7),(8,2);

INSERT INTO media (article_id, kind, file_path, embed_url, alt_text, caption, credit_name, credit_url, license, license_url, poster_path, duration_seconds, transcript, is_primary, sort_order, uploaded_by) VALUES
(1, 'gambar', 'assets/lake.jpg', NULL, 'Foto arsip Danau Kenanga Universitas Indonesia', 'Foto arsip 2023: Danau Kenanga Universitas Indonesia. Ukuran dan potongan tampilan disesuaikan. Ilustrasi, bukan dokumentasi berita terkini.', 'Alif Mikail', 'https://commons.wikimedia.org/wiki/File:Danau_Kenanga_Universitas_Indonesia.jpg', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0/', NULL, NULL, NULL, 1, 0, 3),
(2, 'gambar', 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1000&q=85', NULL, 'Foto ilustrasi kedai kopi', 'Foto ilustrasi dari Unsplash. Bukan foto dokumentasi lokasi atau peristiwa dalam berita.', 'Unsplash', 'https://unsplash.com', NULL, NULL, NULL, NULL, NULL, 1, 0, 4),
(3, 'gambar', 'assets/station.jpg', NULL, 'Foto arsip Concourse Stasiun Pondok Cina', 'Foto arsip 2026: Concourse Stasiun Pondok Cina. Ukuran dan potongan tampilan disesuaikan. Ilustrasi, bukan dokumentasi berita terkini.', 'Irvan Cahyo N', 'https://commons.wikimedia.org/wiki/File:Concourse_Stasiun_Pondok_Cina.jpg', 'CC BY 4.0', 'https://creativecommons.org/licenses/by/4.0/', NULL, NULL, NULL, 1, 0, 3),
(4, 'gambar', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=1000&q=85', NULL, 'Foto ilustrasi suasana tempat makan', 'Foto ilustrasi dari Unsplash. Bukan foto dokumentasi lokasi atau peristiwa dalam berita.', 'Unsplash', 'https://unsplash.com', NULL, NULL, NULL, NULL, NULL, 1, 0, 4),
(5, 'gambar', 'https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=1000&q=85', NULL, 'Foto ilustrasi kebersamaan warga', 'Foto ilustrasi dari Unsplash. Bukan foto dokumentasi lokasi atau peristiwa dalam berita.', 'Unsplash', 'https://unsplash.com', NULL, NULL, NULL, NULL, NULL, 1, 0, 3),
(6, 'gambar', 'assets/margonda.jpg', NULL, 'Foto arsip Jalan Margonda, Depok', 'Foto arsip 2022: Jalan Margonda, Depok. Ukuran dan potongan tampilan disesuaikan. Ilustrasi, bukan dokumentasi berita terkini.', 'Mfikriansori', 'https://commons.wikimedia.org/wiki/File:Jalan_Margonda,_Depok.jpg', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0/', NULL, NULL, NULL, 1, 0, 4),
(7, 'gambar', 'assets/lake.jpg', NULL, 'Poster video: suasana ruang hijau Depok', 'Poster konsep. Foto arsip 2023: Danau Kenanga Universitas Indonesia oleh Alif Mikail (CC BY-SA 4.0).', 'Alif Mikail', 'https://commons.wikimedia.org/wiki/File:Danau_Kenanga_Universitas_Indonesia.jpg', 'CC BY-SA 4.0', 'https://creativecommons.org/licenses/by-sa/4.0/', NULL, NULL, NULL, 1, 0, 3),
(7, 'video', NULL, NULL, NULL, 'Video konsep satu menit sisi hijau kota.', NULL, NULL, NULL, NULL, 'assets/lake.jpg', 60, '00:00 — Pembuka. Setiap sudut kota menyimpan cerita. Hari ini, kita melihat Depok dari dekat.\n00:15 — Cerita utama. Suasana ruang hijau, langkah warga, dan jeda dari keramaian.\n00:45 — Penutup. Kota yang dekat adalah kota yang kita kenal ceritanya.', 0, 1, 3),
(8, 'gambar', 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1000&q=85', NULL, 'Poster video: kedai kopi lokal', 'Poster konsep. Foto ilustrasi dari Unsplash.', 'Unsplash', 'https://unsplash.com', NULL, NULL, NULL, NULL, NULL, 1, 0, 4),
(8, 'video', NULL, NULL, NULL, 'Video konsep dua menit di balik secangkir kopi lokal.', NULL, NULL, NULL, NULL, NULL, 120, '00:00 — Pembuka. Mengenal pelaku usaha melalui cerita di balik meja bar.\n00:20 — Cerita utama. Perjalanan membangun kedai, pelanggan tetap, dan harapan ke depan.\n01:40 — Penutup. Secangkir kopi, banyak cerita.', 0, 1, 4);

INSERT INTO article_logs (article_id, user_id, action, note, created_at) VALUES
(1, 3, 'buat', NULL, '2026-11-16 09:00:00'),
(1, 3, 'submit', NULL, '2026-11-16 14:00:00'),
(1, 2, 'terbit', 'Foto dan kredit sudah diperiksa.', '2026-11-17 08:00:00'),
(2, 4, 'buat', NULL, '2026-11-16 10:00:00'),
(2, 2, 'terbit', NULL, '2026-11-17 07:00:00'),
(3, 3, 'buat', NULL, '2026-11-16 11:00:00'),
(3, 2, 'terbit', NULL, '2026-11-17 06:00:00'),
(4, 4, 'buat', NULL, '2026-11-15 13:00:00'),
(4, 2, 'terbit', NULL, '2026-11-16 15:00:00'),
(5, 3, 'buat', NULL, '2026-11-15 14:00:00'),
(5, 2, 'terbit', NULL, '2026-11-16 10:00:00'),
(6, 4, 'buat', NULL, '2026-11-14 16:00:00'),
(6, 2, 'terbit', NULL, '2026-11-15 09:00:00'),
(7, 3, 'buat', NULL, '2026-11-16 15:00:00'),
(7, 2, 'terbit', NULL, '2026-11-17 09:00:00'),
(8, 4, 'buat', NULL, '2026-11-15 17:00:00'),
(8, 2, 'terbit', NULL, '2026-11-16 16:00:00');

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'depokin'),
('tagline', 'Dekat kotanya. Dekat ceritanya.'),
('site_description', 'Media lokal untuk cerita, kabar, dan kehidupan di Depok. Kabar kota, kuliner, komunitas, agenda, dan transportasi.'),
('about_intro', 'Depokin adalah tempat menemukan informasi tentang Depok lewat foto, video, dan tulisan yang jelas. Topiknya mencakup kabar kota, kuliner, komunitas, agenda, dan transportasi.'),
('about_principles', 'Informasi diperiksa sebelum diterbitkan.\nPenulis, tanggal, sumber, dan lokasi dicantumkan dengan jelas.\nKonten kerja sama diberi label yang mudah dikenali.\nKoreksi berita disertai catatan pembaruan.'),
('contact_email', 'redaksi@depokin.test'),
('footer_note', 'Media lokal Depok · Konten contoh untuk pengembangan.'),
('instagram_url', '');