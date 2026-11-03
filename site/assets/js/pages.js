/* ==========================================================================
   PARITYAB — PAGE INTERACTIONS  •  v2.0
   وابستگی ندارد؛ روی تمام صفحات بعد از partyab.js لود می‌شود.
   ========================================================================== */
(function () {
  "use strict";

  var $  = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
  var fa = new Intl.NumberFormat("fa-IR");

  /* ---------------------------- toast ----------------------------------- */
  var wrap = $(".toast-wrap");
  window.pbToast = function (msg) {
    if (!wrap) return;
    var el = document.createElement("div");
    el.className = "toast";
    el.innerHTML = '<svg><use href="#i-check"/></svg><span></span>';
    $("span", el).textContent = msg;
    wrap.appendChild(el);
    setTimeout(function () {
      el.style.transition = "opacity .3s, transform .3s";
      el.style.opacity = "0";
      el.style.transform = "translateY(10px)";
      setTimeout(function () { el.remove(); }, 320);
    }, 2800);
  };

  /* ---------------------------- mobile drawer ---------------------------- */
  var drawer = $(".drawer"), backdrop = $(".drawer-backdrop");
  function setDrawer(open) {
    if (!drawer || !backdrop) return;
    drawer.classList.toggle("is-open", open);
    backdrop.classList.toggle("is-open", open);
    document.body.style.overflow = open ? "hidden" : "";
    var t = $("[data-drawer-open]");
    if (t) t.setAttribute("aria-expanded", open ? "true" : "false");
  }
  $$("[data-drawer-open]").forEach(function (b) { b.addEventListener("click", function () { setDrawer(true); }); });
  $$("[data-drawer-close]").forEach(function (b) { b.addEventListener("click", function () { setDrawer(false); }); });
  if (backdrop) backdrop.addEventListener("click", function () { setDrawer(false); });
  document.addEventListener("keydown", function (e) { if (e.key === "Escape") setDrawer(false); });

  /* ------------------------------- tabs --------------------------------- */
  $$("[data-tab]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var group = btn.getAttribute("data-tab-group") || "default";
      $$('[data-tab][data-tab-group="' + group + '"]').forEach(function (b) { b.classList.remove("is-active"); });
      $$('[data-tab-panel][data-tab-group="' + group + '"]').forEach(function (p) { p.classList.add("hidden"); });
      btn.classList.add("is-active");
      var panel = $('[data-tab-panel="' + btn.getAttribute("data-tab") + '"][data-tab-group="' + group + '"]');
      if (panel) panel.classList.remove("hidden");
    });
  });

  /* ---------------------------- product gallery -------------------------- */
  $$("[data-thumb]").forEach(function (thumb) {
    thumb.addEventListener("click", function () {
      var i = thumb.getAttribute("data-thumb");
      $$("[data-thumb]").forEach(function (t) { t.classList.remove("is-active"); });
      thumb.classList.add("is-active");
      $$("[data-shot]").forEach(function (s) {
        s.classList.toggle("is-active", s.getAttribute("data-shot") === i);
      });
    });
  });

  /* ------------------------------- steppers ----------------------------- */
  $$("[data-stepper]").forEach(function (box) {
    var out = $("span[data-qty]", box);
    var update = function (delta) {
      var n = Math.max(1, Math.min(99, (parseInt(out.textContent.replace(/[^\d]/g, ""), 10) || 1) + delta));
      out.textContent = fa.format(n);
      box.dispatchEvent(new CustomEvent("qty-change", { bubbles: true, detail: { qty: n } }));
    };
    $("[data-minus]", box) && $("[data-minus]", box).addEventListener("click", function () { update(-1); });
    $("[data-plus]", box)  && $("[data-plus]", box).addEventListener("click", function () { update(1); });
  });

  /* ------------------------------ cart ---------------------------------- */
  $$("[data-remove-row]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var row = btn.closest(".cart-row");
      if (row) {
        row.style.transition = "opacity .25s, transform .25s";
        row.style.opacity = "0";
        row.style.transform = "translateX(-18px)";
        setTimeout(function () { row.remove(); }, 260);
      }
      window.pbToast("کالا از سبد خرید حذف شد");
    });
  });
  var coupon = $("[data-coupon]");
  if (coupon) coupon.addEventListener("click", function () {
    var input = $("[data-coupon-input]");
    var code = input && input.value.trim();
    window.pbToast(code ? "کد «" + code + "» بررسی شد" : "لطفاً کد تخفیف را وارد کنید");
  });

  /* --------------------------- payment options --------------------------- */
  $$("[data-pay]").forEach(function (opt) {
    opt.addEventListener("click", function () {
      $$("[data-pay]").forEach(function (o) { o.classList.remove("is-active"); });
      opt.classList.add("is-active");
    });
  });

  /* ------------------------- shop grid / filters ------------------------- */
  $$("[data-view]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      $$("[data-view]").forEach(function (b) { b.classList.remove("is-active"); });
      btn.classList.add("is-active");
      var grid = $("[data-pc-grid]");
      if (grid) grid.classList.toggle("is-list", btn.getAttribute("data-view") === "list");
    });
  });
  var fToggle = $("[data-filter-toggle]");
  if (fToggle) fToggle.addEventListener("click", function () {
    var f = $(".shop-layout .filters");
    if (f) f.classList.toggle("is-open");
  });
  $$(".f-active").forEach(function (chip) {
    chip.addEventListener("click", function () {
      chip.remove();
      window.pbToast("فیلتر حذف شد");
    });
  });
  $$(".f-block > h4").forEach(function (h) {
    h.addEventListener("click", function () {
      var block = h.parentElement;
      block.classList.toggle("is-collapsed");
      $$(":scope > *:not(h4)", block).forEach(function (el) { el.classList.toggle("hidden"); });
      h.querySelector("svg") && h.querySelector("svg").style.setProperty("transform", block.classList.contains("is-collapsed") ? "rotate(-90deg)" : "");
    });
  });

  /* ------------------------------- FAQ ---------------------------------- */
  $$(".faq-q").forEach(function (q) {
    q.addEventListener("click", function () {
      var item = q.closest(".faq-item");
      var open = item.classList.contains("is-open");
      $$(".faq-item").forEach(function (i) { i.classList.remove("is-open"); });
      if (!open) item.classList.add("is-open");
    });
  });

  /* --------------------------- generic forms ---------------------------- */
  $$("form[data-demo-form]").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      var msg = form.getAttribute("data-demo-form");
      window.pbToast(msg);
      form.reset && form.reset();
    });
  });
  $$("[data-toast]").forEach(function (b) {
    b.addEventListener("click", function () { window.pbToast(b.getAttribute("data-toast")); });
  });
  $$(".ft-news-form").forEach(function (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      window.pbToast("ایمیل شما ثبت شد — خبرنامه فنی از هفته آینده میرسد");
      var i = $("input", form); if (i) i.value = "";
    });
  });

  /* --------------------------- choose your car --------------------------- */
  $$("[data-confirm-car]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var root = btn.closest(".findcar, .car-select") || document;
      var sels = $$("select", root);
      var name = sels.length > 1 ? sels[1].value : (sels[0] ? sels[0].value : "خودروی شما");
      window.pbToast("خودروی «" + name + "» ذخیره شد — فقط قطعات سازگار نمایش داده میشود");
    });
  });

  /* --------------------------- letters / compare ------------------------- */
  $$(".letter-bar button").forEach(function (b) {
    b.addEventListener("click", function () {
      $$(".letter-bar button").forEach(function (x) { x.classList.remove("is-active"); });
      b.classList.add("is-active");
    });
  });
  $$("[data-compare-remove]").forEach(function (b) {
    b.addEventListener("click", function () {
      var col = b.closest(".c-head");
      if (col) { col.innerHTML = '<div class="empty-state"><p>حذف شد</p></div>'; }
      window.pbToast("کالا از مقایسه حذف شد");
    });
  });

  /* ------------------------------ search -------------------------------- */
  var searchForm = $(".search");
  if (searchForm) searchForm.addEventListener("submit", function (e) {
    e.preventDefault();
    var input = $("input", searchForm);
    var q = input ? input.value.trim() : "";
    window.location.href = "search.html" + (q ? "?q=" + encodeURIComponent(q) : "");
  });
  var q = new URLSearchParams(window.location.search).get("q");
  if (q) {
    $$("[data-query]").forEach(function (el) { el.textContent = q; });
    var si = $(".search input"); if (si) si.value = q;
  }

})();
