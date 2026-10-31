/* =========================================================
   PartYab — UI logic (demo)
   ========================================================= */

/* ---------------- Icons ---------------- */
const ICONS = {
  search: '<circle cx="11" cy="11" r="7"/><path d="m20.2 20.2-3.8-3.8"/>',
  user: '<circle cx="12" cy="8" r="3.6"/><path d="M4.8 20.4c0-3.6 3.2-5.9 7.2-5.9s7.2 2.3 7.2 5.9"/>',
  cart: '<path d="M3 4h2.1l2.3 10.6a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 1.95-1.55L20.8 8H6"/><circle cx="9.5" cy="20" r="1.3"/><circle cx="17.5" cy="20" r="1.3"/>',
  phone: '<path d="M6.2 3h2.9l1.4 3.7-2 1.4a12.4 12.4 0 0 0 6.4 6.4l1.4-2L20 13.9v2.9a2 2 0 0 1-2.2 2A17 17 0 0 1 4.2 5.2 2 2 0 0 1 6.2 3Z"/>',
  clock: '<circle cx="12" cy="12" r="8.4"/><path d="M12 7.6V12l3 1.8"/>',
  truck: '<path d="M2.8 7h9.4v9.4H2.8z"/><path d="M12.2 10.6h3.6l3.4 3.2v2.6h-7"/><circle cx="6.3" cy="18.4" r="1.5"/><circle cx="16.6" cy="18.4" r="1.5"/>',
  shield: '<path d="M12 3.2 18.6 6v5.2c0 4.1-2.8 7.2-6.6 9-3.8-1.8-6.6-4.9-6.6-9V6L12 3.2Z"/><path d="m9.2 12 2 2 3.6-3.8"/>',
  refresh: '<path d="M20 12a8 8 0 1 1-2.4-5.7"/><path d="M20 4.4v4.2h-4.2"/>',
  headset: '<path d="M4.4 13.2v-1a7.6 7.6 0 0 1 15.2 0v1"/><path d="M4.4 13.2h2.8v5.6H6A1.6 1.6 0 0 1 4.4 17.2Z"/><path d="M19.6 13.2h-2.8v5.6h1.2a1.6 1.6 0 0 0 1.6-1.6Z"/>',
  check: '<path d="m5 12.6 4.2 4.2L19 6.4"/>',
  x: '<path d="M6 6l12 12M18 6 6 18"/>',
  'chevron-down': '<path d="m6 9.5 6 6 6-6"/>',
  'chevron-left': '<path d="m14 6-6 6 6 6"/>',
  'chevron-right': '<path d="M10 6l6 6-6 6"/>',
  'arrow-left': '<path d="M19 12H5"/><path d="m11 6-6 6 6 6"/>',
  car: '<path d="M4.4 16.4h15.2"/><path d="M4 16.4v-3.6l2-4.8h12l2 4.8v3.6"/><path d="M6.2 12.4h11.6"/><circle cx="7.6" cy="16.6" r="1.5"/><circle cx="16.4" cy="16.6" r="1.5"/>',
  filter: '<path d="M3.6 5.4h16.8l-6.4 7.2v5.6l-4 2.2v-7.8L3.6 5.4Z"/>',
  spark: '<path d="M13 2.6 5.4 13.8h5.4l-.8 7.6 7.8-11.4h-5.4l.6-7.4Z"/>',
  brake: '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="2.6"/><path d="M12 4.6v1.8M12 17.6v1.8M4.6 12h1.8M17.6 12h1.8M7.2 7.2l1.3 1.3M15.5 15.5l1.3 1.3M16.8 7.2l-1.3 1.3M8.5 15.5l-1.3 1.3"/>',
  oil: '<path d="M12 3s5.8 6.2 5.8 9.6A5.8 5.8 0 0 1 12 18.4a5.8 5.8 0 0 1-5.8-5.8C6.2 9.2 12 3 12 3Z"/><path d="M9.5 12.6a2.5 2.5 0 0 0 2.5 2.5"/>',
  shock: '<path d="M12 3.2v3"/><path d="M7.8 6.4h8.4M7.8 9.6h8.4M7.8 12.8h8.4M7.8 16h8.4"/><path d="M12 16v4.8"/>',
  engine: '<path d="M7.4 9.2V6.8h5.4v2.4"/><path d="M4.8 9.2h9.8v8H4.8z"/><path d="M14.6 11.6h2.6l2.2 2v3.6h-4.8"/><path d="M7.6 17.2v2M11.8 17.2v2M2.4 12.4h2.4"/>',
  light: '<path d="M3.6 8.4h3.6l8.4-4v15.2l-8.4-4H3.6z"/><path d="M18.4 9.2 21 7.4M18.4 14.8 21 16.6M17.6 12h3.6"/>',
  tire: '<circle cx="12" cy="12" r="8.4"/><circle cx="12" cy="12" r="3.2"/><path d="M12 3.6v5.2M12 15.2v5.2M3.6 12h5.2M15.2 12h5.2"/>',
  accessory: '<path d="M14.5 6.2a3.6 3.6 0 0 0 4.6 4.6l-8.2 8.2a2.6 2.6 0 0 1-3.7-3.7l8.2-8.2Z"/><path d="M9.6 8.6 6.4 5.4a2 2 0 0 0-2.8 2.8l3.2 3.2"/>',
  sliders: '<path d="M4 7h9M17.5 7H20M4 17h4M12 17h8"/><circle cx="15" cy="7" r="2.2"/><circle cx="9.5" cy="17" r="2.2"/>',
  plus: '<path d="M12 5.5v13M5.5 12h13"/>',
  minus: '<path d="M5.5 12h13"/>',
  star: '<path d="m12 2.6 2.9 5.9 6.5.9-4.7 4.6 1.1 6.4L12 17.4l-5.8 3 1.1-6.4L2.6 9.4l6.5-.9L12 2.6Z"/>',
  info: '<circle cx="12" cy="12" r="8.6"/><path d="M12 11v5.2M12 7.9v.2"/>',
  alert: '<path d="M12 3.6 21 19.4H3L12 3.6Z"/><path d="M12 9.6v4M12 16.6v.2"/>',
  box: '<path d="m12 3.2 8 4.2v9.2l-8 4.2-8-4.2V7.4l8-4.2Z"/><path d="m4 7.4 8 4.2 8-4.2M12 11.6v9.2"/>',
  wrench: '<path d="M20 5.6a4.6 4.6 0 0 1-6.1 6.1L7.3 18.3a2.3 2.3 0 1 1-3.2-3.2l6.6-6.6A4.6 4.6 0 0 1 17.4 2.4l-3 3 1.2 2.8 2.8 1.2 3-3Z"/>',
  instagram: '<rect x="3.4" y="3.4" width="17.2" height="17.2" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r=".9" fill="currentColor" stroke="none"/>',
  telegram: '<path d="m20.6 4.2-2.8 15.2-5.6-3.8-2.8 2.7.2-4.3 8.6-7.5-10 6.6-4.2-1.6 16.6-7.3Z"/>',
  whatsapp: '<path d="M4.2 20.4l1.2-4.2a7.8 7.8 0 1 1 3 3l-4.2 1.2Z"/><path d="M9 8.6c1 3 2.6 4.6 5.4 5.4"/>',
  'map-pin': '<path d="M12 21s6.4-5.6 6.4-10.4A6.4 6.4 0 0 0 5.6 10.6C5.6 15.4 12 21 12 21Z"/><circle cx="12" cy="10.4" r="2.4"/>',
  mail: '<rect x="3" y="5.4" width="18" height="13.2" rx="2.4"/><path d="m3.8 7 8.2 6 8.2-6"/>',
  file: '<path d="M13.6 3.4H6.8a2 2 0 0 0-2 2v13.2a2 2 0 0 0 2 2h10.4a2 2 0 0 0 2-2V8.8l-5.6-5.4Z"/><path d="M13.4 3.6v5.4h5.4"/>',
  grid: '<rect x="4" y="4" width="6.4" height="6.4" rx="1.6"/><rect x="13.6" y="4" width="6.4" height="6.4" rx="1.6"/><rect x="4" y="13.6" width="6.4" height="6.4" rx="1.6"/><rect x="13.6" y="13.6" width="6.4" height="6.4" rx="1.6"/>',
  zoom: '<circle cx="11" cy="11" r="6.6"/><path d="m16 16 4.4 4.4M11 8.6v4.8M8.6 11h4.8"/>',
  menu: '<path d="M4 7h16M4 12h16M4 17h16"/>'
};

