/* ==========================================================================
   PARITYAB UI — partyab.js (v1.1)
   بدون وابستگی • سبک • سازگار با prefers-reduced-motion
   1) تیلت سه‌بعدی هیرو · 2) شمارنده اعداد فارسی · 3) نمایش تدریجی با اسکرول
   4) میکرو‌تعامل افزودن به سبد · 5) درخشش دنبال‌کننده نشانگر · 6) علاقه‌مندی
   ========================================================================== */
(function () {
  "use strict";
  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var fa = new Intl.NumberFormat("fa-IR");

  /* ---------- 1) hero 3D tilt ---------- */
  document.querySelectorAll("[data-scene]").forEach(function (scene) {
    var stage = scene.querySelector(".scene-stage");
    if (!stage || reduce) return;
    var baseRy = -13, baseRx = 5, raf = null;

    scene.addEventListener("pointermove", function (e) {
      if (raf) return;
      raf = requestAnimationFrame(function () {
        raf = null;
        var r = scene.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width - 0.5;
        var y = (e.clientY - r.top) / r.height - 0.5;
        stage.style.setProperty("--ry", (baseRy + x * 22).toFixed(2) + "deg");
        stage.style.setProperty("--rx", (baseRx - y * 15).toFixed(2) + "deg");
        stage.style.setProperty("--px", (x * 16).toFixed(2) + "px");
      });
    });
    scene.addEventListener("pointerleave", function () {
      stage.style.setProperty("--ry", baseRy + "deg");
      stage.style.setProperty("--rx", baseRx + "deg");
      stage.style.setProperty("--px", "0px");
    });
  });

  /* ---------- 2) animated counters (Persian digits) ---------- */
  function countUp(el) {
    if (el.dataset.counted) return;
    el.dataset.counted = "1";
    var target = parseFloat(el.dataset.count);
    var suffix = el.dataset.suffix || "";
    if (isNaN(target)) return;
    if (reduce) { el.textContent = fa.format(target) + suffix; return; }
    var dur = 1500, t0 = performance.now();
    (function tick(t) {
      var p = Math.min(1, (t - t0) / dur);
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = fa.format(Math.round(target * eased)) + suffix;
      if (p < 1) requestAnimationFrame(tick);
    })(t0);
  }

  /* ---------- 3) reveal on scroll ---------- */
  function activate(root) {
    if (root.classList) root.classList.add("in");
    if (root.matches && root.matches("[data-count]")) countUp(root);
    root.querySelectorAll("[data-count]").forEach(countUp);
  }

  var io = ("IntersectionObserver" in window) ? new IntersectionObserver(function (entries) {
    entries.forEach(function (en) {
      if (!en.isIntersecting) return;
      activate(en.target);
      io.unobserve(en.target);
    });
  }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" }) : null;

  var seen = new Set();
  document.querySelectorAll("[data-reveal]").forEach(function (el, i) {
    if (!el.style.getPropertyValue("--d")) el.style.setProperty("--d", (i % 6) * 0.06 + "s");
    seen.add(el);
    if (io) io.observe(el); else activate(el);
  });
  /* شمارنده‌هایی که بیرون از بلوک‌های reveal هستند */
  document.querySelectorAll("[data-count]").forEach(function (el) {
    if (el.closest("[data-reveal]") || !io) return;
    io.observe(el);
  });

  /* ---------- 4) add-to-cart micro interaction ---------- */
  var cartCount = 3;
  document.addEventListener("click", function (e) {
    var btn = e.target.closest(".pc-add");
    if (btn && !btn.classList.contains("is-added")) {
      var t = btn.querySelector(".t"), old = t ? t.textContent : "";
      var use = btn.querySelector("svg use"), oldHref = use ? use.getAttribute("href") : "";
      btn.classList.add("is-added");
      flyToCart(btn);
      if (t) t.textContent = "افزوده شد";
      if (use) use.setAttribute("href", "#i-check");

      var badge = document.querySelector(".h-action .count");
      if (badge) {
        cartCount++;
        badge.textContent = fa.format(cartCount);
        badge.classList.remove("bump");
        void badge.offsetWidth;
        badge.classList.add("bump");
      }
      setTimeout(function () {
        btn.classList.remove("is-added");
        if (t) t.textContent = old;
        if (use) use.setAttribute("href", oldHref || "#i-cart");
      }, 1500);
    }

    /* wishlist toggle */
    var fav = e.target.closest(".pc-tool[data-fav]");
    if (fav) {
      fav.classList.toggle("is-on");
      var u = fav.querySelector("use");
      if (u) u.setAttribute("href", fav.classList.contains("is-on") ? "#i-heart-fill" : "#i-heart");
    }

    /* back to top */
    if (e.target.closest(".ft-top-btn")) {
      window.scrollTo({ top: 0, behavior: reduce ? "auto" : "smooth" });
    }
  });

  /* ---------- 5) pointer glow on product cards ---------- */
  if (!reduce) {
    document.querySelectorAll(".pc").forEach(function (card) {
      card.addEventListener("pointermove", function (e) {
        var r = card.getBoundingClientRect();
        card.style.setProperty("--mx", ((e.clientX - r.left) / r.width) * 100 + "%");
        card.style.setProperty("--my", ((e.clientY - r.top) / r.height) * 100 + "%");
      });
    });
  }
  /* ---------- 7) scroll progress ---------- */
  var prog = document.querySelector(".scroll-progress i");
  if (prog) {
    var onScroll = function () {
      var h = document.documentElement;
      var max = h.scrollHeight - h.clientHeight;
      prog.style.setProperty("--p", (max > 0 ? (h.scrollTop / max) * 100 : 0).toFixed(2) + "%");
    };
    document.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    onScroll();
  }

  /* ---------- 8) countdown (flash sale) ---------- */
  document.querySelectorAll("[data-countdown]").forEach(function (box) {
    var left = parseInt(box.dataset.countdown, 10) || 8 * 3600;
    var f2 = new Intl.NumberFormat("fa-IR", { minimumIntegerDigits: 2, useGrouping: false });
    var cells = {
      h: box.querySelector('[data-cd="h"]'),
      m: box.querySelector('[data-cd="m"]'),
      s: box.querySelector('[data-cd="s"]')
    };
    function paint(key, val) {
      var el = cells[key];
      if (!el) return;
      var txt = f2.format(val);
      if (el.textContent === txt) return;
      el.textContent = txt;
      if (!reduce) { el.classList.remove("tick"); void el.offsetWidth; el.classList.add("tick"); }
    }
    function render() {
      var t = Math.max(0, left);
      paint("h", Math.floor(t / 3600));
      paint("m", Math.floor((t % 3600) / 60));
      paint("s", t % 60);
      if (left > 0) left--;
    }
    render();
    setInterval(render, 1000);
  });

  /* ---------- 9) horizontal rails: arrows + drag ---------- */
  document.querySelectorAll(".rail").forEach(function (rail) {
    var track = rail.querySelector(".rail-track");
    if (!track) return;
    var first = track.firstElementChild;
    function step() {
      var w = first ? first.getBoundingClientRect().width : 200;
      return Math.round(w * 1.15);
    }
    var prev = rail.querySelector('[data-rail="prev"]');
    var next = rail.querySelector('[data-rail="next"]');
    if (next) next.addEventListener("click", function () { track.scrollBy({ left: -step(), behavior: "smooth" }); });
    if (prev) prev.addEventListener("click", function () { track.scrollBy({ left: step(), behavior: "smooth" }); });

    if (!reduce) {
      var down = false, sx = 0, sl = 0, moved = false;
      track.addEventListener("pointerdown", function (e) {
        down = true; moved = false; sx = e.clientX; sl = track.scrollLeft;
        track.classList.add("is-dragging");
      });
      track.addEventListener("pointermove", function (e) {
        if (!down) return;
        var dx = e.clientX - sx;
        if (Math.abs(dx) > 4) moved = true;
        track.scrollLeft = sl - dx;
      });
      ["pointerup", "pointercancel", "pointerleave"].forEach(function (ev) {
        track.addEventListener(ev, function () {
          if (!down) return;
          down = false;
          track.classList.remove("is-dragging");
          if (moved) {
            var after = function (e2) { e2.preventDefault(); e2.stopPropagation(); track.removeEventListener("click", after, true); };
            track.addEventListener("click", after, true);
            setTimeout(function () { track.removeEventListener("click", after, true); }, 60);
          }
        });
      });
    }
  });

  /* ---------- 10) fly-to-cart ---------- */
  function flyToCart(fromEl) {
    if (reduce) return;
    var cart = document.querySelector(".h-action .count") || document.querySelector(".h-action");
    if (!cart || !fromEl || !fromEl.animate) return;
    var a = fromEl.getBoundingClientRect();
    var b = cart.getBoundingClientRect();
    var dot = document.createElement("div");
    dot.className = "fly-dot";
    dot.innerHTML = '<svg><use href="#i-cart"/></svg>';
    dot.style.left = a.left + a.width / 2 - 14 + "px";
    dot.style.top = a.top + a.height / 2 - 14 + "px";
    document.body.appendChild(dot);
    var dx = b.left + b.width / 2 - (a.left + a.width / 2);
    var dy = b.top + b.height / 2 - (a.top + a.height / 2);
    var anim = dot.animate([
      { transform: "translate(0,0) scale(1)", opacity: 1, offset: 0 },
      { transform: "translate(" + dx * 0.45 + "px," + (dy * 0.45 - 110) + "px) scale(.85)", opacity: 1, offset: 0.6 },
      { transform: "translate(" + dx + "px," + dy + "px) scale(.3)", opacity: 0.15, offset: 1 }
    ], { duration: 760, easing: "cubic-bezier(.38,0,.55,1)" });
    anim.onfinish = function () { dot.remove(); };
    anim.oncancel = function () { dot.remove(); };
  }

  /* ---------- 11) magnetic buttons ---------- */
  if (!reduce) {
    document.querySelectorAll("[data-magnetic]").forEach(function (el) {
      el.addEventListener("pointermove", function (e) {
        var r = el.getBoundingClientRect();
        var x = (e.clientX - r.left - r.width / 2) / r.width;
        var y = (e.clientY - r.top - r.height / 2) / r.height;
        el.style.transform = "translate(" + (x * 9).toFixed(1) + "px," + (y * 7).toFixed(1) + "px)";
      });
      el.addEventListener("pointerleave", function () { el.style.transform = ""; });
    });
  }

})();
