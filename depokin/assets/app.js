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
    share:'<path d="m8.7 10.8 6.6-3.9M8.7 13.2l6.6 3.9" stroke="url(#dg-blue) #4285F4"/><circle cx="17.4" cy="5.6" r="2.7" fill="#EE2A7B" fill-opacity=".15" stroke="url(#dg-pink) #EE2A7B"/><circle cx="17.4" cy="18.4" r="2.7" fill="#EE2A7B" fill-opacity=".15" stroke="url(#dg-pink) #EE2A7B"/><circle cx="6.3" cy="12" r="2.7" fill="url(#dg-sun) #FBBC05"/>'
  };
  const icon = name => `<svg viewBox="0 0 24 24" aria-hidden="true"><g fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">${icons[name] || icons.arrow}</g></svg>`;
  document.querySelectorAll('[data-icon]').forEach(el => el.innerHTML = icon(el.dataset.icon));

  const main = document.getElementById('main');
  const skip = document.querySelector('.skip-link');
  if (skip && main) skip.addEventListener('click', e => { e.preventDefault(); main.focus(); main.scrollIntoView(); });

  const dialog = document.getElementById('search-dialog');
  const trigger = document.querySelector('.search-trigger');
  if (dialog && trigger) {
    trigger.addEventListener('click', () => { dialog.showModal(); document.getElementById('search-input').focus(); });
    document.getElementById('close-search').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', e => {
      if (e.target === dialog) {
        const r = dialog.getBoundingClientRect();
        if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dialog.close();
      }
    });
  }

  let toastTimer;
  const toast = msg => {
    const el = document.getElementById('toast');
    if (!el) return;
    el.textContent = msg;
    el.hidden = false;
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => el.hidden = true, 3500);
  };
  document.addEventListener('click', async e => {
    const btn = e.target.closest('[data-share]');
    if (!btn) return;
    const shareUrl = btn.dataset.share || location.href;
    const title = btn.dataset.title || document.title;
    if (navigator.share) {
      try { await navigator.share({ title, url: shareUrl }); return; } catch (err) { if (err.name === 'AbortError') return; }
    }
    try { await navigator.clipboard.writeText(shareUrl); toast('Tautan cerita disalin.'); }
    catch { toast('Salin tautan dari bilah alamat browser.'); }
  });
})();