function icon(name, cls = '') {
  return `<svg class="icon ${cls}" viewBox="0 0 24 24" aria-hidden="true">${ICONS[name] || ''}</svg>`;
}

/* جایگزینی آیکون‌های static مارک‌آپ با SVG واقعی */
function hydrateIcons(root = document) {
  $$('[data-icon]', root).forEach(el => {
    const name = el.dataset.icon;
    const extra = (el.getAttribute('class') || '').replace(/\bicon\b/, '').trim();
    const tmp = document.createElement('div');
    tmp.innerHTML = icon(name, extra);
    const svg = tmp.firstElementChild;
    if (svg) el.replaceWith(svg);
  });
}

/* ---------------- Utils ---------------- */
const $ = (s, r = document) => r.querySelector(s);
const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));
const FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';
const fa = n => String(n).replace(/\d/g, d => FA_DIGITS[+d]);
const money = n => fa(Number(n).toLocaleString('en-US')).replace(/,/g, '٬');
const esc = s => String(s).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

const KEY_V = 'py_vehicle_v1';
const KEY_CART = 'py_cart_v1';

const getVehicle = () => {
  try { return JSON.parse(localStorage.getItem(KEY_V)); } catch { return null; }
};
const setVehicle = v => {
  if (v) localStorage.setItem(KEY_V, JSON.stringify(v));
  else localStorage.removeItem(KEY_V);
  document.dispatchEvent(new CustomEvent('py:vehicle', { detail: v }));
};
const cartCount = () => Number(localStorage.getItem(KEY_CART) || 0);
const addToCart = (p, qty = 1) => {
  localStorage.setItem(KEY_CART, cartCount() + qty);
  const el = $('.cart-count');
  if (el) { el.textContent = fa(cartCount()); el.style.animation = 'none'; void el.offsetWidth; el.style.animation = 'pop .35s ease'; }
  toast(`«${p.name}» به سبد خرید اضافه شد`, 'success');
};

const getModel = id => {
  for (const m of MAKES) {
    const mdl = m.models.find(x => x.id === id);
    if (mdl) return { make: m, model: mdl };
  }
  return null;
};

const fitStatus = (p, v) => {
  if (!v) return 'ask';
  if (!p.fitmentKnown) return 'unknown';
  const f = p.fits.find(x => x.model === v.modelId);
  if (!f) return 'no';
  if (v.year < f.from || v.year > f.to) return 'no';
  return 'fit';
};

const FIT_LABEL = {
  fit: { short: 'سازگار', long: 'با خودروی شما سازگار است', icon: 'check', cls: 'fit' },
  no: { short: 'ناسازگار', long: 'با خودروی شما سازگار نیست', icon: 'x', cls: 'no' },
  unknown: { short: 'ثبت نشده', long: 'سازگاری برای این خودرو ثبت نشده', icon: 'alert', cls: 'unknown' },
  ask: { short: 'انتخاب خودرو', long: 'خودرو را انتخاب کنید', icon: 'car', cls: 'ask' }
};

const fitBadge = (status, long = false) => {
  const l = FIT_LABEL[status];
  return `<span class="fit-badge fit-badge--${l.cls}">${icon(l.icon)}${long ? l.long : l.short}</span>`;
};

const vehicleTitle = v => v ? `${v.modelName} · ${fa(v.year)}` : '';

/* ---------------- Toast ---------------- */
let toastTimer;
function toast(msg, type = 'info') {
  let el = $('#toast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'toast';
    el.className = 'toast';
    document.body.appendChild(el);
  }
  el.className = `toast ${type === 'success' ? 'is-success' : ''}`;
  el.innerHTML = `${icon(type === 'success' ? 'check' : 'info')}<span>${msg}</span>`;
  requestAnimationFrame(() => el.classList.add('is-show'));
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove('is-show'), 3200);
}

/* ---------------- Product card ---------------- */
function productCard(p) {
  const v = getVehicle();
  const st = fitStatus(p, v);
  const off = p.oldPrice ? Math.round((1 - p.price / p.oldPrice) * 100) : 0;
  return `
  <article class="p-card ${st === 'no' ? 'p-card--dim' : ''}" data-product="${p.id}">
    <a class="p-media" href="product.html?id=${p.id}" aria-label="${esc(p.name)}">
      <img src="assets/img/${p.img}" alt="${esc(p.name)}" loading="lazy">
      ${fitBadge(st)}
      ${off ? `<span class="off-badge">${fa(off)}٪</span>` : ''}
    </a>
    <div class="p-body">
      <div class="p-brand-row">
        <span class="p-brand">${p.brand}</span>
        ${p.verified ? `<span class="p-verified">${icon('check')} تأییدشده</span>` : ''}
      </div>
      <h3 class="p-title"><a href="product.html?id=${p.id}">${p.name}</a></h3>
      <div class="p-meta">کد فنی: <b>${p.oem}</b></div>
      <div class="p-stock ${p.stock < 6 ? 'p-stock--low' : ''}">${p.stock < 6 ? `${fa(p.stock)} عدد باقی مانده` : 'موجود در انبار'}</div>
      <div class="p-price-row">
        <div class="p-price">
          ${p.oldPrice ? `<span class="old">${money(p.oldPrice)}</span>` : ''}
          <strong>${money(p.price)} <small>تومان</small></strong>
        </div>
        <button class="p-add" data-add="${p.id}" aria-label="افزودن به سبد خرید">${icon('plus')}</button>
      </div>
    </div>
  </article>`;
}

/* ---------------- Header / footer ---------------- */
function headerHTML() {
  const page = document.body.dataset.page;
  return `
  <div class="topbar">
    <div class="container topbar-inner">
      <div class="topbar-group">
        <a class="topbar-item" href="tel:02191002233">${icon('phone', 'icon--sm')} ۰۲۱–۹۱۰۰۲۲۳۳</a>
        <span class="topbar-sep"></span>
        <span class="topbar-item">${icon('clock', 'icon--sm')} شنبه تا پنجشنبه، ۹ تا ۱۸</span>
        <span class="topbar-sep"></span>
        <a class="topbar-item" href="#">پیگیری سفارش</a>
      </div>
      <div class="topbar-group">
        <span class="topbar-badge">${icon('truck', 'icon--sm')} ارسال رایگان سفارش‌های بالای ۳ میلیون تومان</span>
      </div>
    </div>
  </div>

  <header class="site-header">
    <div class="container header-main">
      <a class="logo" href="index.html" aria-label="پارت‌یاب، صفحه اصلی">
        <span class="logo-mark">
          <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="10.4" cy="10.4" r="6.6" stroke="#F59E0B" stroke-width="2"/>
            <path d="M15.4 15.4 21 21" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
            <circle cx="10.4" cy="10.4" r="2.1" stroke="#fff" stroke-width="1.5"/>
            <path d="M10.4 5.5v1.5M10.4 13.8v1.5M5.5 10.4H7M13.8 10.4h1.5" stroke="#fff" stroke-width="1.2" stroke-linecap="round"/>
          </svg>
        </span>
        <span class="logo-text"><strong>پارت‌یاب</strong><span>PARTYAB.COM</span></span>
      </a>

      <div id="vehicleSlot" class="vehicle-slot"></div>

      <div class="header-search">
        <form role="search" data-search-form>
          ${icon('search', 'icon-search')}
          <input type="search" name="q" placeholder="نام قطعه، برند یا کد فنی را وارد کنید" aria-label="جست‌وجو">
          <button class="search-submit" type="submit" aria-label="جست‌وجو">${icon('search')}</button>
        </form>
      </div>

      <div class="header-actions">
        <button class="nav-toggle icon-btn" data-open-nav aria-label="منو">${icon('menu')}</button>
        <a class="icon-btn" href="#" aria-label="حساب کاربری">${icon('user')}</a>
        <button class="cart-btn" data-cart aria-label="سبد خرید">
          ${icon('cart')}<span class="cart-label">سبد خرید</span>
          <span class="cart-count">${fa(cartCount())}</span>
        </button>
      </div>
    </div>

    <div class="subnav">
      <div class="container subnav-inner">
        <a href="index.html" class="${page === 'home' ? 'is-active' : ''}">${icon('grid')} خانه</a>
        <a href="shop.html" class="${page === 'shop' || page === 'product' ? 'is-active' : ''}">${icon('box')} فروشگاه قطعات</a>
        <a href="#" data-open-vehicle>${icon('car')} خودروی من</a>
        <a href="shop.html?cat=filter">${icon('filter')} فیلترها و روغن</a>
        <a href="shop.html?cat=brake">${icon('brake')} ترمز</a>
        <a href="shop.html?cat=suspension">${icon('shock')} جلوبندی</a>
        <a href="index.html#guides">${icon('file')} راهنمای خرید</a>
        <a href="#" data-open-vehicle class="nav-pill">${icon('spark')} انتخاب خودرو</a>
      </div>
    </div>
  </header>`;
}

