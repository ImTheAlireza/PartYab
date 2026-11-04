/* PartYab page transition: opaque sliding curtain, covered before navigation. */
(function () {
  'use strict';
  var KEY = 'partyab-page-transition';
  var DURATION = 420;
  var overlay = null, state = 'idle', busy = false, serial = 0;
  var reduced = function () { return window.PartyabMotion && window.PartyabMotion.reduced(); };
  var MARKUP = '<div class="pb-page-loader__panel"><span class="pb-page-loader__pulse" aria-hidden="true"><i class="pb-page-loader__wave"></i><i class="pb-page-loader__wave"></i><i class="pb-page-loader__wave"></i></span><span class="pb-page-loader__logo" aria-hidden="true"><svg><use href="#i-logo"></use></svg></span><span class="screen-reader-text">در حال آماده‌سازی صفحه</span></div>';

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
      document.body.appendChild(overlay);
    }
    overlay.innerHTML = MARKUP;
    return overlay;
  }
  function covered() {
    var r = overlay.getBoundingClientRect();
    return r.left <= 0 && r.right >= window.innerWidth && getComputedStyle(overlay).visibility === 'visible';
  }
  function waitForCover(token, instant) {
    return new Promise(function (resolve) {
      if (instant || reduced()) { requestAnimationFrame(function () { resolve(token === serial); }); return; }
      var done = false;
      function finish() {
        if (done) return;
        done = true;
        overlay.removeEventListener('transitionend', onEnd);
        clearTimeout(fallback);
        resolve(token === serial && covered());
      }
      function onEnd(e) { if (e.target === overlay && e.propertyName === 'transform') finish(); }
      var fallback = setTimeout(finish, DURATION + 120);
      overlay.addEventListener('transitionend', onEnd);
      requestAnimationFrame(function () { if (covered()) finish(); });
    });
  }
  function show(instant) {
    var el = ensure(), token = ++serial;
    el.hidden = false;
    el.setAttribute('aria-hidden', 'false');
    el.classList.remove('is-leaving');
    el.classList.toggle('is-static', !!reduced());
    var immediate = !!instant || reduced();
    if (immediate) {
      el.classList.add('is-instant', 'is-active');
      void el.offsetWidth;
      document.documentElement.classList.remove('pb-page-arriving');
      state = 'covered';
      return waitForCover(token, true);
    }
    state = 'entering';
    void el.offsetWidth;
    el.classList.add('is-active');
    return waitForCover(token, false).then(function (ok) { if (ok) state = 'covered'; return ok; });
  }
  function hide() {
    var el = ensure(), token = ++serial;
    document.documentElement.classList.remove('pb-page-arriving', 'pb-page-leaving');
    if (el.hidden && state === 'idle') return Promise.resolve(true);
    el.setAttribute('aria-hidden', 'true');
    el.classList.remove('is-active', 'is-instant');
    void el.offsetWidth;
    if (reduced()) {
      el.hidden = true; el.classList.remove('is-leaving', 'is-static'); state = 'idle'; busy = false;
      return Promise.resolve(true);
    }
    state = 'leaving';
    el.classList.add('is-leaving');
    return new Promise(function (resolve) {
      var done = false;
      function finish() {
        if (done || token !== serial) return;
        done = true;
        el.removeEventListener('transitionend', onEnd);
        clearTimeout(fallback);
        if (!el.classList.contains('is-active')) {
          el.hidden = true; el.classList.remove('is-leaving', 'is-static'); state = 'idle';
        }
        busy = false;
        resolve(true);
      }
      function onEnd(e) { if (e.target === overlay && e.propertyName === 'transform') finish(); }
      var fallback = setTimeout(finish, DURATION + 120);
      el.addEventListener('transitionend', onEnd);
    });
  }
  function remember(url) {
    try { sessionStorage.setItem(KEY, JSON.stringify({ at: Date.now(), url: url })); } catch (e) {}
  }
  function navigate(url) {
    if (busy) return Promise.resolve(false);
    busy = true;
    remember(url);
    if (window.PartyabSound) window.PartyabSound.play();
    return show(false).then(function (ok) {
      if (!ok) { busy = false; return false; }
      document.documentElement.classList.add('pb-page-leaving');
      requestAnimationFrame(function () { window.location.assign(url); });
      return true;
    });
  }
  function internalUrl(href) {
    var url;
    try { url = new URL(href, location.href); } catch (e) { return null; }
    if (url.origin !== location.origin) return null;
    if (/\/(wp-admin|wp-login\.php)/.test(url.pathname) || /action=logout|add-to-cart=|remove_item=/.test(url.search)) return null;
    return url;
  }
  function internalLink(a, event) {
    if (!a || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
    if (a.target && a.target !== '_self') return false;
    if (a.hasAttribute('download') || a.closest('[data-no-transition], .ajax_add_to_cart, .woocommerce-product-gallery, .elementor-editor-active, [data-elementor-open-lightbox]')) return false;
    var href = a.getAttribute('href') || '';
    if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(href)) return false;
    var url = internalUrl(a.href);
    if (!url) return false;
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
    if (!(form instanceof HTMLFormElement) || event.defaultPrevented) return;
    if (form.hasAttribute('data-no-transition') || form.closest('.woocommerce-checkout, [data-no-transition]')) return;
    if (form.matches('[data-car-form], [data-coupon], #demo-checkout, #demo-contact, #demo-auth, #address-form')) return;
    if ((form.method || 'get').toLowerCase() !== 'get') return;
    var url = internalUrl(form.action || location.href);
    if (!url) return;
    event.preventDefault();
    var data = new FormData(form);
    url.search = '';
    data.forEach(function (value, key) { if (typeof value === 'string') url.searchParams.append(key, value); });
    navigate(url.href);
  });
  window.addEventListener('pageshow', function (event) {
    var arriving = false;
    try { arriving = !!sessionStorage.getItem(KEY); sessionStorage.removeItem(KEY); } catch (e) {}
    busy = false;
    if (event.persisted) { hide(); return; }
    if (arriving && !reduced()) {
      show(true).then(function () { setTimeout(hide, 100); });
    } else {
      document.documentElement.classList.remove('pb-page-arriving');
      hide();
    }
  });
  window.addEventListener('pagehide', function () { busy = false; });
  window.PartyabTransition = { show: show, hide: hide, navigate: navigate, get state() { return state; } };
})();
