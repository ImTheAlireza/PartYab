/* PartYab motion pack. No library, no layout dependency, reduced-motion aware. */
(function () {
  'use strict';
  var root = document.documentElement;
  var media = window.matchMedia('(prefers-reduced-motion: reduce)');
  var paused = false;
  try { paused = localStorage.getItem('partyab-motion') === 'paused'; } catch (e) {}
  var reduced = function () { return media.matches || paused; };
  var fa = new Intl.NumberFormat('fa-IR');
  var observer;

  function syncMotion() {
    root.classList.toggle('pb-motion-paused', paused);
    document.querySelectorAll('[data-motion-toggle]').forEach(function (button) {
      button.setAttribute('aria-pressed', String(paused));
      var label = button.querySelector('span');
      if (label) label.textContent = media.matches ? 'حرکت کمتر فعال است' : paused ? 'فعال کردن انیمیشن‌ها' : 'توقف انیمیشن‌ها';
    });
  }
  syncMotion();
  document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-motion-toggle]');
    if (!button) return;
    if (media.matches) { window.PartyabUI.toast('کاهش حرکت در تنظیمات دستگاه شما فعال است.'); return; }
    paused = !paused;
    try { localStorage.setItem('partyab-motion', paused ? 'paused' : 'on'); } catch (e) {}
    syncMotion();
    if (paused) document.querySelectorAll('[data-reveal]').forEach(function (el) { el.classList.add('in'); });
  });
  if (media.addEventListener) media.addEventListener('change', syncMotion);

  function countUp(el) {
    if (el.dataset.counted) return;
    el.dataset.counted = '1';
    var target = Number(el.dataset.count);
    if (!Number.isFinite(target)) return;
    if (reduced()) { el.textContent = fa.format(target) + (el.dataset.suffix || ''); return; }
    var start = performance.now();
    function frame(now) {
      var p = Math.min(1, (now - start) / 1200);
      el.textContent = fa.format(Math.round(target * (1 - Math.pow(1 - p, 3)))) + (el.dataset.suffix || '');
      if (p < 1 && !reduced()) requestAnimationFrame(frame);
      else el.textContent = fa.format(target) + (el.dataset.suffix || '');
    }
    requestAnimationFrame(frame);
  }
  function reveal(scope) {
    var nodes = (scope || document).querySelectorAll('[data-reveal]:not(.in), [data-count]:not([data-counted])');
    if (!('IntersectionObserver' in window)) {
      nodes.forEach(function (el) { el.classList.add('in'); if (el.dataset.count) countUp(el); });
      return;
    }
    if (!observer) observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('in');
        if (entry.target.dataset.count) countUp(entry.target);
        entry.target.querySelectorAll('[data-count]').forEach(countUp);
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });
    nodes.forEach(function (el) { observer.observe(el); });
  }
  function tagCommerce() {
    document.querySelectorAll('.woocommerce ul.products li.product:not([data-reveal]), .pb-strip:not([data-reveal])').forEach(function (el) { el.setAttribute('data-reveal', ''); });
  }
  root.classList.add('pb-motion-ready');
  tagCommerce();
  reveal();

  if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
    document.querySelectorAll('[data-scene]').forEach(function (scene) {
      var stage = scene.querySelector('.pb-hero__stage, .scene-stage');
      if (!stage) return;
      var frame = null;
      scene.addEventListener('pointermove', function (event) {
        if (reduced() || frame) return;
        frame = requestAnimationFrame(function () {
          frame = null;
          var rect = scene.getBoundingClientRect();
          var x = (event.clientX - rect.left) / rect.width - .5;
          var y = (event.clientY - rect.top) / rect.height - .5;
          stage.style.setProperty('--ry', (x * 12).toFixed(2) + 'deg');
          stage.style.setProperty('--rx', (-y * 8).toFixed(2) + 'deg');
        });
      });
      scene.addEventListener('pointerleave', function () {
        if (frame) cancelAnimationFrame(frame);
        frame = null;
        stage.style.setProperty('--ry', '0deg'); stage.style.setProperty('--rx', '0deg');
      });
    });
    document.querySelectorAll('[data-magnetic]').forEach(function (button) {
      button.addEventListener('pointermove', function (event) {
        if (reduced()) return;
        var rect = button.getBoundingClientRect();
        button.style.transform = 'translate(' + ((event.clientX - rect.left - rect.width / 2) * .07) + 'px,' + ((event.clientY - rect.top - rect.height / 2) * .12) + 'px)';
      });
      button.addEventListener('pointerleave', function () { button.style.transform = ''; });
    });
    document.addEventListener('pointermove', function (event) {
      if (reduced()) return;
      var card = event.target.closest('.pb-product-card, .pc');
      if (!card) return;
      var rect = card.getBoundingClientRect();
      card.style.setProperty('--mx', ((event.clientX - rect.left) / rect.width * 100) + '%');
      card.style.setProperty('--my', ((event.clientY - rect.top) / rect.height * 100) + '%');
    }, { passive: true });
  }

  var progress = document.querySelector('.pb-scroll-progress i');
  var scheduled = false;
  function paintProgress() {
    scheduled = false;
    if (!progress) return;
    var max = root.scrollHeight - root.clientHeight;
    progress.style.setProperty('--p', (max > 0 ? root.scrollTop / max * 100 : 0) + '%');
  }
  document.addEventListener('scroll', function () {
    if (!scheduled) { scheduled = true; requestAnimationFrame(paintProgress); }
  }, { passive: true });
  window.addEventListener('resize', paintProgress);
  paintProgress();

  document.querySelectorAll('.pb-rail').forEach(function (rail) {
    var track = rail.querySelector('.pb-rail__track');
    if (!track) return;
    rail.querySelectorAll('[data-rail]').forEach(function (button) {
      button.addEventListener('click', function () {
        var card = track.firstElementChild;
        var step = card ? card.getBoundingClientRect().width + 16 : 260;
        track.scrollBy({ left: step * (button.dataset.rail === 'next' ? -1 : 1), behavior: reduced() ? 'auto' : 'smooth' });
      });
    });
  });

  document.querySelectorAll('[data-countdown]').forEach(function (box) {
    var end = Number(box.dataset.countdown);
    if (!Number.isFinite(end)) return;
    var formatter = new Intl.NumberFormat('fa-IR', { minimumIntegerDigits: 2, useGrouping: false });
    var interval;
    function render() {
      var left = Math.max(0, Math.floor((end - Date.now()) / 1000));
      var values = { h: Math.floor(left / 3600), m: Math.floor(left % 3600 / 60), s: left % 60 };
      Object.keys(values).forEach(function (key) {
        var cell = box.querySelector('[data-cd="' + key + '"]');
        var text = formatter.format(values[key]);
        if (cell && cell.textContent !== text) {
          cell.textContent = text;
          if (!reduced()) { cell.classList.remove('tick'); void cell.offsetWidth; cell.classList.add('tick'); }
        }
      });
      if (!left) { clearInterval(interval); box.setAttribute('aria-label', 'پیشنهاد به پایان رسید'); }
    }
    render(); interval = setInterval(render, 1000);
  });

  window.PartyabMotion = { reveal: reveal, reduced: reduced, countUp: countUp };
  document.addEventListener('updated_wc_div', function () { tagCommerce(); reveal(); paintProgress(); });
  if (window.jQuery) window.jQuery(document.body).on('updated_wc_div wc_fragments_refreshed', function () { tagCommerce(); reveal(); paintProgress(); });
})();
