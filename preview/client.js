/* Interactive preview behavior, sharing the theme's UI/motion helpers. */
(function () {
  'use strict';
  var S = window.DemoStore, UI = window.PartyabUI;
  var fa = function (n) { return new Intl.NumberFormat('fa-IR').format(n); };
  var esc = function (s) { return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
  var icon = function (n) { return '<svg class="pb-icon" aria-hidden="true"><use href="#i-' + n + '"></use></svg>'; };
  var $ = function (s, r) { return (r || document).querySelector(s); };
  var $$ = function (s, r) { return Array.from((r || document).querySelectorAll(s)); };
  var catalog = [];
  function sync() {
    var t = S.totals();
    $$('[data-cart-count]').forEach(function (el) { el.textContent = fa(t.count); });
    $$('[data-fav-count]').forEach(function (el) { el.textContent = fa(S.state.favorites.length); el.hidden = !S.state.favorites.length; });
    $$('[data-favorite]').forEach(function (el) { var on = S.state.favorites.includes(el.dataset.favorite); el.classList.toggle('is-on', on); el.setAttribute('aria-pressed', String(on)); });
    $$('[data-compare]').forEach(function (el) { var on = S.state.compare.includes(el.dataset.compare); el.classList.toggle('is-on', on); el.setAttribute('aria-pressed', String(on)); });
    renderCart(); renderCheckout(); renderAccount(); renderOrders(); renderFavorites(); renderCompare(); renderOrder();
    if (window.PartyabMotion) window.PartyabMotion.reveal();
  }
  function empty(title, text, href, label) { return '<div class="partyab-empty"><div class="pb-empty__icon">' + icon('cart') + '</div><p class="partyab-empty__title">' + title + '</p><p>' + text + '</p><a class="pb-btn" href="' + href + '">' + label + '</a></div>'; }
  function summary(t, checkout) {
    return '<div class="cart_totals"><h2>' + (checkout ? 'خلاصه سفارش' : 'جمع سبد خرید') + '</h2><table class="shop_table"><tbody><tr><th>جمع کالاها</th><td>' + fa(t.subtotal) + ' تومان</td></tr>' + (t.discount ? '<tr><th>تخفیف کد PARTYAB</th><td class="pb-discount">− ' + fa(t.discount) + ' تومان</td></tr>' : '') + '<tr><th>ارسال نمونه</th><td>' + (t.shipping ? fa(t.shipping) + ' تومان' : 'رایگان') + '</td></tr><tr class="order-total"><th>مبلغ قابل پرداخت</th><td><strong>' + fa(t.total) + ' تومان</strong></td></tr></tbody></table>' + (checkout ? '' : '<form class="coupon" data-coupon><input class="input-text" name="coupon" placeholder="کد تخفیف نمونه: PARTYAB" aria-label="کد تخفیف" autocomplete="off"><button class="button" type="submit">اعمال</button></form><div class="wc-proceed-to-checkout"><a class="pb-btn checkout-button" href="/checkout">ادامه و تسویه ' + icon('arrow-l') + '</a></div>') + '<ul class="pb-product-trust"><li>' + icon('shield') + 'پرداخت نمایشی</li><li>' + icon('truck') + 'ارسال نمونه</li></ul></div>';
  }
  function renderCart() {
    var root = $('#cart-root'); if (!root) return; var t = S.totals();
    if (!t.count) { root.innerHTML = empty('سبد خریدت هنوز خالی است', 'قطعه مورد نیازت را پیدا کن و با اطمینان به سبد اضافه کن.', '/shop', 'رفتن به فروشگاه'); root.style.display = 'block'; return; }
    root.style.display = '';
    var remain = Math.max(0, 5000000 - (t.subtotal - t.discount)); var pct = Math.min(100, (t.subtotal - t.discount) / 5000000 * 100);
    root.innerHTML = '<div class="pb-cart-main"><div class="pb-ship"><p class="pb-ship__text">' + (remain ? fa(remain) + ' تومان تا ارسال رایگان نمونه' : 'ارسال این سفارش نمونه رایگان است.') + '</p><span class="pb-ship__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="' + Math.round(pct) + '"><span style="width:' + pct + '%"></span></span></div><a class="pb-continue" href="/shop">' + icon('chev-r') + ' ادامه خرید</a><table class="shop_table"><thead><tr><th></th><th>قطعه</th><th>قیمت</th><th>تعداد</th><th>جمع</th></tr></thead><tbody>' + t.entries.map(function (e) { var p = e.product; return '<tr><td class="product-remove"><button type="button" data-remove="' + p.id + '" aria-label="حذف ' + esc(p.name) + '">×</button></td><td class="product-name"><a class="pb-cart-product" href="/product/' + p.slug + '"><img src="/demo-images/' + p.image + '" alt=""><span>' + esc(p.name) + '<small>' + esc(p.brand) + ' · ' + p.sku + '</small></span></a></td><td>' + fa(p.price) + '</td><td><div class="quantity"><button class="pb-qty-btn" type="button" data-cart-step="-1" data-id="' + p.id + '" aria-label="کاهش">−</button><input class="qty" type="number" min="1" max="' + p.stock + '" value="' + e.qty + '" data-cart-qty="' + p.id + '" aria-label="تعداد ' + esc(p.name) + '"><button class="pb-qty-btn" type="button" data-cart-step="1" data-id="' + p.id + '" aria-label="افزایش">+</button></div></td><td><b>' + fa(p.price * e.qty) + '</b></td></tr>'; }).join('') + '</tbody></table><p class="demo-inline-note">سبد فقط در همین مرورگر ذخیره می‌شود.</p></div>' + summary(t, false);
  }
  function renderCheckout() {
    var box = $('#checkout-items'); if (!box) return; var t = S.totals();
    if (!t.count) { box.innerHTML = '<div class="pb-note">سبد خالی است. <a href="/shop">انتخاب قطعه</a></div>'; $('#place_order').disabled = true; return; }
    $('#place_order').disabled = false;
    box.innerHTML = '<table class="shop_table woocommerce-checkout-review-order-table"><tbody>' + t.entries.map(function (e) { return '<tr><td>' + esc(e.product.name) + ' × ' + fa(e.qty) + '</td><td>' + fa(e.product.price * e.qty) + '</td></tr>'; }).join('') + '</tbody></table>' + summary(t, true);
  }
  function orderRow(o) { return '<tr><td><a href="/order/' + o.id + '">' + o.id + '</a></td><td>' + new Date(o.date).toLocaleDateString('fa-IR') + '</td><td><span class="pb-status pb-status--processing">در حال پردازش نمونه</span></td><td>' + fa(o.total) + ' تومان</td><td><a class="pb-btn pb-btn--sm pb-btn--ghost" href="/order/' + o.id + '">مشاهده</a></td></tr>'; }
  function renderAccount() {
    if (!$('[data-stat-orders]')) return; var orders = S.state.orders;
    $('[data-stat-orders]').textContent = fa(orders.length); $('[data-stat-favorites]').textContent = fa(S.state.favorites.length); $('[data-stat-spent]').textContent = fa(orders.reduce(function (s, o) { return s + o.total; }, 0));
    $('[data-account-car]').textContent = S.state.car ? S.state.car.brand + ' ' + S.state.car.model + ' · ' + S.state.car.year : 'هنوز انتخاب نشده';
    if (orders.length) $('#account-last-order').innerHTML = '<div class="pb-dash__last-body"><strong>' + orders[0].id + '</strong><span>' + new Date(orders[0].date).toLocaleDateString('fa-IR') + '</span><span class="pb-status pb-status--processing">در حال پردازش نمونه</span><b>' + fa(orders[0].total) + ' تومان</b><a class="pb-btn pb-btn--sm" href="/order/' + orders[0].id + '">جزئیات سفارش</a></div>';
  }
  function renderOrders() { var root = $('#orders-root'); if (!root) return; var orders = S.state.orders; root.innerHTML = orders.length ? '<div class="pb-table-scroll"><table class="shop_table"><thead><tr><th>سفارش</th><th>تاریخ</th><th>وضعیت</th><th>مبلغ</th><th></th></tr></thead><tbody>' + orders.map(orderRow).join('') + '</tbody></table></div>' : empty('هنوز سفارشی ثبت نشده', 'یک سفارش نمایشی بساز تا این بخش را ببینی.', '/shop', 'شروع خرید'); }
  function renderFavorites() { var root = $('#favorites-root'); if (!root) return; var list = S.state.favorites.map(S.product).filter(Boolean); $('[data-fav-label]').textContent = fa(list.length) + ' قطعه'; root.innerHTML = list.length ? list.map(cardHTML).join('') : empty('هنوز قطعه‌ای ذخیره نکرده‌ای', 'روی آیکن قلب کارت محصول بزن.', '/shop', 'دیدن قطعات'); }
  function renderCompare() {
    var root = $('#compare-root'); if (!root) return; var list = S.state.compare.map(S.product).filter(Boolean);
    if (!list.length) { root.innerHTML = empty('فهرست مقایسه خالی است', 'تا ۴ قطعه را با آیکن مقایسه کارت‌ها انتخاب کن.', '/shop', 'انتخاب قطعه'); return; }
    var rows = [['تصویر', function (p) { return '<img class="pb-compare-img" src="/demo-images/' + p.image + '" alt="">'; }], ['قیمت', function (p) { return '<b>' + fa(p.price) + ' تومان</b>'; }], ['برند', function (p) { return esc(p.brand); }], ['شماره فنی', function (p) { return '<code dir="ltr">' + p.sku + '</code>'; }], ['سازگاری نمونه', function (p) { return esc(p.fit.label) + ' · ' + p.fit.years; }], ['موجودی', function (p) { return p.stock ? '<span class="pb-status">موجود نمونه</span>' : '<span class="pb-status pb-status--processing">ناموجود</span>'; }], ['خرید', function (p) { return '<button class="pb-btn pb-btn--sm" data-add="' + p.id + '" ' + (p.stock ? '' : 'disabled') + '>افزودن به سبد</button> <button class="pb-btn pb-btn--sm pb-btn--ghost" data-compare="' + p.id + '">حذف</button>'; }]];
    root.innerHTML = '<div class="pb-compat-table-wrap"><table class="pb-compat-table pb-compare-table"><thead><tr><th>مشخصه</th>' + list.map(function (p) { return '<th><a href="/product/' + p.slug + '">' + esc(p.name) + '</a></th>'; }).join('') + '</tr></thead><tbody>' + rows.map(function (r) { return '<tr><th>' + r[0] + '</th>' + list.map(function (p) { return '<td>' + r[1](p) + '</td>'; }).join('') + '</tr>'; }).join('') + '</tbody></table></div>';
  }
  function renderOrder() {
    var root = $('#order-root'); if (!root) return; var o = S.state.orders.find(function (x) { return x.id === root.dataset.orderId; });
    if (!o) { root.innerHTML = '<div class="pb-order-received__check">' + icon('info') + '</div><h1>سفارش نمونه‌ای پیدا نشد</h1><p>این صفحه فقط سفارش‌های ساخته‌شده در همین مرورگر را نشان می‌دهد.</p><a class="pb-btn" href="/shop">ساخت سفارش نمونه</a>'; return; }
    root.innerHTML = '<div class="pb-order-received__check">' + icon('check') + '</div><span class="pb-eyebrow">سفارش نمایشی ثبت شد</span><h1>ممنون، سفارشت ثبت شد.</h1><p>هیچ پرداخت یا ارسال واقعی انجام نشده است.</p><ul class="order_details"><li>شماره سفارش<strong>' + o.id + '</strong></li><li>تاریخ<strong>' + new Date(o.date).toLocaleDateString('fa-IR') + '</strong></li><li>مبلغ نمونه<strong>' + fa(o.total) + ' تومان</strong></li></ul><div class="pb-order-timeline"><span class="pb-order-timeline__step is-done">ثبت سفارش</span><span class="pb-order-timeline__step is-current">پردازش نمونه</span><span class="pb-order-timeline__step">ارسال</span><span class="pb-order-timeline__step">تحویل</span></div><table class="shop_table"><tbody>' + o.items.map(function (i) { var p = S.product(i.id); return '<tr><td>' + esc(p ? p.name : i.id) + ' × ' + fa(i.qty) + '</td><td>' + fa(i.price * i.qty) + ' تومان</td></tr>'; }).join('') + '</tbody></table><div class="partyab-empty__actions"><a class="pb-btn" href="/orders">سفارش‌های من</a><a class="pb-btn pb-btn--ghost" href="/shop">ادامه خرید</a></div>';
  }
  function cardHTML(p, i) {
    return '<article class="pb-product-card" data-reveal><div class="pb-product-card__media"><a class="pb-product-card__link" href="/product/' + p.slug + '"><img class="pb-product-card__image" src="/demo-images/' + p.image + '" alt="' + esc(p.name) + '"></a><div class="pb-product-card__tools"><button class="pb-product-card__tool" data-favorite="' + p.id + '" aria-label="علاقه‌مندی" aria-pressed="false">' + icon('heart') + '</button><button class="pb-product-card__tool" data-compare="' + p.id + '" aria-label="مقایسه" aria-pressed="false">' + icon('compare') + '</button></div></div><div class="pb-product-card__body"><span class="pb-product-card__brand">' + esc(p.brand) + '</span><h3 class="pb-product-card__title"><a href="/product/' + p.slug + '">' + esc(p.name) + '</a></h3><div class="pb-product-card__footer"><div class="pb-product-card__price"><b>' + fa(p.price) + '<small>تومان</small></b></div><button class="pb-product-card__add" data-add="' + p.id + '" aria-label="افزودن به سبد" ' + (p.stock ? '' : 'disabled') + '>' + icon('cart') + '</button></div></div></article>';
  }
  function openQuick(id) {
    var p = S.product(id); if (!p) return; var dialog = $('#demo-dialog');
    $('.pb-dialog__body', dialog).innerHTML = '<div class="pb-quick"><img src="/demo-images/' + p.image + '" alt="' + esc(p.name) + '"><div><span class="pb-badge orange">' + esc(p.brand) + '</span><h2>' + esc(p.name) + '</h2><p>' + esc(p.description) + '</p><p class="pb-product-card__fit">' + icon('car') + 'مناسب ' + esc(p.fit.label) + '</p><div class="pb-product-summary__price"><b>' + fa(p.price) + '<small> تومان</small></b></div><div class="pb-product-buy"><button class="pb-btn" data-add="' + p.id + '" ' + (p.stock ? '' : 'disabled') + '>' + icon('cart') + 'افزودن به سبد</button><a class="pb-btn pb-btn--ghost" href="/product/' + p.slug + '">جزئیات کامل</a></div></div></div>';
    dialog.showModal(); if (window.PartyabMotion) window.PartyabMotion.reveal(dialog);
  }
  function initCarForm() {
    var form = $('[data-car-form]'); if (!form) return; var brand = $('[data-car-brand]', form), model = $('[data-car-model]', form);
    var models = { 'پراید': ['۱۱۱', '۱۳۱', '۱۳۲', 'صندوق‌دار'], 'پژو': ['۲۰۶', '۲۰۷', '۴۰۵', 'پارس'], 'سمند': ['LX', 'EF7', 'سریر'], 'تیبا': ['صندوق‌دار', '۲', 'هاچ‌بک'] };
    var current = S.state.car;
    function fill(keep) { model.innerHTML = '<option value="">انتخاب کن…</option>' + (models[brand.value] || []).map(function (m) { return '<option ' + (keep === m ? 'selected' : '') + '>' + m + '</option>'; }).join(''); }
    brand.addEventListener('change', function () { fill(''); }); if (current) { brand.value = current.brand; fill(current.model); brand.dispatchEvent(new Event('input', { bubbles: true })); }
    form.addEventListener('submit', function (e) { e.preventDefault(); var car = { brand: brand.value, model: model.value, year: S.faDigits(form.year.value) }; S.setCar(car); (window.PartyabTransition ? window.PartyabTransition.navigate('/shop?car=' + encodeURIComponent(car.brand + ' ' + car.model)) : location.href = '/shop?car=' + encodeURIComponent(car.brand + ' ' + car.model)); });
  }
  function initSearch() {
    var form = $('[data-demo-search]'); if (!form) return; var input = $('input[type=search]', form), box = $('#demo-suggest'), active = -1;
    function close() { box.hidden = true; input.setAttribute('aria-expanded', 'false'); active = -1; }
    function render() {
      var q = input.value.trim().toLowerCase(); if (!q) { close(); return; }
      var list = catalog.filter(function (p) { return (p.name + ' ' + p.sku + ' ' + p.brand + ' ' + p.fit.label).toLowerCase().includes(q); }).slice(0, 6);
      box.innerHTML = list.length ? '<div class="pb-suggest__group">قطعات پیشنهادی</div>' + list.map(function (p) { return '<a class="pb-suggest__item" role="option" id="sg-' + p.id + '" href="/product/' + p.slug + '"><img src="/demo-images/' + p.image + '" alt=""><span class="pb-suggest__text"><strong>' + esc(p.name) + '</strong><small>' + p.sku + '</small></span><span class="pb-suggest__meta"><b class="pb-suggest__price">' + fa(p.price) + '</b><small>تومان</small></span></a>'; }).join('') + '<a class="pb-suggest__all" href="/shop?q=' + encodeURIComponent(input.value.trim()) + '">دیدن همه نتیجه‌ها</a>' : '<div class="pb-suggest__empty">قطعه‌ای پیدا نشد. شماره فنی را بدون خط تیره امتحان کن.</div>';
      box.hidden = false; input.setAttribute('aria-expanded', 'true'); active = -1;
    }
    input.addEventListener('input', render); input.addEventListener('focus', render);
    input.addEventListener('keydown', function (e) { var items = $$('.pb-suggest__item', box); if (e.key === 'Escape') { close(); return; } if (!items.length) return; if (e.key === 'ArrowDown' || e.key === 'ArrowUp') { e.preventDefault(); active = (active + (e.key === 'ArrowDown' ? 1 : -1) + items.length) % items.length; items.forEach(function (el, i) { el.classList.toggle('is-active', i === active); el.setAttribute('aria-selected', String(i === active)); }); input.setAttribute('aria-activedescendant', items[active].id); } else if (e.key === 'Enter' && active >= 0) { e.preventDefault(); items[active].click(); } });
    document.addEventListener('click', function (e) { if (!form.contains(e.target)) close(); });
  }
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-add],[data-favorite],[data-compare],[data-quick],[data-remove],[data-cart-step],[data-dialog-close],[data-compare-clear],[data-tab-jump]'); if (!el) return;
    if (el.dataset.add) { var qty = el.hasAttribute('data-qty-input') ? Math.max(1, parseInt($('.pb-product-buy input.qty')?.value || '1', 10)) : 1; if (S.add(el.dataset.add, qty)) { UI.fly(el); UI.bumpCart(); UI.toast('به سبد نمونه اضافه شد.'); el.classList.add('is-added'); setTimeout(function () { el.classList.remove('is-added'); }, 900); } }
    if (el.dataset.favorite) { var on = S.favorite(el.dataset.favorite); UI.toast(on ? 'به علاقه‌مندی‌ها اضافه شد.' : 'از علاقه‌مندی‌ها حذف شد.'); }
    if (el.dataset.compare) { var added = S.compare(el.dataset.compare); if (added === 'full') UI.toast('حداکثر ۴ قطعه قابل مقایسه است.'); else UI.toast(added ? 'به فهرست مقایسه اضافه شد.' : 'از مقایسه حذف شد.'); }
    if (el.dataset.quick) openQuick(el.dataset.quick);
    if (el.dataset.remove) S.setQty(el.dataset.remove, 0);
    if (el.dataset.cartStep) S.setQty(el.dataset.id, (S.state.cart[el.dataset.id] || 1) + Number(el.dataset.cartStep));
    if (el.hasAttribute('data-dialog-close')) $('#demo-dialog').close();
    if (el.hasAttribute('data-compare-clear')) S.clearCompare();
    if (el.dataset.tabJump) { var tab = $('#tab-' + el.dataset.tabJump); if (tab) { tab.click(); tab.scrollIntoView({ behavior: window.PartyabMotion.reduced() ? 'auto' : 'smooth', block: 'center' }); } }
  });
  document.addEventListener('change', function (e) { if (e.target.dataset.cartQty) S.setQty(e.target.dataset.cartQty, parseInt(e.target.value, 10) || 1); if (e.target.hasAttribute('data-auto-submit')) e.target.form.requestSubmit(); });
  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (form.matches('[data-coupon]')) { e.preventDefault(); UI.toast(S.setCoupon(form.coupon.value) ? 'کد نمونه PARTYAB اعمال شد.' : 'کد تخفیف معتبر نیست.'); }
    if (form.id === 'demo-checkout') { e.preventDefault(); if (!form.reportValidity()) return; var order = S.createOrder(); if (order) (window.PartyabTransition ? window.PartyabTransition.navigate('/order/' + order.id) : location.href = '/order/' + order.id); }
    if (form.id === 'demo-contact') { e.preventDefault(); var notice = $('#contact-result'); notice.hidden = false; notice.className = 'pb-form__notice is-ok'; notice.textContent = 'اطلاعات فرم معتبر است. در این پیش‌نمایش پیام واقعی ارسال نمی‌شود؛ ارسال واقعی در نسخه وردپرس انجام می‌شود.'; UI.toast('اعتبارسنجی فرم انجام شد.'); }
    if (form.id === 'demo-auth') { e.preventDefault(); (window.PartyabTransition ? window.PartyabTransition.navigate('/account') : location.href = '/account'); }
    if (form.id === 'address-form') { e.preventDefault(); var data = Object.fromEntries(new FormData(form)); S.setAddress(data); var saved = $('#address-saved'); saved.hidden = false; saved.textContent = 'آدرس نمونه برای ' + esc(data.name) + ' در ' + esc(data.city) + ' ذخیره شد.'; UI.toast('آدرس نمونه ذخیره شد.'); }
  });
  $('#demo-dialog')?.addEventListener('click', function (e) { if (e.target === this) this.close(); });
  document.addEventListener('demo:change', sync);
  fetch('/preview/catalog.json').then(function (r) { return r.json(); }).then(function (data) { catalog = data; S.init(data); initCarForm(); initSearch(); var address = S.state.address; if (address && $('#address-form')) { Object.keys(address).forEach(function (k) { var field = $('#address-form [name="' + k + '"]'); if (field) field.value = address[k]; }); } sync(); }).catch(function () { UI.toast('بارگذاری داده‌های پیش‌نمایش ناموفق بود.'); });
})();
