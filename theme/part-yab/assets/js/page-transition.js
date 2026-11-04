/* PartYab page transition: opaque sliding curtain, covered before navigation.
 * Failsafe-first: the arrival cover must never remain stuck if this script is
 * delayed, cached badly, or if pageshow is missed. */
(function () {
  'use strict';
  var KEY = 'partyab-page-transition';
  var DURATION = 420;
  var ARRIVAL_WATCHDOG = 2500;
  var DEPART_WATCHDOG = 5000;
  var overlay = null, state = 'idle', busy = false, departing = false, serial = 0, watchdog = null, arrivalDone = false;
  var reduced = function () { return !!(window.PartyabMotion && window.PartyabMotion.reduced && window.PartyabMotion.reduced()); };
  var MARKUP = '<div class="pb-page-loader__panel"><span class="pb-page-loader__pulse" aria-hidden="true"><i class="pb-page-loader__wave"></i><i class="pb-page-loader__wave"></i><i class="pb-page-loader__wave"></i></span><span class="pb-page-loader__logo" aria-hidden="true"><svg><use href="#i-logo"></use></svg></span><span class="screen-reader-text">در حال آماده‌سازی صفحه</span></div>';

  function ensure() {
    if (overlay && overlay.isConnected) return overlay;
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
  function clearWatchdog() { if (watchdog) { clearTimeout(watchdog); watchdog = null; } }
  function armWatchdog(ms) {
    clearWatchdog();
    watchdog = setTimeout(function () { if (state !== 'idle' || busy) forceHide(); }, ms);
  }
  function forceHide() {
    clearWatchdog();
    var el = ensure();
    document.documentElement.classList.remove('pb-page-arriving', 'pb-page-leaving');
    el.hidden = true;
    el.setAttribute('aria-hidden', 'true');
    el.classList.remove('is-active', 'is-leaving', 'is-instant', 'is-static');
    state = 'idle'; busy = false; departing = false;
  }
  function covered() {
    var r = overlay.getBoundingClientRect();
    return r.left <= 0 && r.right >= window.innerWidth && getComputedStyle(overlay).visibility === 'visible';
  }
  function waitForCover(token, instant) {
    return new Promise(function (resolve) {
      if (instant || reduced()) { requestAnimationFrame(function () { resolve(token === serial); }); return; }
      var done = false, start = performance.now();
      function finish(ok) {
        if (done) return;
        done = true;
        overlay.removeEventListener('transitionend', onEnd);
        clearTimeout(fallback);
        resolve(ok && token === serial);
      }
      function onEnd(e) { if (e.target === overlay && e.propertyName === 'transform') finish(covered()); }
      function poll() {
        if (done) return;
        if (token !== serial) { finish(false); return; }
        if (covered()) { finish(true); return; }
        if (performance.now() - start > DURATION + 900) { finish(covered()); return; }
        requestAnimationFrame(poll);
      }
      var fallback = setTimeout(function () { finish(covered()); }, DURATION + 1000);
      overlay.addEventListener('transitionend', onEnd);
      requestAnimationFrame(poll);
    });
  }
  function show(instant) {
    var el = ensure(), token = ++serial;
    el.hidden = false;
    el.setAttribute('aria-hidden', 'false');
    el.classList.remove('is-leaving');
    el.classList.toggle('is-static', !!reduced());
    var immediate = !!instant || reduced();
    armWatchdog(immediate ? ARRIVAL_WATCHDOG : DEPART_WATCHDOG);
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
    clearWatchdog();
    document.documentElement.classList.remove('pb-page-arriving', 'pb-page-leaving');
    if (el.hidden && state === 'idle') { busy = false; return Promise.resolve(true); }
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
        if (done) return;
        done = true;
        el.removeEventListener('transitionend', onEnd);
        clearTimeout(fallback);
        clearTimeout(hard);
        if (token !== serial) { resolve(false); return; }
        if (!el.classList.contains('is-active')) {
          el.hidden = true; el.classList.remove('is-leaving', 'is-static'); state = 'idle';
        }
        busy = false;
        resolve(true);
      }
      function onEnd(e) { if (e.target === overlay && e.propertyName === 'transform') finish(); }
      var fallback = setTimeout(finish, DURATION + 120);
      var hard = setTimeout(function () { if (token === serial) forceHide(); }, DURATION + 700);
      el.addEventListener('transitionend', onEnd);
    });
  }
  function remember(url) {
    try { sessionStorage.setItem(KEY, JSON.stringify({ at: Date.now(), url: url })); } catch (e) {}
  }
  function navigate(url) {
    if (busy || departing) return Promise.resolve(false);
    busy = true; departing = true;
    remember(url);
    if (window.PartyabSound) window.PartyabSound.play();
    var assigned = false;
    function go() {
      if (assigned || !departing) return;
      assigned = true;
      document.documentElement.classList.add('pb-page-leaving');
      requestAnimationFrame(function () {
        try { window.location.assign(url); } catch (e) { forceHide(); }
      });
    }
    // Never let a slow/failed cover animation trap the user on the old page.
    var hard = setTimeout(go, 2500);
    return show(false).then(function () {
      clearTimeout(hard);
      go();
      return true;
    }).catch(function () { clearTimeout(hard); forceHide(); return false; });
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

  function handleArrival(event) {
    var persisted = !!(event && event.persisted);
    if (departing && !persisted) return; // late pageshow/load on the page we are leaving
    var arriving = false;
    try { arriving = !!sessionStorage.getItem(KEY); sessionStorage.removeItem(KEY); } catch (e) {}
    busy = false;
    if (persisted) { hide(); return; }
    var bootCover = document.documentElement.classList.contains('pb-page-arriving');
    if (arrivalDone && !bootCover) return;
    arrivalDone = true;
    if ((arriving || bootCover) && !reduced()) {
      show(true).then(function () { setTimeout(hide, 100); });
    } else {
      document.documentElement.classList.remove('pb-page-arriving');
      hide();
    }
  }
  window.addEventListener('pageshow', handleArrival);
  document.addEventListener('DOMContentLoaded', function () { handleArrival(); });
  window.addEventListener('load', function () { if (document.documentElement.classList.contains('pb-page-arriving')) handleArrival(); });
  if (document.readyState !== 'loading') handleArrival();
  window.addEventListener('pagehide', function () { busy = false; departing = false; clearWatchdog(); });
  window.PartyabTransition = { show: show, hide: hide, forceHide: forceHide, navigate: navigate, get state() { return state; } };
})();
