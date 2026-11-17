  </main>
  <footer class="site-footer">
    <div class="footer-inner">
      <a href="<?= url() ?>" class="brand">depokin<span class="brand-dot">.</span></a>
      <p><?= e(setting('tagline', 'Dekat kotanya. Dekat ceritanya.')) ?></p>
      <span><?= e(setting('footer_note')) ?></span>
      <a href="<?= url('tentang') ?>">Tentang &amp; prinsip redaksi <span aria-hidden="true">↗</span></a>
    </div>
  </footer>
  <nav class="bottom-nav" aria-label="Navigasi mobile">
    <a href="<?= url() ?>" <?= $active_nav === 'beranda' ? 'aria-current="page"' : '' ?>><span data-icon="home"></span>Beranda</a>
    <a href="<?= url('jelajah') ?>" <?= $active_nav === 'jelajah' ? 'aria-current="page"' : '' ?>><span data-icon="compass"></span>Jelajah</a>
    <a href="<?= url('video') ?>" <?= $active_nav === 'video' ? 'aria-current="page"' : '' ?>><span data-icon="video"></span>Video</a>
    <a href="<?= url('tentang') ?>" <?= $active_nav === 'tentang' ? 'aria-current="page"' : '' ?>><span data-icon="info"></span>Tentang</a>
  </nav>
  <dialog id="search-dialog" aria-labelledby="search-title">
    <div class="search-head"><h2 id="search-title">Lagi cari apa di Depok?</h2><button class="icon-button" id="close-search" aria-label="Tutup pencarian"><span data-icon="close"></span></button></div>
    <form id="search-form" action="<?= url('cari') ?>" method="get">
      <label class="sr-only" for="search-input">Kata kunci berita</label>
      <div class="search-field"><span data-icon="search"></span><input id="search-input" name="q" type="search" placeholder="Kuliner, Margonda, komunitas…" autocomplete="off"><button type="submit">Cari</button></div>
    </form>
    <p class="search-hint">Cari judul, ringkasan, kategori, dan lokasi liputan.</p>
  </dialog>
  <div class="toast" role="status" id="toast" hidden></div>
  <script src="<?= url('assets/app.js') ?>"></script>
</body>
</html>