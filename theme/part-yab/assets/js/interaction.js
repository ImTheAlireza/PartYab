/* Global micro-interactions: pointer glow, custom selects. Button motion is CSS-only (B1+B4 / B2). */
(function () {
  'use strict';
  var reduced = function () { return window.PartyabMotion && window.PartyabMotion.reduced(); };
  var fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

  if (fine) {
    document.addEventListener('pointermove', function (event) {
      if (reduced()) return;
      var card = event.target.closest('.pb-card, .pb-panel, .pb-side-card, .pb-step, .pb-service, .pb-article-card, .pb-contact-cards__card, .pb-about-value, .pb-dash-card');
      if (!card) return;
      var rect = card.getBoundingClientRect();
      card.style.setProperty('--mx', ((event.clientX - rect.left) / rect.width * 100) + '%');
      card.style.setProperty('--my', ((event.clientY - rect.top) / rect.height * 100) + '%');
    }, { passive: true });
  }

  /* Custom select: keeps the native select submitted, replaces only the popup. */
  var uid = 0;
  function enhanceSelect(select) {
    if (select.dataset.pbSelectDone || select.multiple || select.classList.contains('select2-hidden-accessible')) return;
    if (!select.matches('.pb-field select, .pb-sort select, [data-pb-select], .woocommerce-ordering select')) return;
    select.dataset.pbSelectDone = '1';
    var wrap = document.createElement('div'); wrap.className = 'pb-select';
    select.parentNode.insertBefore(wrap, select); wrap.appendChild(select);
    select.classList.add('pb-select__native'); select.tabIndex = -1; select.setAttribute('aria-hidden', 'true');
    var button = document.createElement('button'); button.type = 'button'; button.className = 'pb-select__button';
    button.setAttribute('role', 'combobox'); button.setAttribute('aria-haspopup', 'listbox'); button.setAttribute('aria-expanded', 'false');
    var label = select.closest('label')?.querySelector('span')?.textContent || select.getAttribute('aria-label') || select.name || 'انتخاب';
    button.setAttribute('aria-label', label);
    var list = document.createElement('ul'); list.className = 'pb-select__list'; list.id = 'pb-select-' + (++uid); list.setAttribute('role', 'listbox'); list.hidden = true;
    button.setAttribute('aria-controls', list.id);
    wrap.appendChild(button); wrap.appendChild(list);
    function render() {
      var current = select.options[select.selectedIndex];
      button.innerHTML = '<span></span><svg class="pb-icon" aria-hidden="true"><use href="#i-chev-d"></use></svg>';
      button.firstChild.textContent = current ? current.textContent : 'انتخاب کن…';
      list.innerHTML = '';
      Array.from(select.options).forEach(function (opt, i) {
        var li = document.createElement('li'); li.className = 'pb-select__option'; li.setAttribute('role', 'option'); li.dataset.value = opt.value; li.textContent = opt.textContent; li.tabIndex = -1;
        li.setAttribute('aria-selected', String(i === select.selectedIndex));
        li.addEventListener('click', function (event) { event.preventDefault(); choose(i); close(true); });
        list.appendChild(li);
      });
    }
    function open() {
      render(); list.hidden = false; wrap.classList.add('is-open'); button.setAttribute('aria-expanded', 'true');
      var active = list.querySelector('[aria-selected=true]') || list.firstElementChild; if (active) { active.classList.add('is-active'); active.focus(); }
    }
    function close(focus) { list.hidden = true; wrap.classList.remove('is-open'); button.setAttribute('aria-expanded', 'false'); if (focus) button.focus(); }
    function choose(index) { select.selectedIndex = index; select.dispatchEvent(new Event('change', { bubbles: true })); select.dispatchEvent(new Event('input', { bubbles: true })); render(); }
    button.addEventListener('click', function (event) { event.preventDefault(); list.hidden ? open() : close(true); });
    button.addEventListener('keydown', function (event) {
      if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(event.key)) { event.preventDefault(); open(); }
      if (event.key === 'Escape') close(true);
    });
    list.addEventListener('keydown', function (event) {
      var items = Array.from(list.children); var i = items.indexOf(document.activeElement);
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') { event.preventDefault(); var n = (i + (event.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length; items.forEach(function (x) { x.classList.remove('is-active'); }); items[n].classList.add('is-active'); items[n].focus(); }
      if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); if (i >= 0) { choose(i); close(true); } }
      if (event.key === 'Escape' || event.key === 'Tab') close(true);
      if (event.key === 'Home') { event.preventDefault(); items[0]?.focus(); }
      if (event.key === 'End') { event.preventDefault(); items[items.length - 1]?.focus(); }
    });
    document.addEventListener('click', function (event) { if (!wrap.contains(event.target)) close(false); });
    select.addEventListener('change', render); select.addEventListener('input', render);
    select.addEventListener('invalid', function (event) { event.preventDefault(); open(); if (window.PartyabUI) window.PartyabUI.toast('لطفاً یک گزینه را انتخاب کن.'); });
    new MutationObserver(render).observe(select, { childList: true, subtree: true, attributes: true });
    render();
  }
  function enhanceSelects(scope) { (scope || document).querySelectorAll('select').forEach(enhanceSelect); }
  document.addEventListener('DOMContentLoaded', function () {
    enhanceSelects();
    var revealTargets = document.querySelectorAll('.pb-contact-cards__card, .pb-about-value, .pb-faq__item, .pb-guide__item, .pb-side-card, .pb-dash-card, .pb-policy-section, .pb-help-box');
    revealTargets.forEach(function (el, i) { if (!el.hasAttribute('data-reveal')) { el.setAttribute('data-reveal', ''); el.style.setProperty('--d', (i % 4) * .06 + 's'); } });
    if (window.PartyabMotion) window.PartyabMotion.reveal();
  });
  if (window.jQuery) window.jQuery(document.body).on('updated_wc_div wc_fragments_refreshed', function () { enhanceSelects(); });
  window.PartyabInteraction = { enhanceSelects: enhanceSelects };
})();
