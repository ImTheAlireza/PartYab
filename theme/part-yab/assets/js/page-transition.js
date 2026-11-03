/* PartYab page-to-page loader. Progressive enhancement; never blocks no-JS. */
(function () {
  'use strict';
  var overlay, busy = false;
  var reduced = function () { return window.PartyabMotion && window.PartyabMotion.reduced(); };
  function ensure() {
    if (overlay) return overlay;
    overlay = document.querySelector('.pb-page-loader');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'pb-page-loader';
      overlay.setAttribute('role', 'status');
      overlay.setAttribute('aria-live', 'polite');
      overlay.setAttribute('aria-hidden', 'true');
      overlay.hidden = true;
      overlay.innerHTML = '<div class="pb-page-loader__scene"><div class="pb-page-loader__logo"><svg aria-hidden="true"><use href="#i-logo"></use></svg></div><div class="pb-page-loader__road"><svg class="pb-page-loader__car" aria-hidden="true"><use href="#i-car"></use></svg></div><div class="pb-page-loader__bar"><i></i></div><p class="pb-page-loader__text">در حال آماده‌سازی مسیر…</p></div>';
      document.body.appendChild(overlay);
    }
    return overlay;
  }
  function show() {
    var el = ensure();
    el.hidden = false;
    el.setAttribute('aria-hidden', 'false');
    el.classList.toggle('is-static', !!reduced());
    requestAnimationFrame(function () { el.classList.add('is-active'); });
    if (!reduced()) document.documentElement.classList.add('pb-page-leaving');
  }
  function hide() {
    var el = ensure();
    el.classList.remove('is-active', 'is-static');
    el.setAttribute('aria-hidden', 'true');
    document.documentElement.classList.remove('pb-page-leaving');
    setTimeout(function () { if (!el.classList.contains('is-active')) el.hidden = true; }, 380);
    busy = false;
  }
  function navigate(url) {
    if (busy) return;
    busy = true;
    show();
    setTimeout(function () { window.location.assign(url); }, reduced() ? 60 : 420);
  }
  function internalLink(a, event) {
    if (!a || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
    if (a.target && a.target !== '_self') return false;
    if (a.hasAttribute('download') || a.hasAttribute('data-no-transition')) return false;
    if (a.closest('.ajax_add_to_cart, .woocommerce-product-gallery, .elementor-editor-active, [data-elementor-open-lightbox]')) return false;
    var href = a.getAttribute('href') || '';
    if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(href)) return false;
    var url;
    try { url = new URL(a.href, location.href); } catch (e) { return false; }
    if (url.origin !== location.origin) return false;
    if (/\/(wp-admin|wp-login\.php)/.test(url.pathname) || /action=logout|add-to-cart=|remove_item=/.test(url.search)) return false;
    if (url.pathname === location.pathname && url.search === location.search && url.hash) return false;
    return url;
  }
  document.addEventListener('click', function (event) {
    var url = internalLink(event.target.closest('a[href]'), event);
    if (!url) return;
    event.preventDefault();
    navigate(url.href);
  });
  document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (event.defaultPrevented || form.hasAttribute('data-no-transition') || form.closest('.woocommerce-checkout')) return;
    if ((form.method || 'get').toLowerCase() !== 'get') return;
    if (form.hasAttribute('data-pb-suggest-form') || form.hasAttribute('data-demo-search') || form.classList.contains('pb-car') || form.hasAttribute('data-car-form')) {
      show();
    }
  });
  window.addEventListener('pageshow', function () { hide(); var main = document.querySelector('.pb-site-content'); if (main && !reduced()) { main.classList.remove('pb-page-enter'); void main.offsetWidth; main.classList.add('pb-page-enter'); } });
  document.addEventListener('DOMContentLoaded', hide);
  window.PartyabTransition = { show: show, hide: hide, navigate: navigate };
})();
