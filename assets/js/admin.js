(() => {
  'use strict';
  const licenseModal = document.getElementById('licenseModal');
  const profileModal = document.getElementById('profileModal');
  const show = (modal) => { modal.hidden = false; document.body.style.overflow = 'hidden'; };
  const hide = (modal) => { modal.hidden = true; document.body.style.overflow = ''; };

  document.getElementById('openLicense')?.addEventListener('click', () => show(licenseModal));
  licenseModal?.querySelectorAll('[data-close]').forEach((button) => button.addEventListener('click', () => hide(licenseModal)));
  document.querySelector('[data-profile-close]')?.addEventListener('click', () => hide(profileModal));
  [licenseModal, profileModal].forEach((modal) => modal?.addEventListener('click', (event) => { if (event.target === modal) hide(modal); }));

  document.querySelectorAll('[data-copy]').forEach((button) => button.addEventListener('click', async () => {
    await copyText(button.dataset.copy || '');
    button.textContent = 'Kopiert';
  }));

  document.getElementById('licenseForm')?.addEventListener('submit', async (event) => {
    event.preventDefault();
    const form = event.currentTarget;
    const button = form.querySelector('button[type="submit"]');
    const output = form.querySelector('.license-output');
    if (!button || !output) return;
    button.disabled = true;
    button.textContent = 'Wird generiert…';
    const payload = Object.fromEntries(new FormData(form).entries());
    try {
      const response = await fetch('/api/generate_license.php', {method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-Token':window.DARTSYSTEM_ADMIN.csrf},body:JSON.stringify(payload)});
      const data = await response.json();
      if (!response.ok || !data.success) throw new Error(data.error || 'Lizenz konnte nicht erstellt werden.');
      output.hidden = false;
      output.innerHTML = `<small>Lizenz für ${escapeHtml(data.device_id)}</small><b>${escapeHtml(data.license_key)}</b><button type="button">Schlüssel kopieren</button>`;
      output.querySelector('button').addEventListener('click', async () => {
        await copyText(data.license_key);
        output.querySelector('button').textContent = 'Kopiert';
      });
      form.reset();
    } catch (error) {
      output.hidden = false;
      output.textContent = error.message;
    } finally {
      button.disabled = false;
      button.textContent = 'Lizenzschlüssel erstellen';
    }
  });

  document.querySelectorAll('.profile-button').forEach((button) => button.addEventListener('click', async () => {
    show(profileModal);
    const body = document.getElementById('profileBody');
    const name = button.dataset.user;
    document.getElementById('profileName').textContent = name;
    body.textContent = 'Wird geladen…';
    try {
      const response = await fetch(`/api/get_profile.php?username=${encodeURIComponent(name)}`, {credentials:'same-origin'});
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.error || 'Profil konnte nicht geladen werden.');
      const user = result.data;
      const progress = Array.isArray(user.progress) ? user.progress : [];
      body.innerHTML = `<div class="profile-hero"><span>${String(user.username).slice(0,2).toUpperCase()}</span><div><b>Level ${user.level || 1}</b><small>${user.experience || 0} Experience</small></div></div><h3>Fortschritt</h3><div class="data-list">${progress.length ? progress.map(item => `<article><div><b>${escapeHtml(item.level_id)}</b><small>${Number(item.darts_thrown || 0)} Darts · ${Number(item.attempts || 0)} Versuche</small></div><strong>${Number(item.accuracy || 0).toFixed(1)}%</strong></article>`).join('') : '<p>Noch kein Fortschritt.</p>'}</div>`;
    } catch (error) { body.textContent = error.message; }
  }));

  document.addEventListener('keydown', (event) => { if (event.key === 'Escape') { if (licenseModal) hide(licenseModal); if (profileModal) hide(profileModal); } });
  async function copyText(value){
    if (navigator.clipboard && window.isSecureContext) return navigator.clipboard.writeText(value);
    const input=document.createElement('textarea');input.value=value;input.style.position='fixed';input.style.opacity='0';document.body.appendChild(input);input.select();document.execCommand('copy');input.remove();
  }
  function escapeHtml(value){const node=document.createElement('div');node.textContent=String(value??'');return node.innerHTML;}
})();
