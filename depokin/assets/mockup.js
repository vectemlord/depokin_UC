(() => {
  'use strict';
  const icons = {
    search:'<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 4.5 4.5"/>',
    home:'<path d="m3 10 9-7 9 7v10H3Z"/><path d="M9 20v-7h6v7"/>',
    compass:'<circle cx="12" cy="12" r="9"/><path d="m16 8-2.5 5.5L8 16l2.5-5.5Z"/>',
    video:'<rect x="3" y="5" width="18" height="14" rx="4"/><path d="m10 9 5 3-5 3Z"/>',
    info:'<circle cx="12" cy="12" r="9"/><path d="M12 11v6m0-11v1"/>',
    arrow:'<path d="M4 12h16m-6-6 6 6-6 6"/>',
    close:'<path d="m6 6 12 12M6 18 18 6"/>',
    play:'<path d="m9 5 11 7-11 7Z"/>',
    photo:'<rect x="3" y="4" width="18" height="16" rx="3"/><circle cx="8" cy="9" r="1"/><path d="m3 17 6-5 4 3 4-5 4 5"/>',
    share:'<path d="M12 15V3m-5 5 5-5 5 5M5 13v7h14v-7"/>'
  };
  const icon = name => `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${icons[name] || icons.arrow}</svg>`;
  const image = (id, width=1000) => `https://images.unsplash.com/${id}?auto=format&fit=crop&w=${width}&q=85`;
  const photos = {
    park:'assets/lake.jpg',
    coffee:image('photo-1501339847302-ac426a4a7cbb',700),
    train:'assets/station.jpg',
    market:image('photo-1555396273-367ea4eb4db5',800),
    community:image('photo-1511632765486-a01980e01a18',800),
    city:'assets/margonda.jpg'
  };
  const archiveCredits = {
    park: {title:'Danau Kenanga Universitas Indonesia',author:'Alif Mikail',year:'2023',source:'https://commons.wikimedia.org/wiki/File:Danau_Kenanga_Universitas_Indonesia.jpg',license:'CC BY-SA 4.0',licenseUrl:'https://creativecommons.org/licenses/by-sa/4.0/'},
    train: {title:'Concourse Stasiun Pondok Cina',author:'Irvan Cahyo N',year:'2026',source:'https://commons.wikimedia.org/wiki/File:Concourse_Stasiun_Pondok_Cina.jpg',license:'CC BY 4.0',licenseUrl:'https://creativecommons.org/licenses/by/4.0/'},
    city: {title:'Jalan Margonda, Depok',author:'Mfikriansori',year:'2022',source:'https://commons.wikimedia.org/wiki/File:Jalan_Margonda,_Depok.jpg',license:'CC BY-SA 4.0',licenseUrl:'https://creativecommons.org/licenses/by-sa/4.0/'}
  };
  const caption = s => {const c=archiveCredits[s.photo];return c?`Foto arsip ${c.year}: <a href="${c.source}" target="_blank" rel="noreferrer">${c.title} — ${c.author}</a> · <a href="${c.licenseUrl}" target="_blank" rel="noreferrer">${c.license}</a>. Ukuran dan potongan tampilan disesuaikan. Ilustrasi, bukan dokumentasi berita terkini.`:'Foto ilustrasi dari Unsplash. Bukan foto dokumentasi lokasi atau peristiwa dalam berita. <a href="https://unsplash.com" target="_blank" rel="noreferrer">Sumber foto ↗</a>';};
  const stories = [
    {id:'ruang-hijau-depok',category:'Kabar Kota',title:'Mencari jeda di antara ruang hijau Depok',deck:'Di balik ritme kota yang bergerak cepat, selalu ada ruang untuk berjalan pelan dan melihat Depok dari dekat.',photo:'park',place:'Depok',time:'17 Nov 2026',read:'4 menit',format:'photo'},
    {id:'cerita-kopi-margonda',category:'Kuliner',title:'Secangkir kopi, banyak cerita di Margonda',deck:'Mengenal tempat singgah dan orang-orang yang membuat sudut kota terasa lebih dekat.',photo:'coffee',place:'Margonda',time:'17 Nov 2026',read:'3 menit',format:'photo'},
    {id:'perjalanan-komuter-depok',category:'Transportasi',title:'Cerita pagi dari perjalanan komuter Depok',deck:'Dari langkah menuju stasiun hingga perjalanan pulang, beginilah sebuah cerita kota bisa dimulai.',photo:'train',place:'Pondok Cina',time:'17 Nov 2026',read:'3 menit',format:'photo'},
    {id:'kuliner-akhir-pekan',category:'Kuliner',title:'Menjelajah rasa, mengenal cerita di balik kuliner Depok',deck:'Bukan sekadar soal menu. Ada kisah usaha, keluarga, dan lingkungan yang tumbuh bersama.',photo:'market',place:'Beji',time:'16 Nov 2026',read:'5 menit',format:'photo'},
    {id:'komunitas-di-sekitar-kita',category:'Komunitas',title:'Dari tetangga jadi teman: cerita komunitas di sekitar kita',deck:'Kegiatan sederhana dapat menjadi awal pertemuan dan ruang untuk saling mengenal.',photo:'community',place:'Pancoran Mas',time:'16 Nov 2026',read:'4 menit',format:'photo'},
    {id:'ide-akhir-pekan-depok',category:'Agenda',title:'Menyusun akhir pekan yang lebih dekat dengan kota sendiri',deck:'Sebuah contoh rubrik agenda: ruang untuk menemukan kegiatan, tempat, dan pertemuan di Depok.',photo:'city',place:'Depok',time:'15 Nov 2026',read:'3 menit',format:'photo'},
    {id:'video-sudut-hijau',category:'Kabar Kota',title:'Satu menit melihat sisi hijau kota',deck:'Konsep video pendek: suasana ruang hijau, langkah warga, dan jeda dari keramaian.',photo:'park',place:'Depok',time:'17 Nov 2026',read:'Konsep 1 menit',format:'video'},
    {id:'video-kopi-lokal',category:'Kuliner',title:'Di balik secangkir kopi lokal',deck:'Konsep liputan video: mengenal pelaku usaha melalui cerita di balik meja bar.',photo:'coffee',place:'Margonda',time:'16 Nov 2026',read:'Konsep 2 menit',format:'video'}
  ];
  const categories = ['Semua','Kabar Kota','Kuliner','Komunitas','Agenda','Transportasi'];
  const main = document.getElementById('main');
  const dialog = document.getElementById('search-dialog');
  const meta = s => `<div class="meta"><span>${s.place}</span><span class="dot"></span><time datetime="2026-11-${s.time.slice(0,2)}">${s.time}</time><span class="dot"></span><span>${s.read}</span></div>`;
  const photo = (s,lazy=true) => `<img src="${photos[s.photo]}" alt="${archiveCredits[s.photo]?'Foto arsip '+archiveCredits[s.photo].title+' oleh '+archiveCredits[s.photo].author:'Foto ilustrasi '+(s.photo==='coffee'?'kedai kopi':s.photo==='community'?'kebersamaan warga':'suasana tempat makan')}; ilustrasi konten contoh" ${lazy?'loading="lazy"':'fetchpriority="high"'} decoding="async">`;
  const link = s => `#berita/${s.id}`;
  const chips = selected => `<div class="topic-nav" aria-label="Kategori berita">${categories.map(c=>`<a class="chip ${c===selected?'active':''}" href="#jelajah/${encodeURIComponent(c)}" ${c===selected?'aria-current="page"':''}>${c}</a>`).join('')}</div>`;
  const row = s => `<a class="news-row" href="${link(s)}"><div><span class="category">${s.category}</span><h3>${s.title}</h3><p>${s.deck}</p>${meta(s)}</div><div class="thumb">${photo(s)}</div></a>`;
  const card = s => `<article class="grid-story"><a href="${link(s)}"><div class="thumb">${photo(s)}</div><span class="category">${s.format==='video'?'VIDEO · ':''}${s.category}</span><h2>${s.title}</h2><p>${s.deck}</p>${meta(s)}</a></article>`;
  function home() {
    const s = stories[0];
    main.innerHTML=`<div class="container"><section class="welcome"><div><div class="eyebrow">Depok, dari dekat.</div><h1>Ada cerita apa <span class="line-mobile">di <span>Depok?</span></span></h1></div><p class="welcome-note"><span class="date-label">KABAR · ORANG · TEMPAT</span>Dari kabar kota sampai sudut favoritmu.<br>Cerita yang dekat dengan keseharian.</p></section>${chips('Semua')}<section class="hero-grid" aria-label="Pilihan redaksi"><a class="hero-story" href="${link(s)}">${photo(s,false)}<div class="hero-top"><span class="pill-label">PILIHAN REDAKSI</span><span class="photo-label">${icon('photo')} Foto cerita</span></div><div class="hero-content"><span class="category" style="color:#d5f591">KABAR KOTA</span><h2>${s.title}</h2><div class="hero-bottom">${meta(s)}<span class="round-arrow" aria-hidden="true">↗</span></div></div></a><div class="hero-side">${stories.slice(1,3).map(s=>`<a class="side-story" href="${link(s)}"><div class="thumb">${photo(s,false)}</div><div><span class="category">${s.category}</span><h2>${s.title}</h2>${meta(s)}</div></a>`).join('')}</div></section><div class="ticker"><strong>JELAJAH TOPIK</strong><div class="ticker-items"><a href="#jelajah/Kuliner"><span>↗</span> Kuliner lokal</a><a href="#jelajah/Komunitas"><span>↗</span> Cerita komunitas</a><a href="#jelajah/Transportasi"><span>↗</span> Mobilitas warga</a></div></div><div class="home-lower"><section><div class="section-head"><h2>Kabar terbaru<span style="color:#91b15a">.</span></h2><a href="#jelajah">Semua kabar ${icon('arrow')}</a></div><div class="latest-list">${stories.slice(1,5).map(row).join('')}</div></section><aside class="video-panel"><div class="eyebrow">Depok dalam frame</div><div class="section-head"><h2>Kota kita,<br>lewat cerita.</h2>${icon('video')}</div><div class="video-cards">${stories.filter(s=>s.format==='video').map(s=>`<a class="video-card" href="${link(s)}">${photo(s)}<span class="play-dot">${icon('play')}</span><div class="video-copy"><h3>${s.title}</h3><span>${s.read} · Contoh</span></div></a>`).join('')}</div><a class="video-foot" href="#video">Jelajahi video ${icon('arrow')}</a><p class="brand-note"><strong>Setiap sudut punya cerita.</strong><br>Depokin hadir untuk mengenal kota dan orang-orang di dalamnya lebih dekat.</p></aside></div></div>`;
  }
  function explore(selected='Semua', videos=false) {
    if(!categories.includes(selected)) selected='Semua';
    const filtered = stories.filter(s=>(videos?s.format==='video':s.format==='photo')&&(selected==='Semua'||s.category===selected));
    main.innerHTML=`<div class="container"><header class="page-head"><div class="eyebrow">${videos?'Depok dalam frame':'Temukan cerita di sekitarmu'}</div><h1>${videos?'Cerita yang bergerak.':'Jelajah Depok.'}</h1><p>${videos?'Potongan kehidupan kota, orang-orang, dan tempat yang menarik untuk dikenal.':'Pilih topik yang dekat denganmu. Dari kabar sehari-hari sampai cerita yang membuat kita mengenal kota sendiri.'}</p></header>${!videos?`<div class="filter-bar">${chips(selected)}<p class="result-count" aria-live="polite">${filtered.length} cerita contoh ${selected!=='Semua'?'· '+selected:''}</p></div>`:'<p class="sample-note">Pratinjau halaman video. Poster dan naskah adalah contoh; file video belum ditambahkan.</p>'}<div class="story-grid">${filtered.map(card).join('')}</div></div>`;
  }
  function article(id) {
    const s = stories.find(x=>x.id===id);
    if(!s){main.innerHTML='<div class="container"><div class="page-head"><h1>Cerita belum tersedia.</h1><p><a href="#beranda">Kembali ke beranda →</a></p></div></div>';return;}
    document.title=`${s.title} — depokin (Mockup)`;
    main.innerHTML=`<article class="article-shell"><nav class="breadcrumb" aria-label="Breadcrumb"><a href="#beranda">Beranda</a><span>/</span><a href="#jelajah/${encodeURIComponent(s.category)}">${s.category}</a><span>/</span><span>Cerita pilihan</span></nav><header class="article-head"><span class="pill-label">${s.category.toUpperCase()}</span><h1>${s.title}</h1><p class="article-deck">${s.deck}</p><div class="byline"><span class="avatar" aria-hidden="true">d.</span><div><strong>Redaksi Depokin · Contoh</strong>${meta(s)}</div><button class="share-button" data-share="${s.id}" aria-label="Salin tautan cerita">${icon('share')}<span>Bagikan</span></button></div></header><figure class="article-figure">${s.format==='video'?`<div class="video-placeholder">${photo(s,false)}<div class="placeholder-copy">${icon('video')}<strong>Ruang untuk video liputan</strong><p>Poster konsep · ${s.read.toLowerCase()}<br>Video belum dilampirkan pada mockup.</p></div></div>`:photo(s,false)}<figcaption>${caption(s)}</figcaption></figure><div class="article-body"><aside class="sample-note"><strong>Konten contoh untuk desain.</strong> Judul, tanggal, penulis, dan isi ini merupakan simulasi tampilan redaksi, bukan laporan peristiwa terkini yang telah diverifikasi.</aside><p>${s.deck} Melalui rubrik ${s.category.toLowerCase()}, Depokin ingin mengajak pembaca melihat kisah yang ada di sekitar mereka.</p><p>Di halaman ini, foto menjadi pembuka cerita. Teks memberi konteks: apa yang terjadi, siapa yang terlibat, di mana lokasinya, dan mengapa hal itu penting bagi warga Depok.</p><h2>Cerita yang dekat dengan keseharian</h2><p>Sebuah liputan lokal dapat dimulai dari hal sederhana—tempat yang sering dilewati, aktivitas warga, atau percakapan di lingkungan sekitar. Setiap cerita kemudian dilengkapi informasi yang jelas dan sumber yang bisa diperiksa.</p><blockquote>“Kota bukan hanya tempat tinggal, tetapi juga cerita yang kita bagi.”</blockquote><p>Petikan di atas merupakan contoh gaya editorial, bukan kutipan narasumber. Pada berita yang diterbitkan, bagian ini diisi hasil wawancara yang sudah dikonfirmasi.</p>${s.format==='video'?'<details class="transcript"><summary>Lihat contoh transkrip video</summary><p><strong>00:00 — Pembuka.</strong> Setiap sudut kota menyimpan cerita. Hari ini, kita melihat Depok dari dekat.</p><p><strong>00:15 — Cerita utama.</strong> Bagian ini akan memuat transkrip ucapan narasumber dan narasi yang sesuai dengan video sebenarnya.</p></details>':''}<h2>Mengenal Depok, satu cerita setiap hari</h2><p>Berita terkait di bawah membantu pembaca melanjutkan penelusuran. Kategori dan lokasi yang konsisten membuat cerita tentang Depok lebih mudah ditemukan kembali.</p></div><section class="related"><div class="section-head"><h2>Cerita lainnya</h2><a href="#jelajah">Jelajahi ${icon('arrow')}</a></div><div class="story-grid">${stories.filter(x=>x.id!==s.id&&x.format==='photo').slice(0,2).map(card).join('')}</div></section></article>`;
  }
  function about() {
    main.innerHTML=`<div class="container"><header class="page-head"><div class="eyebrow">Cerita kota kita</div><h1>Lebih dekat dengan Depok.</h1><p>Konsep Depokin: media lokal yang merangkum kabar, mengenalkan tempat, dan memberi ruang bagi cerita warga.</p></header><section class="about-layout"><div class="about-mark"><span class="brand">depokin.</span><h2>Dekat kotanya.<br>Dekat ceritanya.</h2><span>DEPOK, DARI DEKAT.</span></div><div class="about-copy"><h2>Berangkat dari sekitar kita.</h2><p>Depokin dirancang sebagai tempat menemukan informasi tentang Depok lewat foto, video, dan tulisan yang jelas. Topiknya mencakup kabar kota, kuliner, komunitas, agenda, dan transportasi.</p><h2>Arah prinsip redaksi</h2><ul><li>Informasi diperiksa sebelum diterbitkan.</li><li>Penulis, tanggal, sumber, dan lokasi dicantumkan dengan jelas.</li><li>Konten kerja sama diberi label yang mudah dikenali.</li><li>Koreksi berita disertai catatan pembaruan.</li></ul><p class="sample-note">Ini adalah konsep identitas dan halaman redaksi. Profil penerbit, susunan redaksi, kontak, serta kebijakan editorial perlu diisi dengan data sebenarnya sebelum peluncuran.</p><h2>Kredit foto arsip</h2><p>${Object.keys(archiveCredits).map(key=>caption({photo:key})).join("</p><p>")}</p><p>Foto ilustrasi kedai, tempat makan, dan komunitas: Unsplash.</p></div></section></div>`;
  }
  function route() {
    const parts=location.hash.slice(1).split('/');
    let page=parts[0]||'beranda';
    let sub='';try{sub=decodeURIComponent(parts.slice(1).join('/'));}catch{page='beranda';}
    document.title='depokin — Dekat kotanya, dekat ceritanya.';
    if(page==='berita') article(sub);else if(page==='jelajah') explore(sub||'Semua');else if(page==='video') explore('Semua',true);else if(page==='tentang') about();else{page='beranda';home();}
    const activePage=page==='berita'?(stories.find(s=>s.id===sub)?.format==='video'?'video':'beranda'):page;
    document.querySelectorAll('[data-nav]').forEach(a=>{if(a.dataset.nav===activePage)a.setAttribute('aria-current','page');else a.removeAttribute('aria-current');});
    if(dialog.open)dialog.close();
    window.scrollTo({top:0,behavior:'instant'});
  }
  document.querySelectorAll('[data-icon]').forEach(el=>el.innerHTML=icon(el.dataset.icon));
  document.querySelector('.skip-link').addEventListener('click',e=>{e.preventDefault();main.focus();main.scrollIntoView();});
  function search() {
    const q=document.getElementById('search-input').value.trim().toLocaleLowerCase('id');
    const found=stories.filter(s=>`${s.title} ${s.deck} ${s.category} ${s.place}`.toLocaleLowerCase('id').includes(q));
    document.getElementById('search-results').innerHTML=q?(found.length?`<p class="search-hint">${found.length} cerita ditemukan</p>${found.map(row).join('')}`:'<p class="empty-state">Belum ada cerita yang cocok. Coba kata “kuliner”, “komunitas”, atau “Depok”.</p>'):`<p class="search-hint">Pilihan cerita</p>${stories.slice(0,3).map(row).join('')}`;
  }
  document.querySelector('.search-trigger').addEventListener('click',()=>{dialog.showModal();search();document.getElementById('search-input').focus();});
  document.getElementById('close-search').addEventListener('click',()=>dialog.close());
  dialog.addEventListener('click',e=>{if(e.target===dialog){const r=dialog.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)dialog.close();}});
  document.getElementById('search-form').addEventListener('submit',e=>{e.preventDefault();search();});
  document.getElementById('search-input').addEventListener('input',search);
  document.getElementById('search-results').addEventListener('click',e=>{if(e.target.closest('a'))dialog.close();});
  document.getElementById('wire-toggle').addEventListener('click',e=>{const on=document.body.classList.toggle('wireframe');e.currentTarget.setAttribute('aria-pressed',String(on));e.currentTarget.innerHTML=on?'Lihat desain <span aria-hidden="true">↗</span>':'Lihat wireframe <span aria-hidden="true">↗</span>';});
  let toastTimer;
  main.addEventListener('click',async e=>{const button=e.target.closest('[data-share]');if(!button)return;const toast=document.getElementById('toast');try{await navigator.clipboard.writeText(location.href);toast.textContent='Tautan cerita disalin.';}catch{toast.textContent='Salin tautan dari bilah alamat browser.';}toast.hidden=false;clearTimeout(toastTimer);toastTimer=setTimeout(()=>toast.hidden=true,3500);});
  window.addEventListener('hashchange',route);
  route();
})();
