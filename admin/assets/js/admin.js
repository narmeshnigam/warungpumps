// Tiny helper for confirmable actions
document.addEventListener('click', (e) => {
  const el = e.target.closest('[data-confirm]');
  if (el) {
    const msg = el.getAttribute('data-confirm') || 'Are you sure?';
    if (!confirm(msg)) e.preventDefault();
  }
});
