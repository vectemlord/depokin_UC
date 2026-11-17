(() => {
  'use strict';

  const DG_SPRITE = '<svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden"><defs>'
    + '<linearGradient id="dg-ig" gradientUnits="userSpaceOnUse" x1="4" y1="4" x2="20" y2="20"><stop offset="0" stop-color="#F9CE34"/><stop offset=".52" stop-color="#EE2A7B"/><stop offset="1" stop-color="#6228D7"/></linearGradient>'
    + '<linearGradient id="dg-blue" gradientUnits="userSpaceOnUse" x1="4" y1="4" x2="20" y2="20"><stop offset="0" stop-color="#4285F4"/><stop offset="1" stop-color="#6228D7"/></linearGradient>'
    + '<linearGradient id="dg-red" gradientUnits="userSpaceOnUse" x1="4" y1="4" x2="20" y2="20"><stop offset="0" stop-color="#EA4335"/><stop offset="1" stop-color="#EE2A7B"/></linearGradient>'
    + '<linearGradient id="dg-sun" gradientUnits="userSpaceOnUse" x1="4" y1="4" x2="20" y2="20"><stop offset="0" stop-color="#FBBC05"/><stop offset="1" stop-color="#EA4335"/></linearGradient>'
    + '<linearGradient id="dg-green" gradientUnits="userSpaceOnUse" x1="4" y1="4" x2="20" y2="20"><stop offset="0" stop-color="#34A853"/><stop offset="1" stop-color="#4285F4"/></linearGradient>'
    + '<linearGradient id="dg-pink" gradientUnits="userSpaceOnUse" x1="4" y1="4" x2="20" y2="20"><stop offset="0" stop-color="#EE2A7B"/><stop offset="1" stop-color="#6228D7"/></linearGradient>'
    + '</defs></svg>';
  document.body.insertAdjacentHTML('afterbegin', DG_SPRITE);
  const icons = {
    search:'<circle cx="10.5" cy="10.5" r="6.5" fill="#4285F4" fill-opacity=".12" stroke="url(#dg-blue) #4285F4"/><path d="M15.2 15.2 20 20" stroke="url(#dg-blue) #4285F4"/><circle cx="10.5" cy="10.5" r="1.6" fill="#FBBC05"/>',
    home:'<path d="M6 9.6 12 4.4l6 5.2V20H6Z" fill="#EE2A7B" fill-opacity=".12" stroke="url(#dg-pink) #EE2A7B"/><path d="M4 10.4 12 3.4l8 7" stroke="url(#dg-pink) #EE2A7B"/><path d="M10 20v-5.2h4V20" stroke="url(#dg-ig) #6228D7"/><circle cx="13.1" cy="17.4" r=".9" fill="#FBBC05"/>',
    compass:'<circle cx="12" cy="12" r="8.6" fill="#34A853" fill-opacity=".1" stroke="url(#dg-green) #34A853"/><path d="m15.6 8.4-2.3 4.9-4.9 2.3 2.3-4.9 4.9-2.3Z" fill="url(#dg-pink) #EE2A7B"/><circle cx="12" cy="12" r="1.1" fill="#FBBC05"/>',
    video:'<rect x="2.8" y="5" width="18.4" height="14" rx="4" fill="#EA4335" fill-opacity=".12" stroke="url(#dg-red) #EA4335"/><path d="m10.2 9.2 5 2.8-5 2.8Z" fill="url(#dg-sun) #FBBC05"/><circle cx="18" cy="8.2" r="1.1" fill="#FBBC05"/>',
    info:'<circle cx="12" cy="12" r="8.6" fill="#34A853" fill-opacity=".12" stroke="url(#dg-green) #34A853"/><path d="M12 11v6" stroke="url(#dg-blue) #4285F4" stroke-width="2.2"/><circle cx="12" cy="7.7" r="1.4" fill="#FBBC05"/>',
    arrow:'<path d="M4.5 12h14" stroke="url(#dg-pink) #EE2A7B"/><path d="m13.5 6.5 5.5 5.5-5.5 5.5" stroke="url(#dg-pink) #EE2A7B"/><circle cx="4.6" cy="12" r="1.5" fill="#FBBC05"/>',
    close:'<path d="m6.5 6.5 11 11" stroke="url(#dg-red) #EA4335"/><path d="m17.5 6.5-11 11" stroke="url(#dg-red) #EA4335"/>',
    play:'<circle cx="12" cy="12" r="8.6" fill="#EA4335" fill-opacity=".12" stroke="url(#dg-red) #EA4335"/><path d="m10 8.6 6 3.4-6 3.4Z" fill="url(#dg-sun) #FBBC05"/>',
    photo:'<rect x="3" y="4.5" width="18" height="15" rx="4" fill="#34A853" fill-opacity=".1" stroke="url(#dg-green) #34A853"/><circle cx="8.3" cy="9.3" r="1.8" fill="url(#dg-sun) #FBBC05"/><path d="m5.5 17 4.4-4.2 3.2 2.6 3-3.4 2.4 2.6" stroke="url(#dg-blue) #4285F4"/>',
    share:'<path d="m8.7 10.8 6.6-3.9M8.7 13.2l6.6 3.9" stroke="url(#dg-blue) #4285F4"/><circle cx="17.4" cy="5.6" r="2.7" fill="#EE2A7B" fill-opacity=".15" stroke="url(#dg-pink) #EE2A7B"/><circle cx="17.4" cy="18.4" r="2.7" fill="#EE2A7B" fill-opacity=".15" stroke="url(#dg-pink) #EE2A7B"/><circle cx="6.3" cy="12" r="2.7" fill="url(#dg-sun) #FBBC05"/>',
    dashboard:'<rect x="3.5" y="3.5" width="7.4" height="7.4" rx="2.4" fill="#4285F4" fill-opacity=".15" stroke="url(#dg-blue) #4285F4"/><rect x="13.1" y="3.5" width="7.4" height="7.4" rx="2.4" fill="#EA4335" fill-opacity=".15" stroke="url(#dg-red) #EA4335"/><rect x="3.5" y="13.1" width="7.4" height="7.4" rx="2.4" fill="#FBBC05" fill-opacity=".18" stroke="url(#dg-sun) #FBBC05"/><rect x="13.1" y="13.1" width="7.4" height="7.4" rx="2.4" fill="#34A853" fill-opacity=".15" stroke="url(#dg-green) #34A853"/>',
    doc:'<path d="M6 2.9h7.6l4.4 4.4v13.8H6Z" fill="#4285F4" fill-opacity=".1" stroke="url(#dg-blue) #4285F4"/><path d="M13.6 2.9v4.4H18" fill="#FBBC05" fill-opacity=".35" stroke="url(#dg-sun) #FBBC05"/><path d="M9 12.2h6M9 15.6h4.4" stroke="url(#dg-blue) #4285F4"/>',
    eye:'<path d="M2.8 12C4.6 8.4 8 5.8 12 5.8s7.4 2.6 9.2 6.2c-1.8 3.6-5.2 6.2-9.2 6.2S4.6 15.6 2.8 12Z" fill="#4285F4" fill-opacity=".1" stroke="url(#dg-blue) #4285F4"/><circle cx="12" cy="12" r="3" stroke="url(#dg-green) #34A853"/><circle cx="12" cy="12" r="1.2" fill="#FBBC05"/>',
    tag:'<path d="M3.6 3.6h7.2l9.6 9.6a2 2 0 0 1 0 2.8l-4.4 4.4a2 2 0 0 1-2.8 0L3.6 10.8Z" fill="#34A853" fill-opacity=".12" stroke="url(#dg-green) #34A853"/><circle cx="7.7" cy="7.7" r="1.7" stroke="url(#dg-green) #34A853"/><circle cx="7.7" cy="7.7" r=".8" fill="#FBBC05"/>',
    users:'<circle cx="9" cy="8.2" r="3.2" fill="#4285F4" fill-opacity=".12" stroke="url(#dg-blue) #4285F4"/><path d="M3.6 19.4c0-3 2.4-5.4 5.4-5.4s5.4 2.4 5.4 5.4" stroke="url(#dg-blue) #4285F4"/><circle cx="16.7" cy="9" r="2.6" fill="#EE2A7B" fill-opacity=".12" stroke="url(#dg-pink) #EE2A7B"/><path d="M16.9 13.6c2.1.4 3.7 2.2 3.7 4.4" stroke="url(#dg-pink) #EE2A7B"/>',
    gear:'<circle cx="12" cy="12" r="6.2" fill="#4285F4" fill-opacity=".1" stroke="url(#dg-blue) #4285F4"/><path d="M12 2.6v3M12 18.4v3M2.6 12h3M18.4 12h3M5.4 5.4l2.1 2.1M16.5 16.5l2.1 2.1M18.6 5.4l-2.1 2.1M7.5 16.5l-2.1 2.1" stroke="url(#dg-blue) #4285F4"/><circle cx="12" cy="12" r="1.6" fill="url(#dg-sun) #FBBC05"/>',
    user:'<circle cx="12" cy="7.6" r="3.6" fill="#EE2A7B" fill-opacity=".12" stroke="url(#dg-pink) #EE2A7B"/><path d="M4.8 20c0-4 3.2-7.2 7.2-7.2s7.2 3.2 7.2 7.2" stroke="url(#dg-pink) #EE2A7B"/>',
    external:'<path d="M11 5H5v14h14v-6" stroke="url(#dg-blue) #4285F4"/><path d="M14 4h6v6M20 4l-9.5 9.5" stroke="url(#dg-red) #EA4335"/>',
    logout:'<path d="M10 4H4.8v16H10" stroke="url(#dg-blue) #4285F4"/><path d="m15 8 4 4-4 4M19 12H9.5" stroke="url(#dg-red) #EA4335"/>',
    menu:'<path d="M4 6.5h16" stroke="url(#dg-blue) #4285F4" stroke-width="1.9"/><path d="M4 12h16" stroke="url(#dg-pink) #EE2A7B" stroke-width="1.9"/><path d="M4 17.5h16" stroke="url(#dg-sun) #FBBC05" stroke-width="1.9"/>',
    clock:'<circle cx="12" cy="12" r="8.6" fill="#4285F4" fill-opacity=".1" stroke="url(#dg-blue) #4285F4"/><path d="M12 7.4V12l3.4 2" stroke="url(#dg-red) #EA4335"/>',
    check:'<circle cx="12" cy="12" r="8.6" fill="#34A853" fill-opacity=".12" stroke="url(#dg-green) #34A853"/><path d="m8 12.4 2.8 2.8 5.4-6" stroke="url(#dg-green) #34A853" stroke-width="2"/>',
    edit:'<path d="M4.5 16.8 14.6 6.7l2.7 2.7L7.2 19.5H4.5Z" fill="#FBBC05" fill-opacity=".18" stroke="url(#dg-sun) #FBBC05"/><path d="m15.8 5.5 1.5-1.5a1.6 1.6 0 0 1 2.3 0l.4.4a1.6 1.6 0 0 1 0 2.3l-1.5 1.5" stroke="url(#dg-pink) #EE2A7B"/>',
    star:'<path d="m12 3.4 2.6 5.4 5.9.8-4.3 4.1 1 5.9-5.2-2.8-5.2 2.8 1-5.9-4.3-4.1 5.9-.8Z" fill="#FBBC05" fill-opacity=".18" stroke="url(#dg-sun) #FBBC05"/><circle cx="12" cy="10.8" r="1.3" fill="#EE2A7B"/>'
  };
  document.querySelectorAll('[data-icon]').forEach(el => {
    el.innerHTML = `<svg viewBox="0 0 24 24" aria-hidden="true"><g fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">${icons[el.dataset.icon] || icons.arrow}</g></svg>`;
  });

  const sidebar = document.querySelector('.sidebar');
  const overlay = document.querySelector('.overlay');
  const burger = document.querySelector('.hamburger');
  if (burger && sidebar && overlay) {
    burger.addEventListener('click', () => {
      sidebar.classList.toggle('open');
      overlay.classList.toggle('show', sidebar.classList.contains('open'));
    });
    overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); });
  }

  document.querySelectorAll('[data-tabs]').forEach(nav => {
    const buttons = nav.querySelectorAll('[data-tab]');
    buttons.forEach(btn => btn.addEventListener('click', () => {
      buttons.forEach(b => b.setAttribute('aria-selected', String(b === btn)));
      document.querySelectorAll(nav.dataset.tabs + ' .tab-panel').forEach(p => { p.hidden = p.id !== 'tab-' + btn.dataset.tab; });
    }));
  });

  document.querySelectorAll('[data-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      const dlg = document.getElementById(btn.dataset.modal);
      if (!dlg) return;
      const form = dlg.querySelector('form');
      if (form) {
        form.reset();
        dlg.querySelectorAll('[data-fill]').forEach(el => {
          const key = el.dataset.fill;
          if (!(key in btn.dataset)) return;
          const val = btn.dataset[key];
          if (el.type === 'checkbox') el.checked = val === '1';
          else el.value = val;
        });
        const idField = form.querySelector('[name=id]');
        if (idField) idField.value = btn.dataset.id ?? '';
        const title = dlg.querySelector('[data-dialog-title]');
        if (title) title.textContent = btn.dataset.id ? (title.dataset.edit || title.textContent) : (title.dataset.create || title.textContent);
      }
      dlg.showModal();
    });
  });
  document.querySelectorAll('dialog [data-close]').forEach(btn => btn.addEventListener('click', () => btn.closest('dialog').close()));
  document.querySelectorAll('dialog').forEach(dlg => dlg.addEventListener('click', e => {
    if (e.target === dlg) {
      const r = dlg.getBoundingClientRect();
      if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dlg.close();
    }
  }));

  document.querySelectorAll('form[data-confirm]').forEach(f => f.addEventListener('submit', e => {
    if (!confirm(f.dataset.confirm)) e.preventDefault();
  }));

  const editor = document.getElementById('editor-area');
  const hidden = document.getElementById('content-input');
  if (editor && hidden) {
    document.querySelectorAll('.editor-toolbar [data-cmd]').forEach(btn => btn.addEventListener('click', () => {
      editor.focus();
      const cmd = btn.dataset.cmd;
      if (cmd === 'createLink') {
        const url = prompt('URL tautan (https://…):', 'https://');
        if (url && /^https?:\/\//i.test(url)) document.execCommand('createLink', false, url);
        return;
      }
      if (cmd === 'formatBlock') document.execCommand('formatBlock', false, btn.dataset.value);
      else document.execCommand(cmd, false, null);
    }));
    const form = editor.closest('form');
    if (form) form.addEventListener('submit', () => { hidden.value = editor.innerHTML; });
  }

  const judul = document.getElementById('f-judul');
  const slug = document.getElementById('f-slug');
  if (judul && slug) {
    let manual = slug.value !== '';
    slug.addEventListener('input', () => { manual = slug.value !== ''; });
    judul.addEventListener('input', () => {
      if (manual) return;
      slug.value = judul.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    });
  }

  const format = document.getElementById('f-format');
  const videoBox = document.getElementById('video-fields');
  if (format && videoBox) {
    const sync = () => { videoBox.hidden = format.value !== 'video'; };
    format.addEventListener('change', sync);
    sync();
  }

  document.querySelectorAll('[data-revisi]').forEach(btn => btn.addEventListener('click', () => {
    const dlg = document.getElementById('revisi-dialog');
    if (!dlg) return;
    dlg.querySelector('[name=id]').value = btn.dataset.revisi;
    dlg.querySelector('[name=kembali]').value = btn.dataset.kembali || 'tinjauan.php';
    dlg.showModal();
  }));
})();