function overlaysHTML() {
  return `
  <div class="drawer" id="navDrawer" aria-hidden="true">
    <div class="drawer-backdrop" data-close-nav></div>
    <aside class="drawer-panel" role="dialog" aria-label="منوی اصلی">
      <div class="drawer-head">
        <b>منوی پارت‌یاب</b>
        <button class="modal-close" data-close-nav aria-label="بستن">${icon('x')}</button>
      </div>
      <div class="drawer-body">
        <a class="drawer-link" href="index.html">${icon('grid')} خانه</a>
        <a class="drawer-link" href="shop.html">${icon('box')} فروشگاه قطعات</a>
        <a class="drawer-link" href="#" data-open-vehicle>${icon('car')} انتخاب خودرو</a>
        <a class="drawer-link" href="shop.html?cat=brake">${icon('brake')} ترمز و ABS</a>
        <a class="drawer-link" href="shop.html?cat=filter">${icon('filter')} فیلتر و روغن</a>
        <a class="drawer-link" href="shop.html?cat=suspension">${icon('shock')} جلوبندی و تعلیق</a>
        <a class="drawer-link" href="shop.html?cat=electrical">${icon('light')} برق و روشنایی</a>
        <a class="drawer-link" href="index.html#guides">${icon('file')} راهنمای خرید</a>
        <a class="drawer-link" href="#">${icon('user')} حساب کاربری</a>
      </div>
      <div class="drawer-foot">
        <a class="btn btn-amber btn-block" href="tel:02191002233">${icon('phone')} تماس با پشتیبانی</a>
      </div>
    </aside>
  </div>

  <div class="modal" id="vehicleModal" aria-hidden="true">
    <div class="modal-backdrop" data-close-vehicle-modal></div>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
      <div class="modal-head">
        <div>
          <h3 id="vmTitle">خودروی خود را انتخاب کنید</h3>
          <p>فقط قطعاتی نمایش داده می‌شود که فروشگاه سازگاری آن‌ها را ثبت کرده است.</p>
        </div>
        <button class="modal-close" data-close-vehicle-modal aria-label="بستن">${icon('x')}</button>
      </div>
      <div class="modal-body">
        <div class="wizard-step" id="wsMake">
          <span class="ws-num">۱</span>
          <div class="field">
            <label for="vwMake">سازنده خودرو</label>
            <div class="select-wrap">
              <select id="vwMake"><option value="">انتخاب کنید…</option>${MAKES.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}</select>
            </div>
          </div>
        </div>
        <div class="wizard-step" id="wsModel">
          <span class="ws-num">۲</span>
          <div class="field">
            <label for="vwModel">مدل</label>
            <div class="select-wrap">
              <select id="vwModel" disabled><option value="">ابتدا سازنده را انتخاب کنید</option></select>
            </div>
          </div>
        </div>
        <div class="wizard-step" id="wsYear">
          <span class="ws-num">۳</span>
          <div class="field">
            <label for="vwYear">سال ساخت</label>
            <div class="select-wrap">
              <select id="vwYear" disabled><option value="">ابتدا مدل را انتخاب کنید</option></select>
            </div>
          </div>
        </div>
        <div class="wizard-step" id="wsEngine">
          <span class="ws-num">۴</span>
          <div class="field">
            <label for="vwEngine">موتور / تیپ <span style="color:var(--muted-2);font-weight:400">(اختیاری)</span></label>
            <div class="select-wrap">
              <select id="vwEngine" disabled><option value="">در صورت نیاز انتخاب کنید</option></select>
            </div>
          </div>
        </div>

        <div class="modal-foot">
          <button class="btn btn-amber" data-apply-vehicle>${icon('check')} تأیید و مشاهده قطعات</button>
          <button class="btn btn-ghost" data-close-vehicle-modal>انصراف</button>
        </div>

        <div class="popular-makes">
          <span>انتخاب سریع سازنده‌های پرطرفدار:</span>
          <div class="row">
            ${MAKES.map(m => `<button class="make-pill" data-make="${m.id}">${m.name}</button>`).join('')}
          </div>
        </div>
      </div>
    </div>
  </div>`;
}

function footerHTML() {
  return `
  <footer class="site-footer">
    <div class="container footer-top">
      <div class="footer-brand">
        <a class="logo" href="index.html">
          <span class="logo-mark">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <circle cx="10.4" cy="10.4" r="6.6" stroke="#F59E0B" stroke-width="2"/>
              <path d="M15.4 15.4 21 21" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
              <circle cx="10.4" cy="10.4" r="2.1" stroke="#fff" stroke-width="1.5"/>
            </svg>
          </span>
          <span class="logo-text"><strong>پارت‌یاب</strong><span>قطعهٔ درست، برای خودروی درست</span></span>
        </a>
        <p>پارت‌یاب یک فروشگاه نمونه برای نمایش تجربهٔ خرید قطعهٔ سازگار با خودرو است؛ از انتخاب خودرو تا ورود گروهی اطلاعات سازگاری توسط فروشنده.</p>
        <div class="footer-social">
          <a href="#" aria-label="اینستاگرام">${icon('instagram')}</a>
          <a href="#" aria-label="تلگرام">${icon('telegram')}</a>
          <a href="#" aria-label="واتساپ">${icon('whatsapp')}</a>
        </div>
      </div>
      <div class="footer-col">
        <b>دسته‌بندی‌ها</b>
        <a href="shop.html?cat=engine">موتور و انتقال قدرت</a>
        <a href="shop.html?cat=filter">فیلتر و روغن</a>
        <a href="shop.html?cat=brake">ترمز و ABS</a>
        <a href="shop.html?cat=suspension">جلوبندی و تعلیق</a>
        <a href="shop.html?cat=electrical">برق و روشنایی</a>
      </div>
      <div class="footer-col">
        <b>راهنما</b>
        <a href="index.html#guides">انتخاب خودرو</a>
        <a href="index.html#guides">کد فنی یا OEM چیست؟</a>
        <a href="#">شرایط ارسال</a>
        <a href="#">بازگشت کالا</a>
        <a href="#">پرسش‌های متداول</a>
      </div>
      <div class="footer-col footer-col--hide">
        <b>پارت‌یاب</b>
        <a href="#">دربارهٔ ما</a>
        <a href="#">تماس با ما</a>
        <a href="#">قوانین و مقررات</a>
        <a href="#">حریم خصوصی</a>
        <a href="#">همکاری با ما</a>
      </div>
      <div class="footer-col">
        <b>ارتباط با ما</b>
        <ul class="footer-contact">
          <li>${icon('map-pin')}<span>تهران، خیابان ولیعصر، پلاک ۱۲۴۵، طبقهٔ سوم</span></li>
          <li>${icon('phone')}<span>۰۲۱–۹۱۰۰۲۲۳۳</span></li>
          <li>${icon('mail')}<span>support@partYab.ir</span></li>
          <li>${icon('clock')}<span>شنبه تا پنجشنبه، ۹ تا ۱۸</span></li>
        </ul>
      </div>
    </div>
    <div class="footer-note">${icon('info')} این سایت یک نمونهٔ طراحی است؛ محصولات، قیمت‌ها و اطلاعات سازگاری نمایشی هستند و برای خرید واقعی قابل اتکا نیستند.</div>
    <div class="container footer-bottom">
      <span>© ۱۴۰۵ پارت‌یاب — تمام حقوق محفوظ است.</span>
      <div class="footer-pay"><span>پرداخت آنلاین</span><span>درگاه بانکی</span><span>ارسال سراسری</span></div>
    </div>
  </footer>`;
}

