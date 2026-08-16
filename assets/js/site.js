(() => {
  'use strict';

  const menu = document.querySelector('.menu-button');
  const nav = document.querySelector('.desktop-nav');
  menu?.addEventListener('click', () => {
    const open = nav?.classList.toggle('mobile-open') ?? false;
    menu.setAttribute('aria-expanded', String(open));
    menu.textContent = open ? '×' : '☰';
  });

  nav?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => {
    nav.classList.remove('mobile-open');
    menu?.setAttribute('aria-expanded', 'false');
    if (menu) menu.textContent = '☰';
  }));

  document.querySelectorAll('[data-accordion]').forEach((button) => {
    button.addEventListener('click', () => {
      const panel = document.getElementById(button.getAttribute('aria-controls') || '');
      const expanded = button.getAttribute('aria-expanded') === 'true';
      button.setAttribute('aria-expanded', String(!expanded));
      if (panel) panel.hidden = expanded;
    });
  });

  document.querySelectorAll('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
      const text = button.getAttribute('data-copy') || '';
      await navigator.clipboard.writeText(text);
      const original = button.textContent;
      button.textContent = 'Kopiert ✓';
      setTimeout(() => { button.textContent = original; }, 1800);
    });
  });

  window.setTimeout(() => document.querySelectorAll('.flash').forEach((el) => el.remove()), 5000);
})();
