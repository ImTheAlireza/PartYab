/* =====================================================================
 * PartYab — behaviour for the hero demo pages
 * (countdown timers + the hero slider). No dependencies, no network.
 * ================================================================== */
(function () {
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  function pad(n) {
    return String(n).padStart(2, '0').replace(/\d/g, function (d) { return FA[+d]; });
  }

  /* ---------------- countdowns ---------------- */
  var clocks = [].slice.call(document.querySelectorAll('[data-countdown]'));
  if (clocks.length) {
    // each clock counts down from 7h 42m 18s for the demo
    var ends = clocks.map(function () {
      return Date.now() + (7 * 3600 + 42 * 60 + 18) * 1000;
    });
    var tick = function () {
      clocks.forEach(function (el, i) {
        var left = Math.max(0, Math.floor((ends[i] - Date.now()) / 1000));
        var parts = {
          h: Math.floor(left / 3600),
          m: Math.floor((left % 3600) / 60),
          s: left % 60
        };
        Object.keys(parts).forEach(function (k) {
          var node = el.querySelector('[data-cd=' + k + ']');
          if (node) node.textContent = pad(parts[k]);
        });
      });
    };
    tick();
    setInterval(tick, 1000);
  }

  /* ---------------- slider ---------------- */
  [].slice.call(document.querySelectorAll('[data-slider]')).forEach(function (slider) {
    var track = slider.querySelector('[data-track]');
    if (!track) return;
    var slides = [].slice.call(track.children);
    var dots = [].slice.call(slider.querySelectorAll('[data-dots] button'));
    var next = slider.querySelector('[data-next]');
    var prev = slider.querySelector('[data-prev]');
    var index = 0;
    var timer = null;
    var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function paint() {
      dots.forEach(function (d, k) { d.classList.toggle('on', k === index); });
    }

    function go(i) {
      index = (i + slides.length) % slides.length;
      var slide = slides[index];
      if (!slide) return;
      // physical distance between the slide and the track viewport start;
      // negative in RTL, which is exactly the sign scrollBy expects (Chrome 85+/FF 76+)
      var delta = slide.getBoundingClientRect().left - track.getBoundingClientRect().left;
      if (Math.abs(delta) < 2) { paint(); return; }
      try {
        track.scrollBy({ left: delta, behavior: reduce ? 'auto' : 'smooth' });
      } catch (e) {
        track.scrollLeft += delta;
      }
      paint();
    }

    dots.forEach(function (d, i) {
      d.addEventListener('click', function () { go(i); restart(); });
    });
    if (next) next.addEventListener('click', function () { go(index + 1); restart(); });
    if (prev) prev.addEventListener('click', function () { go(index - 1); restart(); });

    function start() {
      if (reduce || slides.length < 2) return;
      timer = setInterval(function () { go(index + 1); }, 6500);
    }
    function restart() { clearInterval(timer); start(); }

    slider.addEventListener('mouseenter', function () { clearInterval(timer); });
    slider.addEventListener('mouseleave', start);

    var raf;
    track.addEventListener('scroll', function () {
      cancelAnimationFrame(raf);
      raf = requestAnimationFrame(function () {
        var mid = track.scrollLeft + track.clientWidth / 2;
        var best = 0;
        var bestD = Infinity;
        slides.forEach(function (s, k) {
          var c = s.offsetLeft - track.offsetLeft + s.offsetWidth / 2;
          var d = Math.abs(c - mid);
          if (d < bestD) { bestD = d; best = k; }
        });
        if (best !== index) { index = best; paint(); }
      });
    }, { passive: true });

    // keep the height stable while slides differ in content length
    function sizeTrack() {
      var h = 0;
      slides.forEach(function (s) { h = Math.max(h, s.offsetHeight); });
      if (h) track.style.minHeight = h + 'px';
    }
    window.addEventListener('resize', sizeTrack);
    window.addEventListener('load', sizeTrack);
    sizeTrack();

    start();
    paint();
  });
})();