/* ---------------- Vehicle UI ---------------- */
function vehicleTriggerHTML() {
  const v = getVehicle();
  if (v) {
    return `<div class="vehicle-trigger is-set" role="button" tabindex="0" data-open-vehicle aria-label="تغییر خودرو">
      ${icon('car')}
      <span class="vt-text"><small>خودروی من</small><b>${esc(v.modelName)} · ${fa(v.year)}</b></span>
      <span class="vt-clear" role="button" tabindex="0" data-clear-vehicle aria-label="حذف خودرو">${icon('x')}</span>
    </div>`;
  }
  return `<button class="vehicle-trigger" data-open-vehicle>
    ${icon('car')}
    <span class="vt-text"><small>خودرویتان را انتخاب کنید</small><b>انتخاب خودرو</b></span>
  </button>`;
}

function refreshVehicleUI() {
  const slot = $('#vehicleSlot');
  if (slot) slot.innerHTML = vehicleTriggerHTML();
  $$('.vchip-slot').forEach(el => {
    const v = getVehicle();
    el.innerHTML = v ? `<span class="vchip">${icon('car')} ${esc(v.modelName)} <small>· ${fa(v.year)}</small><button data-clear-vehicle aria-label="حذف خودرو">${icon('x')}</button></span>` : '';
  });
}

/* modal selects */
function fillModalMakes() {
  const makeSel = $('#vwMake');
  if (!makeSel) return;
  makeSel.innerHTML = '<option value="">انتخاب کنید…</option>' + MAKES.map(m => `<option value="${m.id}">${m.name}</option>`).join('');
}
function modalSetMake(makeId, keep = false) {
  const make = MAKES.find(m => m.id === makeId);
  const modelSel = $('#vwModel');
  const yearSel = $('#vwYear');
  const engSel = $('#vwEngine');
  if (!make) {
    modelSel.innerHTML = '<option value="">ابتدا سازنده را انتخاب کنید</option>';
    modelSel.disabled = true;
    yearSel.innerHTML = '<option value="">ابتدا مدل را انتخاب کنید</option>';
    yearSel.disabled = true;
    engSel.innerHTML = '<option value="">در صورت نیاز انتخاب کنید</option>';
    engSel.disabled = true;
    $('#wsModel').classList.remove('is-done');
    $('#wsYear').classList.remove('is-done');
    $('#wsEngine').classList.remove('is-done');
    return;
  }
  modelSel.innerHTML = '<option value="">انتخاب کنید…</option>' + make.models.map(m => `<option value="${m.id}">${m.name}</option>`).join('');
  modelSel.disabled = false;
  yearSel.innerHTML = '<option value="">ابتدا مدل را انتخاب کنید</option>';
  yearSel.disabled = true;
  engSel.innerHTML = '<option value="">در صورت نیاز انتخاب کنید</option>';
  engSel.disabled = true;
  $('#wsModel').classList.add('is-done');
  $('#wsYear').classList.remove('is-done');
  $('#wsEngine').classList.remove('is-done');
}
function modalSetModel(modelId) {
  const found = getModel(modelId);
  const yearSel = $('#vwYear');
  const engSel = $('#vwEngine');
  if (!found) {
    yearSel.innerHTML = '<option value="">ابتدا مدل را انتخاب کنید</option>';
    yearSel.disabled = true;
    return;
  }
  const [from, to] = found.model.years;
  const years = [];
  for (let y = to; y >= from; y--) years.push(y);
  yearSel.innerHTML = '<option value="">انتخاب کنید…</option>' + years.map(y => `<option value="${y}">${fa(y)}</option>`).join('');
  yearSel.disabled = false;
  engSel.innerHTML = '<option value="">در صورت نیاز انتخاب کنید</option>' +
    found.model.engines.map(e => `<option value="${e}">${e}</option>`).join('');
  engSel.disabled = false;
  $('#wsYear').classList.add('is-done');
  $('#wsEngine').classList.add('is-done');
}

function openVehicleModal() {
  const v = getVehicle();
  fillModalMakes();
  $('#vwMake').value = v ? v.makeId : '';
  modalSetMake(v ? v.makeId : '');
  if (v) {
    $('#vwModel').value = v.modelId;
    modalSetModel(v.modelId);
    $('#vwYear').value = v.year;
    if (v.engine) $('#vwEngine').value = v.engine;
  }
  $('#vehicleModal').classList.add('is-open');
  document.body.style.overflow = 'hidden';
}
function closeVehicleModal() {
  $('#vehicleModal').classList.remove('is-open');
  document.body.style.overflow = '';
}

function applyVehicleFromModal() {
  const makeId = $('#vwMake').value;
  const modelId = $('#vwModel').value;
  const year = Number($('#vwYear').value);
  const engine = $('#vwEngine').value;
  if (!makeId || !modelId || !year) {
    toast('برای ادامه، سازنده، مدل و سال ساخت را انتخاب کنید');
    return;
  }
  const found = getModel(modelId);
  setVehicle({
    makeId, makeName: found.make.name,
    modelId, modelName: found.model.name,
    year, engine
  });
  closeVehicleModal();
  toast(`خودروی شما ثبت شد: ${found.model.name} · ${fa(year)}`, 'success');
}

/* ---------------- Header wiring ---------------- */
function initHeader() {
  const h = $('#site-header');
  const f = $('#site-footer');
  if (h) h.innerHTML = headerHTML();
  if (f) f.innerHTML = footerHTML();
  let ov = $('#site-overlays');
  if (!ov) {
    ov = document.createElement('div');
    ov.id = 'site-overlays';
    document.body.appendChild(ov);
  }
  ov.innerHTML = overlaysHTML();
  refreshVehicleUI();

  document.addEventListener('click', e => {
    const clear = e.target.closest('[data-clear-vehicle]');
    if (clear) {
      e.stopPropagation();
      e.preventDefault();
      setVehicle(null);
      refreshVehicleUI();
      toast('خودروی انتخابی حذف شد');
      return;
    }
    if (e.target.closest('[data-open-vehicle]')) {
      e.preventDefault();
      openVehicleModal();
      return;
    }
    if (e.target.closest('[data-close-vehicle-modal]')) {
      e.preventDefault();
      closeVehicleModal();
      return;
    }
    if (e.target.closest('[data-make]')) {
      const id = e.target.closest('[data-make]').dataset.make;
      $('#vwMake').value = id;
      modalSetMake(id);
      return;
    }
    if (e.target.closest('[data-apply-vehicle]')) {
      applyVehicleFromModal();
      return;
    }
    if (e.target.closest('[data-open-nav]')) {
      $('#navDrawer').classList.add('is-open');
      document.body.style.overflow = 'hidden';
      return;
    }
    if (e.target.closest('[data-close-nav]')) {
      $('#navDrawer').classList.remove('is-open');
      document.body.style.overflow = '';
      return;
    }
    const add = e.target.closest('[data-add]');
    if (add) {
      const p = PRODUCTS.find(x => x.id === add.dataset.add);
      if (p) addToCart(p);
      return;
    }
    if (e.target.closest('[data-cart]')) {
      toast(`سبد خرید شما ${fa(cartCount())} قلم کالا دارد`, 'success');
    }
  });

  document.addEventListener('change', e => {
    if (e.target.id === 'vwMake') { modalSetMake(e.target.value); }
    if (e.target.id === 'vwModel') { modalSetModel(e.target.value); }
  });

  document.addEventListener('keydown', e => {
    if ((e.key === 'Enter' || e.key === ' ') && e.target.closest('[data-open-vehicle]')) {
      e.preventDefault();
      openVehicleModal();
      return;
    }
    if ((e.key === 'Enter' || e.key === ' ') && e.target.closest('[data-clear-vehicle]')) {
      e.preventDefault();
      setVehicle(null);
      refreshVehicleUI();
      toast('خودروی انتخابی حذف شد');
      return;
    }
    if (e.key === 'Escape') {
      closeVehicleModal();
      const d = $('#navDrawer');
      if (d) d.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  });

  const form = $('[data-search-form]');
  if (form) {
    form.addEventListener('submit', e => {
      e.preventDefault();
      const q = form.querySelector('input[name="q"]').value.trim();
      location.href = q ? `shop.html?q=${encodeURIComponent(q)}` : 'shop.html';
    });
  }

  document.addEventListener('py:vehicle', () => {
    refreshVehicleUI();
    if (document.body.dataset.page === 'shop') renderShop(true);
    if (document.body.dataset.page === 'product') renderPDP();
  });
}

/* ---------------- Home page ---------------- */
function initHero() {
  const makeSel = $('#heroMake');
  if (!makeSel) return;
  const modelSel = $('#heroModel');
  const yearSel = $('#heroYear');

  makeSel.innerHTML = '<option value="">سازنده</option>' + MAKES.map(m => `<option value="${m.id}">${m.name}</option>`).join('');

  const resetModel = () => {
    modelSel.innerHTML = '<option value="">مدل</option>';
    modelSel.disabled = true;
    yearSel.innerHTML = '<option value="">سال ساخت</option>';
    yearSel.disabled = true;
  };
  const resetYear = () => {
    yearSel.innerHTML = '<option value="">سال ساخت</option>';
    yearSel.disabled = true;
  };

  makeSel.addEventListener('change', () => {
    const make = MAKES.find(m => m.id === makeSel.value);
    if (!make) { resetModel(); return; }
    modelSel.innerHTML = '<option value="">مدل</option>' + make.models.map(m => `<option value="${m.id}">${m.name}</option>`).join('');
    modelSel.disabled = false;
    resetYear();
  });

  modelSel.addEventListener('change', () => {
    const found = getModel(modelSel.value);
    if (!found) { resetYear(); return; }
    const [from, to] = found.model.years;
    const years = [];
    for (let y = to; y >= from; y--) years.push(y);
    yearSel.innerHTML = '<option value="">سال ساخت</option>' + years.map(y => `<option value="${y}">${fa(y)}</option>`).join('');
    yearSel.disabled = false;
  });

  const go = () => {
    const make = MAKES.find(m => m.id === makeSel.value);
    const found = getModel(modelSel.value);
    const year = Number(yearSel.value);
    if (!make || !found) {
      toast('لطفاً سازنده و مدل خودرو را انتخاب کنید');
      if (!make) makeSel.focus(); else modelSel.focus();
      return;
    }
    if (!year) {
      toast('سال ساخت خودرو را انتخاب کنید');
      yearSel.focus();
      return;
    }
    setVehicle({ makeId: make.id, makeName: make.name, modelId: found.model.id, modelName: found.model.name, year, engine: '' });
    location.href = 'shop.html';
  };

  const btn = $('[data-hero-go]');
  if (btn) btn.addEventListener('click', go);
  const oem = $('[data-hero-oem]');
  if (oem) oem.addEventListener('click', () => {
    const input = $('[data-search-form] input');
    if (input) { input.focus(); input.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
  });
}

function initHome() {
  const grid = $('#featuredGrid');
  if (grid) {
    const list = [...PRODUCTS].filter(p => p.featured).concat(PRODUCTS.filter(p => !p.featured)).slice(0, 8);
    grid.innerHTML = list.map(productCard).join('');
  }
  const catGrid = $('#catGrid');
  if (catGrid) {
    catGrid.innerHTML = CATEGORIES.map(c => {
      const count = PRODUCTS.filter(p => p.cat === c.id).length;
      return `<a class="cat-card" href="shop.html?cat=${c.id}">
        <span class="cat-icon">${icon(c.icon)}</span>
        <b>${c.name}</b>
        <span>${fa(count * 37)} قطعه</span>
        <span class="cat-arrow">${icon('chevron-left')}</span>
      </a>`;
    }).join('');
  }
  const brands = $('#brandStrip');
  if (brands) {
    brands.innerHTML = BRANDS.map(b => `<div class="brand-cell"><span>${b}</span></div>`).join('');
  }
  const guides = $('#guideGrid');
  if (guides) {
    guides.innerHTML = GUIDES.map(g => `<a class="guide-card" href="#">
      <div class="guide-top"><span class="guide-tag">${g.tag}</span>${icon(g.icon)}</div>
      <div class="guide-body">
        <b>${g.title}</b>
        <p>${g.text}</p>
        <div class="guide-meta">${icon('clock', 'icon--sm')} ${g.meta}</div>
      </div>
    </a>`).join('');
  }
}

/* ---------------- Shop page ---------------- */
let shopState = null;

function initShop() {
  const params = new URLSearchParams(location.search);
  const v = getVehicle();
  shopState = {
    q: (params.get('q') || '').trim(),
    cats: new Set(params.get('cat') ? [params.get('cat')] : []),
    brands: new Set(),
    compatOnly: !!v,
    stockOnly: false,
    maxPrice: 5000000,
    sort: 'relevance'
  };
  renderFilters();
  renderShop(true);

  document.addEventListener('py:vehicle', e => {
    shopState.compatOnly = !!e.detail;
    renderShop(true);
  });

  const sortSel = $('#sortSelect');
  if (sortSel) {
    sortSel.addEventListener('change', () => {
      shopState.sort = sortSel.value;
      renderShop(false);
    });
  }
  const openFilters = $('[data-open-filters]');
  if (openFilters) {
    openFilters.addEventListener('click', () => {
      $('#filtersDrawer').classList.add('is-open');
      document.body.style.overflow = 'hidden';
    });
  }
  document.addEventListener('click', e => {
    if (e.target.closest('[data-close-filters]')) {
      $('#filtersDrawer').classList.remove('is-open');
      document.body.style.overflow = '';
    }
    const rm = e.target.closest('[data-remove-chip]');
    if (rm) {
      const { removeChip } = rm.dataset;
      if (removeChip === 'q') shopState.q = '';
      if (removeChip === 'compat') { shopState.compatOnly = false; }
      if (removeChip.startsWith('cat:')) shopState.cats.delete(removeChip.slice(4));
      if (removeChip.startsWith('brand:')) shopState.brands.delete(removeChip.slice(6));
      if (removeChip === 'stock') shopState.stockOnly = false;
      renderFilters();
      renderShop(true);
    }
    if (e.target.closest('[data-clear-filters]')) {
      shopState.cats.clear();
      shopState.brands.clear();
      shopState.q = '';
      shopState.stockOnly = false;
      shopState.compatOnly = !!getVehicle();
      shopState.maxPrice = 5000000;
      renderFilters();
      renderShop(true);
    }
    const pageBtn = e.target.closest('[data-page]');
    if (pageBtn) toast('صفحه‌بندی در نسخهٔ دمو فقط یک صفحه دارد');
  });
}

function filterHTML() {
  const st = shopState;
  const v = getVehicle();
  const catCounts = {};
  PRODUCTS.forEach(p => { catCounts[p.cat] = (catCounts[p.cat] || 0) + 1; });
  const brandCounts = {};
  PRODUCTS.forEach(p => { brandCounts[p.brand] = (brandCounts[p.brand] || 0) + 1; });

  return `
    <div class="filters-head">
      <b>${icon('sliders')} فیلترها</b>
      <button data-clear-filters>پاک کردن همه</button>
    </div>
    <div class="f-group is-open">
      <div class="f-group-title">سازگاری با خودروی من</div>
      <div class="f-group-body">
        <div class="switch-row">
          <div>
            <b>فقط قطعات سازگار</b>
            <small>${v ? `${esc(v.modelName)} · ${fa(v.year)}` : 'خودرویی انتخاب نشده است'}</small>
          </div>
          <label class="switch">
            <input type="checkbox" data-f="compat" ${st.compatOnly ? 'checked' : ''} ${v ? '' : 'disabled'}>
            <span></span>
          </label>
        </div>
        ${!v ? `<button class="btn btn-ghost btn-sm btn-block" style="margin-top:10px" data-open-vehicle>${icon('car')} انتخاب خودرو</button>` : ''}
      </div>
    </div>
    <div class="f-group is-open">
      <div class="f-group-title">دسته‌بندی</div>
      <div class="f-group-body">
        ${CATEGORIES.map(c => `<label class="check">
          <input type="checkbox" data-f="cat" value="${c.id}" ${st.cats.has(c.id) ? 'checked' : ''}>
          <span>${c.name}</span><span class="count">${fa(catCounts[c.id] || 0)}</span>
        </label>`).join('')}
      </div>
    </div>
    <div class="f-group is-open">
      <div class="f-group-title">برند قطعه</div>
      <div class="f-group-body">
        ${BRANDS.filter(b => brandCounts[b]).map(b => `<label class="check">
          <input type="checkbox" data-f="brand" value="${b}" ${st.brands.has(b) ? 'checked' : ''}>
          <span style="direction:ltr">${b}</span><span class="count">${fa(brandCounts[b])}</span>
        </label>`).join('')}
      </div>
    </div>
    <div class="f-group is-open">
      <div class="f-group-title">حداکثر قیمت</div>
      <div class="f-group-body">
        <div class="price-range">
          <input type="range" min="0" max="5000000" step="50000" value="${st.maxPrice}" data-f="price" dir="ltr">
          <div class="price-inputs">
            <span class="price-max-label" style="font-size:11.5px;color:var(--muted);width:100%;text-align:center">
              تا ${money(st.maxPrice)} تومان
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="f-group is-open">
      <div class="f-group-title">موجودی</div>
      <div class="f-group-body">
        <label class="check">
          <input type="checkbox" data-f="stock" ${st.stockOnly ? 'checked' : ''}>
          <span>فقط کالاهای موجود</span>
        </label>
      </div>
    </div>`;
}

function renderFilters() {
  const a = $('#filtersDesktop');
  const b = $('#filtersDrawerBody');
  if (a) a.innerHTML = filterHTML();
  if (b) b.innerHTML = filterHTML();
}

function filteredProducts() {
  const st = shopState;
  const v = getVehicle();
  let list = PRODUCTS.filter(p => {
    if (st.q) {
      const hay = `${p.name} ${p.brand} ${p.sku} ${p.oem}`.toLowerCase();
      if (!hay.includes(st.q.toLowerCase())) return false;
    }
    if (st.cats.size && !st.cats.has(p.cat)) return false;
    if (st.brands.size && !st.brands.has(p.brand)) return false;
    if (st.stockOnly && p.stock <= 0) return false;
    if (p.price > st.maxPrice) return false;
    if (st.compatOnly && v && fitStatus(p, v) !== 'fit') return false;
    return true;
  });

  const rank = { fit: 0, unknown: 1, ask: 2, no: 3 };
  if (st.sort === 'relevance') {
    list.sort((a, b) => rank[fitStatus(a, v)] - rank[fitStatus(b, v)]);
  } else if (st.sort === 'price-asc') list.sort((a, b) => a.price - b.price);
  else if (st.sort === 'price-desc') list.sort((a, b) => b.price - a.price);
  else if (st.sort === 'rating') list.sort((a, b) => b.rating - a.rating);
  else if (st.sort === 'newest') list.reverse();

  return list;
}

function renderShop(rebuildFilters = false) {
  if (!shopState) return;
  if (rebuildFilters) renderFilters();
  const st = shopState;
  const v = getVehicle();
  const list = filteredProducts();

  const grid = $('#shopGrid');
  if (grid) {
    if (list.length) {
      grid.innerHTML = list.map(productCard).join('');
    } else {
      grid.innerHTML = `<div class="empty" style="grid-column:1/-1">
        <span class="empty-icon">${icon('search')}</span>
        <b>قطعه‌ای با این مشخصات پیدا نشد</b>
        <p>${st.compatOnly && v ? `هیچ قطعهٔ ثبت‌شده‌ای برای ${esc(v.modelName)} با فیلترهای فعلی پیدا نشد. فیلتر «فقط سازگار» را بردارید یا خودرو را تغییر دهید.` : 'عبارت جست‌وجو یا فیلترها را تغییر دهید؛ می‌توانید فیلترها را پاک کنید.'}</p>
        <button class="btn btn-amber" data-clear-filters>پاک کردن فیلترها</button>
      </div>`;
    }
  }

  const countEl = $('#resultCount');
  if (countEl) {
    const all = filteredProducts().length;
    countEl.innerHTML = `<b>${fa(all)}</b> قطعه${v && st.compatOnly ? ` سازگار با <b>${esc(v.modelName)}</b>` : ''} پیدا شد`;
  }

  /* active chips */
  const chips = $('#activeChips');
  if (chips) {
    const items = [];
    if (st.q) items.push(`<span class="chip">جست‌وجو: ${esc(st.q)} <button data-remove-chip="q" aria-label="حذف">${icon('x')}</button></span>`);
    if (st.compatOnly && v) items.push(`<span class="chip">فقط سازگار با ${esc(v.modelName)} <button data-remove-chip="compat" aria-label="حذف">${icon('x')}</button></span>`);
    st.cats.forEach(c => {
      const cat = CATEGORIES.find(x => x.id === c);
      if (cat) items.push(`<span class="chip">${cat.name} <button data-remove-chip="cat:${c}" aria-label="حذف">${icon('x')}</button></span>`);
    });
    st.brands.forEach(b => items.push(`<span class="chip" style="direction:ltr">${b} <button data-remove-chip="brand:${b}" aria-label="حذف">${icon('x')}</button></span>`));
    if (st.stockOnly) items.push(`<span class="chip">موجود در انبار <button data-remove-chip="stock" aria-label="حذف">${icon('x')}</button></span>`);
    if (items.length) items.push('<button class="chip chip--clear" data-clear-filters>حذف همه</button>');
    chips.innerHTML = items.join('');
    chips.style.display = items.length ? 'flex' : 'none';
  }

  /* vehicle bar */
  const bar = $('#vehicleBar');
  if (bar) {
    bar.innerHTML = v
      ? `<div class="vbar-left">
           <span class="vb-icon">${icon('car')}</span>
           <div>
             <b>خودروی انتخابی: ${esc(v.makeName)} ${esc(v.modelName)} · ${fa(v.year)}</b>
             <span>${st.compatOnly ? 'فقط قطعاتی نمایش داده می‌شود که سازگاری آن‌ها تأیید شده است.' : 'همهٔ قطعات نمایش داده می‌شود؛ نشان سازگاری روی هر کارت مشخص است.'}</span>
           </div>
         </div>
         <div class="vbar-actions">
           <button class="btn btn-ghost btn-sm" data-open-vehicle>${icon('refresh')} تغییر خودرو</button>
           <button class="btn btn-ghost btn-sm" data-clear-vehicle>${icon('x')} حذف</button>
         </div>`
      : `<div class="vbar-left">
           <span class="vb-icon">${icon('car')}</span>
           <div>
             <b>خودرویتان را انتخاب کنید</b>
             <span>با انتخاب خودرو، فقط قطعات سازگار را می‌بینید و از خرید اشتباه جلوگیری می‌کنید.</span>
           </div>
         </div>
         <div class="vbar-actions">
           <button class="btn btn-amber btn-sm" data-open-vehicle>${icon('car')} انتخاب خودرو</button>
         </div>`;
  }

  const firstCat = [...st.cats][0];
  const cat = CATEGORIES.find(c => c.id === firstCat);
  const title = cat ? cat.name : (st.q ? `نتایج جست‌وجو برای «${st.q}»` : 'همهٔ قطعات خودرو');
  const pageTitle = $('#shopTitle');
  const heading = $('#pageHeading');
  if (pageTitle) pageTitle.textContent = title;
  if (heading) heading.textContent = title;
  document.title = `${title} | پارت‌یاب`;
}

/* filters events (delegated) */
document.addEventListener('change', e => {
  if (!shopState) return;
  const el = e.target.closest('[data-f]');
  if (!el) return;
  const f = el.dataset.f;
  if (f === 'compat') { shopState.compatOnly = el.checked; renderShop(true); }
  if (f === 'stock') { shopState.stockOnly = el.checked; renderShop(true); }
  if (f === 'cat') {
    el.checked ? shopState.cats.add(el.value) : shopState.cats.delete(el.value);
    renderShop(true);
  }
  if (f === 'brand') {
    el.checked ? shopState.brands.add(el.value) : shopState.brands.delete(el.value);
    renderShop(true);
  }
});
document.addEventListener('input', e => {
  if (!shopState) return;
  const el = e.target.closest('[data-f="price"]');
  if (!el) return;
  shopState.maxPrice = Number(el.value);
  $$('.price-max-label').forEach(l => l.textContent = `تا ${money(shopState.maxPrice)} تومان`);
  renderShop(false);
});

/* ---------------- Product page ---------------- */
let pdpQty = 1;

function initProduct() {
  const id = new URLSearchParams(location.search).get('id') || PRODUCTS[0].id;
  const p = PRODUCTS.find(x => x.id === id) || PRODUCTS[0];
  $('#pdpRoot').innerHTML = pdpHTML(p);
  document.title = `${p.name} | پارت‌یاب`;

  document.addEventListener('py:vehicle', () => renderPDP());

  document.addEventListener('click', e => {
    const qtyBtn = e.target.closest('[data-qty]');
    if (qtyBtn) {
      pdpQty = Math.max(1, pdpQty + (qtyBtn.dataset.qty === '+' ? 1 : -1));
      $$('.qty-val').forEach(el => el.textContent = fa(pdpQty));
      return;
    }
    const buy = e.target.closest('[data-buy]');
    if (buy) {
      addToCart(p, pdpQty);
      return;
    }
    if (e.target.closest('[data-buy-now]')) {
      addToCart(p, pdpQty);
      toast('در نسخهٔ واقعی، کاربر به صفحهٔ پرداخت ووکامرس منتقل می‌شود');
      return;
    }
    const tab = e.target.closest('[data-tab]');
    if (tab) {
      $$('.tab-btn').forEach(b => b.classList.toggle('is-active', b === tab));
      $$('.tab-panel').forEach(panel => panel.classList.toggle('is-active', panel.dataset.panel === tab.dataset.tab));
    }
  });
}

function pdpHTML(p) {
  const v = getVehicle();
  const st = fitStatus(p, v);
  const off = p.oldPrice ? Math.round((1 - p.price / p.oldPrice) * 100) : 0;
  let related = PRODUCTS.filter(x => x.cat === p.cat && x.id !== p.id).slice(0, 4);
  if (related.length < 2) {
    related = related.concat(PRODUCTS.filter(x => x.id !== p.id && !related.includes(x)).slice(0, 4 - related.length));
  }
  const fits = p.fits.map(f => {
    const m = getModel(f.model);
    const mine = v && v.modelId === f.model;
    return `<tr class="${mine ? 'is-mine' : ''}">
      <td><b>${m ? m.model.name : f.model}</b>${mine ? ' <span style="color:var(--amber-700);font-size:10px">(خودروی شما)</span>' : ''}</td>
      <td>${m ? m.make.name : ''}</td>
      <td class="mono">${fa(f.from)} تا ${fa(f.to)}</td>
      <td><span class="fit-status ${mine ? 'fit-status--fit' : 'fit-status--no'}">${icon(mine ? 'check' : 'info')} ${mine ? 'سازگار' : 'ثبت‌شده'}</span></td>
    </tr>`;
  }).join('');

  const fitPanel = (() => {
    if (!v) {
      return `<div class="fitment-panel fitment-panel--unknown">
        <div class="fp-head">${icon('car')} خودروی خود را انتخاب کنید</div>
        <div class="fp-body">برای دیدن وضعیت دقیق سازگاری، خودرویتان را انتخاب کنید. اطلاعات سازگاری این محصول توسط فروشگاه ثبت شده است.</div>
        <button class="btn btn-amber btn-sm" style="margin-top:10px" data-open-vehicle>${icon('car')} انتخاب خودرو</button>
      </div>`;
    }
    const l = FIT_LABEL[st];
    return `<div class="fitment-panel fitment-panel--${l.cls}">
      <div class="fp-head">${icon(l.icon)} ${l.long}</div>
      <div class="fp-body">
        ${st === 'fit'
          ? 'فروشگاه سازگاری این قطعه را با خودروی انتخابی شما تأیید کرده است. پیش از نصب، شمارهٔ فنی روی قطعه را با کد بالا تطبیق دهید.'
          : st === 'no'
            ? 'این قطعه برای خودروی انتخابی شما در فهرست سازگاری فروشگاه ثبت نشده است. لطفاً قطعهٔ دیگری انتخاب کنید یا از پشتیبانی بپرسید.'
            : 'اطلاعات سازگاری این محصول در فروشگاه ثبت نشده است. این به معنی ناسازگاری نیست؛ پیش از خرید با پشتیبانی استعلام کنید.'}
      </div>
      <div class="fp-vehicle">${icon('car')} ${esc(v.makeName)} ${esc(v.modelName)} · ${fa(v.year)}${v.engine ? ` · ${esc(v.engine)}` : ''}</div>
      <div class="fp-optional"><a href="#" data-open-vehicle>تغییر خودرو</a> · ${st === 'unknown' ? `از پشتیبانی بپرسید: ۰۲۱–۹۱۰۰۲۲۳۳` : 'سازگاری توسط فروشگاه ثبت شده است'}</div>
    </div>`;
  })();

  return `
  <div class="container">
    <nav class="breadcrumb" style="padding-block:14px">
      <a href="index.html">خانه</a><span class="sep">/</span>
      <a href="shop.html">فروشگاه قطعات</a><span class="sep">/</span>
      <a href="shop.html?cat=${p.cat}">${(CATEGORIES.find(c => c.id === p.cat) || {}).name || 'دسته'}</a><span class="sep">/</span>
      <span class="current">${p.name}</span>
    </nav>

    <div class="pdp">
      <div class="gallery">
        <div class="gallery-main">
          <img src="assets/img/${p.img}" alt="${esc(p.name)}">
          ${fitBadge(st)}
        </div>
        <div class="buy-meta" style="border:0;padding-top:10px;margin-top:2px">
          <span>${icon('shield')} اصالت کالا تضمین‌شده</span>
          <span>${icon('truck')} ارسال ۲۴ ساعته</span>
          <span>${icon('refresh')} ۷ روز بازگشت</span>
        </div>
      </div>

      <div class="pdp-info">
        <div class="pdp-top">
          <div class="pdp-brandline">
            <span class="brand-chip">${p.brand}</span>
            <span>${p.verified ? 'برند تأییدشدهٔ فروشگاه' : 'برند ثبت‌شده'}</span>
          </div>
          <div class="rating">
            <span class="stars">${icon('star')}${icon('star')}${icon('star')}${icon('star')}${icon('star')}</span>
            <b>${fa(p.rating)}</b> <span>(${fa(p.reviews)} دیدگاه)</span>
          </div>
        </div>

        <h1 class="pdp-title">${p.name}</h1>
        <p class="pdp-sub">${p.desc}</p>

        <div class="code-table">
          <div class="code-cell"><span>کد فنی (OEM)</span><b>${p.oem}</b></div>
          <div class="code-cell"><span>کد کالا (SKU)</span><b>${p.sku}</b></div>
          <div class="code-cell"><span>برند</span><b style="font-family:Vazirmatn;direction:ltr">${p.brand}</b></div>
        </div>

        ${fitPanel}

        <div class="buybox">
          <div class="buybox-row">
            <div class="price-big">
              ${p.oldPrice ? `<span class="old">${money(p.oldPrice)} تومان</span>` : ''}
              <strong>${money(p.price)} <small>تومان</small></strong>
              <div class="stock-line">${p.stock < 6 ? `فقط ${fa(p.stock)} عدد در انبار` : 'موجود در انبار فروشگاه'}</div>
            </div>
            ${off ? `<span class="off-tag">${fa(off)}٪ تخفیف</span>` : ''}
          </div>
          <div class="buy-actions">
            <div class="qty">
              <button data-qty="+" aria-label="افزودن">${icon('plus')}</button>
              <span class="qty-val">${fa(pdpQty)}</span>
              <button data-qty="-" aria-label="کاهش">${icon('minus')}</button>
            </div>
            <button class="btn btn-amber" data-buy>${icon('cart')} افزودن به سبد خرید</button>
          </div>
          <div class="buy-meta">
            <span>${icon('shield')} ضمانت اصالت و سلامت فیزیکی</span>
            <span>${icon('truck')} ارسال به سراسر کشور</span>
            <span>${icon('refresh')} بازگشت تا ۷ روز</span>
          </div>
        </div>
      </div>
    </div>

    <div class="tabs-wrap">
      <div class="tabs-head">
        <button class="tab-btn is-active" data-tab="desc">${icon('file')} توضیحات</button>
        <button class="tab-btn" data-tab="specs">${icon('sliders')} مشخصات فنی</button>
        <button class="tab-btn" data-tab="fit">${icon('car')} خودروهای سازگار</button>
        <button class="tab-btn" data-tab="install">${icon('wrench')} راهنمای نصب</button>
        <button class="tab-btn" data-tab="reviews">${icon('star')} دیدگاه‌ها</button>
      </div>

      <div class="tab-panel is-active" data-panel="desc">
        <div class="prose">
          <p>${p.desc}</p>
          <h4>نکات مهم پیش از خرید</h4>
          <ul>
            <li>کد فنی روی قطعه یا بسته‌بندی را با کد <bdi>${p.oem}</bdi> تطبیق دهید.</li>
            <li>سازگاری اعلام‌شده بر اساس داده‌ای است که فروشگاه ثبت کرده؛ در موارد خاص ممکن است نیاز به استعلام داشته باشد.</li>
            <li>در صورت ثبت‌نشدن سازگاری برای خودروی شما، خرید را نهایی نکنید و با پشتیبانی تماس بگیرید.</li>
          </ul>
        </div>
      </div>

      <div class="tab-panel" data-panel="specs">
        <table class="spec-table">
          <tbody>
            ${Object.entries(p.specs).map(([k, val]) => `<tr><th>${k}</th><td>${val}</td></tr>`).join('')}
          </tbody>
        </table>
      </div>

      <div class="tab-panel" data-panel="fit">
        ${p.fitmentKnown ? `
          <p class="prose" style="margin-bottom:12px">این محصول برای خودروهای زیر در فروشگاه ثبت شده است. بازهٔ سال ساخت شامل هر دو سال ابتدا و انتها است.</p>
          <table class="fit-table">
            <thead><tr><th>مدل خودرو</th><th>سازنده</th><th>بازهٔ سال ساخت</th><th>وضعیت</th></tr></thead>
            <tbody>${fits}</tbody>
          </table>
          <p class="prose" style="margin-top:12px;font-size:11.5px;color:var(--muted)">
            ${icon('info')} نبودن یک خودرو در این جدول به معنی ناسازگاری قطعی نیست؛ یعنی فروشگاه برای آن خودرو اطلاعاتی ثبت نکرده است.
          </p>`
          : `<div class="empty" style="padding:32px">
              <span class="empty-icon">${icon('alert')}</span>
              <b>اطلاعات سازگاری برای این محصول ثبت نشده است</b>
              <p>فروشنده هنوز فهرست خودروهای سازگار را برای این کالا وارد نکرده است. پیش از خرید با پشتیبانی استعلام کنید.</p>
              <button class="btn btn-ghost" data-open-vehicle>${icon('car')} انتخاب خودرو برای بررسی</button>
            </div>`}
      </div>

      <div class="tab-panel" data-panel="install">
        <div class="prose">
          <h4>راهنمای نصب</h4>
          <ul>
            <li>پیش از نصب، باتری خودرو را جدا کنید و اجازه دهید قطعات خنک شوند.</li>
            <li>قطعهٔ قدیمی را باز کنید و سطح نشیمن را از آلودگی و زنگ پاک کنید.</li>
            <li>گشتاور پیچ‌ها را طبق دفترچهٔ خودرو اعمال کنید؛ سفت‌کردن بیش از حد می‌تواند به قطعه آسیب بزند.</li>
            <li>پس از نصب، در مسیر خلوت ترمزگیری/حرکت آزمایشی انجام دهید.</li>
          </ul>
          <p style="font-size:11.5px;color:var(--muted)">${icon('alert')} نصب توسط تعمیرکار مجاز توصیه می‌شود؛ این راهنما جایگزین دفترچهٔ خودرو نیست.</p>
        </div>
      </div>

      <div class="tab-panel" data-panel="reviews">
        <div class="prose">
          <p>میانگین امتیاز: <b>${fa(p.rating)} از ۵</b> بر اساس ${fa(p.reviews)} دیدگاه ثبت‌شده.</p>
          <p style="color:var(--muted);font-size:12px">در نسخهٔ واقعی، دیدگاه‌ها فقط برای خریداران تأییدشده ثبت می‌شود تا از امتیازهای غیرواقعی جلوگیری شود.</p>
        </div>
      </div>
    </div>

    <section class="section">
      <div class="section-head">
        <div>
          <span class="eyebrow">پیشنهاد پارت‌یاب</span>
          <h2 class="section-title">قطعات مرتبط</h2>
        </div>
        <a class="link-more" href="shop.html?cat=${p.cat}">مشاهدهٔ همه ${icon('arrow-left')}</a>
      </div>
      <div class="p-grid">${related.map(productCard).join('')}</div>
    </section>
  </div>

  <div class="mobile-buybar">
    <div class="price-big">
      ${p.oldPrice ? `<span class="old">${money(p.oldPrice)}</span>` : ''}
      <strong>${money(p.price)} <small>تومان</small></strong>
    </div>
    <button class="btn btn-amber" data-buy>${icon('cart')} افزودن به سبد</button>
  </div>`;
}

function renderPDP() {
  const id = new URLSearchParams(location.search).get('id') || PRODUCTS[0].id;
  const p = PRODUCTS.find(x => x.id === id) || PRODUCTS[0];
  $('#pdpRoot').innerHTML = pdpHTML(p);
  document.title = `${p.name} | پارت‌یاب`;
}

function bindPDPTabs() {
  /* tabs are handled by delegated listener in initProduct */
}

/* ---------------- Boot ---------------- */
document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  const page = document.body.dataset.page;
  if (page === 'home') {
    initHero();
    initHome();
  }
  if (page === 'shop') initShop();
  if (page === 'product') initProduct();
  hydrateIcons();
});

window.pyToast = toast;
window.hydrateIcons = hydrateIcons;
