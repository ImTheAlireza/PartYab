/* =========================================================
   PartYab — UI logic (demo)
   گاراژ چندخودرو · انتخاب خودرو با جست‌وجوی تایپی
   پنج حالت سازگاری · کدهای معادل · کیت تعمیر
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
  garage: '<path d="M3.4 20.2V9.6L12 4.2l8.6 5.4v10.6"/><path d="M7.4 20.2v-6.4h9.2v6.4"/><path d="M9.4 16.8h5.2"/>',
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
  menu: '<path d="M4 7h16M4 12h16M4 17h16"/>',
  copy: '<rect x="8.6" y="8.6" width="11.4" height="11.4" rx="2.4"/><path d="M15.4 5.4H6a2 2 0 0 0-2 2v9.4"/>',
  send: '<path d="m21 3-9.4 18-2.2-7-7-2.2L21 3Z"/><path d="m9.4 14 5.2-5.2"/>',
  key: '<circle cx="8.2" cy="15.8" r="3.4"/><path d="m10.8 13.2 8-8M16.4 5.2l2.4 2.4M14.4 7.2l2.4 2.4"/>',
  'check-shield': '<path d="M12 3.2 18.6 6v5.2c0 4.1-2.8 7.2-6.6 9-3.8-1.8-6.6-4.9-6.6-9V6L12 3.2Z"/><path d="m9 12 2 2 4-4"/>',
  layers: '<path d="m12 3.4 8.4 4.4-8.4 4.4-8.4-4.4 8.4-4.4Z"/><path d="m3.6 12 8.4 4.4 8.4-4.4M3.6 16.4 12 20.8l8.4-4.4"/>'
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
const norm = s => String(s)
  .replace(/[۰-۹]/g, d => FA_DIGITS.indexOf(d))
  .replace(/[يى]/g, 'ی').replace(/ك/g, 'ک')
  .replace(/\u200c/g, ' ')
  .toLowerCase()
  .replace(/\s+/g, ' ')
  .trim();

/* ---------------- Vehicle data helpers ---------------- */
const MODELS = MAKES.flatMap(mk => mk.models.map(md => ({
  ...md,
  makeId: mk.id,
  makeName: mk.name,
  makeLatin: mk.latin,
  group: mk.group,
  searchText: norm([md.name, (md.keywords || []).join(' '), mk.name, mk.latin, md.id].join(' '))
})));

const findModel = id => MODELS.find(m => m.id === id) || null;

const POPULAR_MODELS = ['206', 'pride', 'samand', 'quick', 'shahin', 'dena', 'corolla', 'tucson'];

function searchModels(q) {
  const nq = norm(q);
  if (!nq) return [];
  return MODELS.filter(m => m.searchText.includes(nq) || norm(fa(m.years[0]) + ' ' + fa(m.years[1])).includes(nq));
}

/* ---------------- Garage (multi-vehicle) ---------------- */
const GKEY = 'py_garage_v2';
const OLD_KEY = 'py_vehicle_v1';
const RECENT_KEY = 'py_recent_v1';

function garageLoad() {
  try {
    const g = JSON.parse(localStorage.getItem(GKEY));
    if (g && Array.isArray(g.vehicles)) return g;
  } catch (e) { /* ignore */ }
  return { vehicles: [], active: null };
}
function garageWrite(g) {
  localStorage.setItem(GKEY, JSON.stringify(g));
  refreshVehicleUI();
  document.dispatchEvent(new CustomEvent('py:vehicle', { detail: activeVehicle() }));
}
const activeVehicle = () => {
  const g = garageLoad();
  return g.vehicles.find(v => v.key === g.active) || null;
};
const getVehicle = activeVehicle;

function makeVehicle(makeId, modelId, year, engine = '') {
  const m = findModel(modelId);
  if (!m) return null;
  return {
    key: `${makeId}:${modelId}:${year}`,
    makeId, makeName: m.makeName,
    modelId, modelName: m.name,
    year: Number(year), engine: engine || ''
  };
}

function addVehicle(v) {
  const g = garageLoad();
  const exists = g.vehicles.find(x => x.key === v.key);
  if (exists) {
    exists.engine = v.engine || exists.engine;
  } else {
    if (g.vehicles.length >= 3) g.vehicles.shift();
    g.vehicles.push(v);
  }
  g.active = v.key;
  garageWrite(g);
  pushRecent(v.modelId);
}

function setActiveByKey(key) {
  const g = garageLoad();
  if (!g.vehicles.some(v => v.key === key)) return;
  g.active = key;
  garageWrite(g);
}

function removeVehicle(key) {
  const g = garageLoad();
  g.vehicles = g.vehicles.filter(v => v.key !== key);
  if (g.active === key) g.active = g.vehicles.length ? g.vehicles[g.vehicles.length - 1].key : null;
  garageWrite(g);
}

function pushRecent(modelId) {
  let list = [];
  try { list = JSON.parse(localStorage.getItem(RECENT_KEY)) || []; } catch (e) { /* ignore */ }
  list = [modelId, ...list.filter(x => x !== modelId)].slice(0, 4);
  localStorage.setItem(RECENT_KEY, JSON.stringify(list));
}
function recentModels() {
  try { return (JSON.parse(localStorage.getItem(RECENT_KEY)) || []).map(findModel).filter(Boolean); }
  catch (e) { return []; }
}

/* مهاجرت از نسخهٔ تک‌خودرو */
(function migrateOldVehicle() {
  try {
    const g = garageLoad();
    if (g.vehicles.length) return;
    const old = JSON.parse(localStorage.getItem(OLD_KEY) || 'null');
    if (old && old.modelId) {
      const v = makeVehicle(old.makeId, old.modelId, old.year, old.engine);
      if (v) localStorage.setItem(GKEY, JSON.stringify({ vehicles: [v], active: v.key }));
    }
  } catch (e) { /* ignore */ }
})();

/* ---------------- Fitment (5 states) ---------------- */
const FIT = {
  fit: { short: 'سازگار', long: 'با خودروی شما سازگار است', icon: 'check', tone: 'fit' },
  conditional: { short: 'با شرط', long: 'سازگار است، با شرط', icon: 'alert', tone: 'conditional' },
  no: { short: 'ناسازگار', long: 'با خودروی شما سازگار نیست', icon: 'x', tone: 'no' },
  unknown: { short: 'ثبت نشده', long: 'سازگاری برای این خودرو ثبت نشده', icon: 'info', tone: 'unknown' },
  universal: { short: 'عمومی', long: 'قطعهٔ عمومی', icon: 'box', tone: 'universal' },
  ask: { short: 'انتخاب خودرو', long: 'خودرو را انتخاب کنید', icon: 'car', tone: 'ask' }
};

function fitInfo(p, v) {
  if (p.universal) return { state: 'universal', note: p.universalNote || '' };
  if (!v) return { state: 'ask' };
  if (!p.fitmentKnown) return { state: 'unknown' };
  const f = p.fits.find(x => x.model === v.modelId);
  if (!f) return { state: 'no' };
  if (v.year < f.from || v.year > f.to) return { state: 'no', outOfRange: { from: f.from, to: f.to } };
  return {
    state: f.status === 'conditional' ? 'conditional' : 'fit',
    note: f.note || '',
    range: { from: f.from, to: f.to }
  };
}

const fitBadge = (state, long = false) => {
  const l = FIT[state] || FIT.ask;
  return `<span class="fit-badge fit-badge--${l.tone}">${icon(l.icon)}${long ? l.long : l.short}</span>`;
};

function fitMessage(p, v) {
  if (p.universal) return `قطعهٔ عمومی؛ محدود به خودروی خاصی نیست. ${esc(p.universalNote || '')}`;
  if (!v) return '';
  const info = fitInfo(p, v);
  const car = `${esc(v.modelName)} ${fa(v.year)}`;
  if (info.state === 'fit') return `این قطعه برای <b>${car}</b> در فروشگاه تأیید شده است.`;
  if (info.state === 'conditional') return `با <b>${car}</b> سازگار است، به شرط: ${esc(info.note || 'بررسی تیپ خودرو')}`;
  if (info.state === 'unknown') return `اطلاعات سازگاری برای <b>${car}</b> ثبت نشده است؛ یعنی «نامشخص»، نه ناسازگار.`;
  const others = p.fits.slice(0, 3).map(f => findModel(f.model)?.name).filter(Boolean);
  if (info.outOfRange) {
    return `این قطعه برای <b>${car}</b> ثبت نشده؛ بازهٔ ثبت‌شده برای این مدل ${fa(info.outOfRange.from)} تا ${fa(info.outOfRange.to)} است.`;
  }
  return `با <b>${car}</b> سازگار نیست.${others.length ? ` این قطعه برای ${others.join('، ')} ثبت شده است.` : ''}`;
}

/* place the garage dropdown inside the viewport (RTL-safe) */
function placeVehicleMenu() {
  const wrap = $('.vehicle-wrap');
  const menu = $('#vehicleMenu');
  if (!wrap || !menu || menu.hidden) return;
  const w = Math.min(330, window.innerWidth - 24);
  const r = wrap.getBoundingClientRect();
  const left = Math.max(12, Math.min(r.right - w, window.innerWidth - 12 - w));
  menu.style.width = w + 'px';
  menu.style.insetInlineStart = 'auto';
  menu.style.insetInlineEnd = 'auto';
  menu.style.left = (left - r.left) + 'px';
}

/* ---------------- Toast ---------------- */
let toastTimer;
function toast(msg, type = 'info') {
  let el = $('#toast');
  if (!el) {
    el = document.createElement('div');
    el.id = 'toast';
    el.className = 'toast';
    el.setAttribute('role', 'status');
    el.setAttribute('aria-live', 'polite');
    document.body.appendChild(el);
  }
  el.className = `toast ${type === 'success' ? 'is-success' : ''}`;
  el.innerHTML = `${icon(type === 'success' ? 'check' : 'info')}<span>${msg}</span>`;
  requestAnimationFrame(() => el.classList.add('is-show'));
  clearTimeout(toastTimer);
  toastTimer = setTimeout(() => el.classList.remove('is-show'), 3600);
}

/* ---------------- Cart ---------------- */
const KEY_CART = 'py_cart_v1';
const cartCount = () => Number(localStorage.getItem(KEY_CART) || 0);
function addToCart(p, qty = 1, silent = false) {
  localStorage.setItem(KEY_CART, cartCount() + qty);
  const el = $('.cart-count');
  if (el) {
    el.textContent = fa(cartCount());
    el.style.animation = 'none';
    void el.offsetWidth;
    el.style.animation = 'pop .35s ease';
  }
  if (!silent) toast(`«${p.name}» به سبد خرید اضافه شد`, 'success');
}

/* ---------------- Modals ---------------- */
let lastFocused = null;
function openModal(sel) {
  lastFocused = document.activeElement;
  const m = $(sel);
  if (!m) return;
  m.classList.add('is-open');
  m.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
  setTimeout(() => {
    const f = m.querySelector('[data-autofocus]') || m.querySelector('input, select, textarea, button');
    if (f) f.focus({ preventScroll: true });
  }, 60);
}
function closeModal(sel) {
  const m = $(sel);
  if (!m) return;
  m.classList.remove('is-open');
  m.setAttribute('aria-hidden', 'true');
  if (!$('.modal.is-open') && !$('.drawer.is-open')) document.body.style.overflow = '';
  if (lastFocused && document.contains(lastFocused)) {
    try { lastFocused.focus({ preventScroll: true }); } catch (e) { /* ignore */ }
  }
}
function trapFocus(e) {
  if (e.key !== 'Tab') return;
  const modal = e.currentTarget;
  const items = $$('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])', modal)
    .filter(el => !el.disabled && el.offsetParent !== null);
  if (!items.length) return;
  const first = items[0], last = items[items.length - 1];
  if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
  else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
}

/* ---------------- Product card ---------------- */
function productCard(p) {
  const v = getVehicle();
  const info = fitInfo(p, v);
  const off = p.oldPrice ? Math.round((1 - p.price / p.oldPrice) * 100) : 0;
  return `
  <article class="p-card ${info.state === 'no' ? 'p-card--dim' : ''}" data-product="${p.id}">
    <a class="p-media" href="product.html?id=${p.id}" aria-label="${esc(p.name)}">
      <img src="assets/img/${p.img}" alt="${esc(p.name)}" loading="lazy">
      ${fitBadge(info.state)}
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
        <button class="p-add" data-add="${p.id}" aria-label="افزودن ${esc(p.name)} به سبد خرید">${icon('plus')}</button>
      </div>
      ${p.equivalents && p.equivalents.length > 1 ? `<div class="p-equiv">${icon('copy')} ${fa(p.equivalents.length)} کد معادل دارد</div>` : ''}
    </div>
  </article>`;
}

/* ---------------- Header / overlays ---------------- */
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

      <div class="vehicle-wrap" id="vehicleSlot"></div>

      <div class="header-search">
        <form role="search" data-search-form>
          ${icon('search', 'icon-search')}
          <input type="search" name="q" placeholder="نام قطعه، برند یا کد فنی را وارد کنید" aria-label="جست‌وجو">
          <button class="search-kbd" type="button" data-open-cmd aria-label="جست‌وجوی سریع (Ctrl+K)"><kbd>⌘K</kbd></button>
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
        <a class="drawer-link" href="#" data-open-vehicle>${icon('garage')} گاراژ من</a>
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
    <div class="modal-card modal-card--wide" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
      <div class="modal-head">
        <div>
          <h3 id="vmTitle">خودروی خود را انتخاب کنید</h3>
          <p>پوشش فعلی: خودروهای ایرانی و وارداتی‌های رایج. دادهٔ سازگاری را فروشگاه ثبت می‌کند.</p>
        </div>
        <button class="modal-close" data-close-vehicle-modal aria-label="بستن">${icon('x')}</button>
      </div>
      <div class="modal-body" id="vehicleModalBody"></div>
    </div>
  </div>

  <div class="modal" id="requestModal" aria-hidden="true">
    <div class="modal-backdrop" data-close-request></div>
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="rmTitle">
      <div class="modal-head">
        <div>
          <h3 id="rmTitle">درخواست بررسی</h3>
          <p>کد فنی یا نام قطعه را بفرستید؛ کارشناس سازگاری را بررسی و تماس می‌گیرد.</p>
        </div>
        <button class="modal-close" data-close-request aria-label="بستن">${icon('x')}</button>
      </div>
      <div class="modal-body" id="requestModalBody"></div>
    </div>
  </div>

  <div class="modal cmd" id="cmdModal" aria-hidden="true">
    <div class="modal-backdrop" data-close-cmd></div>
    <div class="modal-card cmd-card" role="dialog" aria-modal="true" aria-label="جست‌وجوی سریع">
      <div class="cmd-input">
        ${icon('search')}
        <input id="cmdInput" type="text" placeholder="قطعه، کد فنی، خودرو یا صفحه…" autocomplete="off" aria-label="جست‌وجوی سریع" data-autofocus>
        <kbd>Esc</kbd>
      </div>
      <div class="cmd-results" id="cmdResults"></div>
      <div class="cmd-foot"><span>جابه‌جایی با ↑ ↓</span><span>انتخاب با Enter</span></div>
    </div>
  </div>

  <nav class="mobile-nav" aria-label="ناوبری موبایل">
    <a href="index.html" class="${document.body.dataset.page === 'home' ? 'is-active' : ''}">${icon('grid')}<span>خانه</span></a>
    <a href="shop.html" class="${document.body.dataset.page === 'shop' ? 'is-active' : ''}">${icon('box')}<span>دسته‌ها</span></a>
    <button data-open-vehicle>${icon('garage')}<span>خودرو</span></button>
    <button data-cart>${icon('cart')}<span>سبد</span></button>
  </nav>`;
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
        <p>فروشگاه نمونهٔ قطعات خودرو با تمرکز بر خودروهای ایرانی و پشتیبانی از وارداتی‌ها؛ از انتخاب خودرو تا نمایش شفاف وضعیت سازگاری.</p>
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
        <a href="index.html#states">معنی نشان‌های سازگاری</a>
        <a href="#">شرایط ارسال</a>
        <a href="#">بازگشت کالا</a>
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
          <li>${icon('mail')}<span>support@partyab.ir</span></li>
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
  const g = garageLoad();
  const count = g.vehicles.length;
  if (v) {
    return `<button class="vehicle-trigger is-set" data-garage-toggle aria-expanded="false" aria-haspopup="true" aria-label="گاراژ من: ${esc(v.modelName)} ${fa(v.year)}">
      ${icon('car')}
      <span class="vt-text"><small>خودروی من${count > 1 ? ` · ${fa(count)} خودرو` : ''}</small><b>${esc(v.modelName)} · ${fa(v.year)}</b></span>
      <span class="vt-chevron">${icon('chevron-down')}</span>
    </button>`;
  }
  return `<button class="vehicle-trigger" data-open-vehicle>
    ${icon('car')}
    <span class="vt-text"><small>خودرویتان را انتخاب کنید</small><b>انتخاب خودرو</b></span>
  </button>`;
}

function vehicleMenuHTML() {
  const g = garageLoad();
  const rows = g.vehicles.map(v => `
    <div class="vm-item ${v.key === g.active ? 'is-active' : ''}" role="button" tabindex="0" data-pick-vehicle="${v.key}">
      <span class="vm-check">${icon(v.key === g.active ? 'check' : 'car')}</span>
      <span class="vm-text">
        <b>${esc(v.modelName)}</b>
        <small>${esc(v.makeName)} · ${fa(v.year)}${v.engine ? ` · ${esc(v.engine)}` : ''}</small>
      </span>
      ${v.key === g.active ? '<span class="vm-active">فعال</span>' : ''}
      <button class="vm-del" data-remove-vehicle="${v.key}" aria-label="حذف ${esc(v.modelName)}">${icon('x')}</button>
    </div>`).join('');

  return `
    <div class="vm-head">
      <b>${icon('garage')} گاراژ من</b>
      <span>${fa(g.vehicles.length)} از ۳ خودرو</span>
    </div>
    <div class="vm-list">${rows || '<p class="vm-empty">هنوز خودرویی اضافه نکرده‌اید.</p>'}</div>
    <div class="vm-actions">
      <button class="vm-add" data-add-vehicle>${icon('plus')} افزودن خودرو</button>
      <button class="vm-manage" data-manage-garage>${icon('sliders')} مدیریت</button>
    </div>`;
}

function refreshVehicleUI() {
  const slot = $('#vehicleSlot');
  if (slot) {
    slot.innerHTML = vehicleTriggerHTML() + `<div class="vehicle-menu" id="vehicleMenu" hidden>${vehicleMenuHTML()}</div>`;
  }
  $$('.vchip-slot').forEach(el => {
    const v = getVehicle();
    el.innerHTML = v ? `<span class="vchip">${icon('car')} ${esc(v.modelName)} <small>· ${fa(v.year)}</small><button data-clear-vehicle aria-label="حذف خودرو">${icon('x')}</button></span>` : '';
  });
}

function toggleGarageMenu(force) {
  const menu = $('#vehicleMenu');
  const btn = $('[data-garage-toggle]');
  if (!menu) return;
  const open = typeof force === 'boolean' ? force : menu.hidden;
  menu.hidden = !open;
  if (btn) btn.setAttribute('aria-expanded', String(open));
  if (open) { renderVehicleMenu(); placeVehicleMenu(); }
}
function renderVehicleMenu() {
  const menu = $('#vehicleMenu');
  if (menu) { menu.innerHTML = vehicleMenuHTML(); placeVehicleMenu(); }
}
window.addEventListener('resize', () => { if ($('#vehicleMenu') && !$('#vehicleMenu').hidden) placeVehicleMenu(); });

/* ---------------- Vehicle picker ---------------- */
const picker = { view: 'search', modelId: null, year: null, engine: '' };

function openVehiclePicker() {
  picker.view = 'search';
  picker.modelId = null;
  picker.year = null;
  picker.engine = '';
  renderPicker();
  openModal('#vehicleModal');
}

function renderPicker() {
  const body = $('#vehicleModalBody');
  if (!body) return;
  body.innerHTML = picker.view === 'search' ? pickerSearchHTML() : pickerYearsHTML();
  if (picker.view === 'search') bindPickerSearch();
}
function updateModalFocus() {
  const m = $('#vehicleModal');
  if (!m || !m.classList.contains('is-open')) return;
  const el = m.querySelector('[data-autofocus]');
  if (el) el.focus({ preventScroll: true });
}

function pickerSearchHTML() {
  const g = garageLoad();
  const garage = g.vehicles.length ? `
    <div class="picker-garage">
      <div class="picker-label">${icon('garage')} خودروهای من</div>
      <div class="garage-rows">
        ${g.vehicles.map(v => `
          <div class="garage-row ${v.key === g.active ? 'is-active' : ''}">
            <button class="gr-main" data-pick-vehicle="${v.key}">
              <span class="gr-icon">${icon(v.key === g.active ? 'check' : 'car')}</span>
              <span class="gr-text"><b>${esc(v.modelName)}</b><small>${esc(v.makeName)} · ${fa(v.year)}</small></span>
            </button>
            <button class="gr-del" data-remove-vehicle="${v.key}" aria-label="حذف">${icon('x')}</button>
          </div>`).join('')}
      </div>
    </div>` : '';

  const recent = recentModels().filter(m => !g.vehicles.some(v => v.modelId === m.id));
  const recentHTML = recent.length ? `
    <div class="picker-recent">
      <span>اخیراً:</span>
      ${recent.map(m => `<button class="make-pill" data-quick-model="${m.id}">${esc(m.name)}</button>`).join('')}
    </div>` : '';

  return `
    ${garage}
    <div class="picker-search">
      <label class="picker-label" for="vSearchInput">${icon('search')} مدل خودرو را بنویسید</label>
      <div class="combo" id="pickerCombo">
        ${icon('search', 'combo-icon')}
        <input id="vSearchInput" type="text" placeholder="مثلاً ۲۰۶، پراید، سمند، کرولا…" autocomplete="off"
               role="combobox" aria-expanded="false" aria-controls="vResults" aria-autocomplete="list" data-autofocus>
        <div class="combo-list" id="vResults" role="listbox" aria-label="نتایج خودرو"></div>
      </div>
      ${recentHTML}
    </div>
    <div class="picker-popular">
      <span>پرجست‌وجوترین خودروها:</span>
      <div class="row">
        ${POPULAR_MODELS.map(id => findModel(id)).filter(Boolean).map(m =>
          `<button class="make-pill" data-quick-model="${m.id}">${esc(m.name)}</button>`).join('')}
      </div>
    </div>
    <p class="picker-note">${icon('info')} اگر خودرویتان در فهرست نیست، مدل را بنویسید یا از پشتیبانی بخواهید اضافه شود.</p>`;
}

function pickerYearsHTML() {
  const m = findModel(picker.modelId);
  if (!m) return pickerSearchHTML();
  const years = [];
  for (let y = m.years[1]; y >= m.years[0]; y--) years.push(y);
  return `
    <button class="picker-back" data-picker-back>${icon('chevron-right')} تغییر خودرو</button>
    <div class="picker-model">
      <span class="pm-icon">${icon('car')}</span>
      <div><b>${esc(m.name)}</b><small>${esc(m.makeName)}</small></div>
    </div>
    <div class="picker-block">
      <span class="picker-label">سال ساخت</span>
      <div class="year-grid">
        ${years.map(y => `<button class="year-btn ${picker.year === y ? 'is-active' : ''}" data-year="${y}" aria-pressed="${picker.year === y}">${fa(y)}</button>`).join('')}
      </div>
    </div>
    ${m.engines.length ? `
      <div class="picker-block">
        <span class="picker-label">موتور / تیپ <em>(اختیاری)</em></span>
        <div class="engine-chips">
          ${m.engines.map(e => `<button class="engine-chip ${picker.engine === e ? 'is-active' : ''}" data-engine="${esc(e)}" aria-pressed="${picker.engine === e}">${esc(e)}</button>`).join('')}
        </div>
      </div>` : ''}
    <div class="modal-foot">
      <button class="btn btn-amber" data-confirm-vehicle ${picker.year ? '' : 'disabled'}>${icon('check')} تأیید و مشاهده قطعات</button>
      <button class="btn btn-ghost" data-close-vehicle-modal>انصراف</button>
    </div>`;
}

function bindPickerSearch() {
  const input = $('#vSearchInput');
  const list = $('#vResults');
  if (!input || !list) return;
  bindCombobox(input, list, m => {
    picker.modelId = m.id;
    picker.year = null;
    picker.engine = '';
    picker.view = 'years';
    renderPicker();
  });
}

/* ---------------- Combobox ---------------- */
function comboResultsHTML(query) {
  const q = norm(query);
  let list;
  if (q) {
    list = searchModels(q).slice(0, 12);
  } else {
    list = POPULAR_MODELS.map(findModel).filter(Boolean);
    const extra = MODELS.filter(m => m.group === 'iran' && !list.includes(m)).slice(0, 2);
    list = [...list, ...extra];
  }
  if (!list.length) {
    return `<div class="combo-empty">${icon('search')}<span>خودرویی با این نام پیدا نشد.<br><small>مثلاً «۲۰۶» یا «کرولا» را امتحان کنید.</small></span></div>`;
  }
  const groups = [
    { key: 'iran', label: GROUP_LABEL.iran },
    { key: 'foreign', label: GROUP_LABEL.foreign }
  ];
  return groups.map(g => {
    const items = list.filter(m => m.group === g.key);
    if (!items.length) return '';
    return `<div class="combo-group">
      <div class="combo-group-label">${g.label}</div>
      ${items.map(m => `<button type="button" class="combo-opt" role="option" data-model-id="${m.id}">
          ${icon('car')}
          <span class="co-text"><b>${esc(m.name)}</b><small>${esc(m.makeName)} · ${fa(m.years[0])} تا ${fa(m.years[1])}</small></span>
          ${icon('chevron-left', 'co-arrow')}
        </button>`).join('')}
    </div>`;
  }).join('');
}

function bindCombobox(input, list, onPick) {
  let items = [];
  const render = q => {
    list.innerHTML = comboResultsHTML(q);
    items = $$('.combo-opt', list);
    list.classList.add('is-open');
    input.setAttribute('aria-expanded', 'true');
  };
  const close = () => {
    list.classList.remove('is-open');
    input.setAttribute('aria-expanded', 'false');
    items.forEach(i => i.classList.remove('is-focused'));
  };
  const choose = m => { if (m) { onPick(m); close(); } };
  input.addEventListener('focus', () => render(input.value));
  input.addEventListener('input', () => render(input.value));
  input.addEventListener('keydown', e => {
    const focused = list.querySelector('.combo-opt.is-focused');
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
      e.preventDefault();
      if (!items.length) return;
      let i = items.indexOf(focused);
      i = e.key === 'ArrowDown' ? Math.min(items.length - 1, i + 1) : Math.max(0, i - 1);
      items.forEach(x => x.classList.remove('is-focused'));
      items[i].classList.add('is-focused');
      items[i].scrollIntoView({ block: 'nearest' });
    } else if (e.key === 'Enter') {
      e.preventDefault();
      choose(MODELS.find(m => m.id === (focused || items[0])?.dataset.modelId));
    } else if (e.key === 'Escape') {
      close();
      input.blur();
    }
  });
  list.addEventListener('mousedown', e => {
    const opt = e.target.closest('.combo-opt');
    if (opt) { e.preventDefault(); choose(findModel(opt.dataset.modelId)); }
  });
  document.addEventListener('click', e => {
    if (!input.parentElement.contains(e.target)) close();
  });
  render('');
}

/* ---------------- Request / inquiry modal ---------------- */
function openRequestModal({ mode = 'fitment', product = null } = {}) {
  const v = getVehicle();
  const body = $('#requestModalBody');
  if (!body) return;
  const title = mode === 'part' ? 'درخواست قطعه' : 'استعلام سازگاری';
  body.innerHTML = `
    <form class="req-form" novalidate>
      <div class="req-context">
        <span>${icon(mode === 'part' ? 'box' : 'check-shield')}</span>
        <div>
          <b>${title}</b>
          <small>${mode === 'part'
            ? 'نام قطعه یا شرح مشکل را بنویسید تا کارشناس پیدا کند.'
            : 'کد فنی و خودرو را بفرستید؛ نتیجه را تلفنی اعلام می‌کنیم.'}</small>
        </div>
      </div>
      ${product ? `<div class="req-product">${esc(product.name)} <span>${product.oem}</span></div>` : ''}
      <div class="field">
        <label for="reqName">نام و نام خانوادگی</label>
        <input id="reqName" name="name" type="text" required placeholder="مثلاً علی رضایی">
      </div>
      <div class="field">
        <label for="reqPhone">شمارهٔ تماس</label>
        <input id="reqPhone" name="phone" type="tel" inputmode="numeric" required placeholder="۰۹۱۲۱۲۳۴۵۶۷" dir="ltr">
      </div>
      <div class="field">
        <label for="reqCode">${mode === 'part' ? 'نام قطعه / شرح مشکل' : 'کد فنی (OEM)'}</label>
        <input id="reqCode" name="code" type="text" placeholder="${mode === 'part' ? 'مثلاً لنت ترمز جلو' : 'مثلاً 4253.42'}" dir="ltr">
      </div>
      <div class="field">
        <label for="reqNote">توضیح بیشتر <em>(اختیاری)</em></label>
        <textarea id="reqNote" name="note" rows="3" placeholder="تیپ خودرو، نوع موتور، توضیح اضافه…"></textarea>
      </div>
      <div class="req-vehicle">
        ${icon('car')} ${v ? `${esc(v.modelName)} · ${fa(v.year)}${v.engine ? ' · ' + esc(v.engine) : ''}` : 'خودرویی انتخاب نشده — می‌توانید تیپ را در توضیح بنویسید.'}
      </div>
      <button class="btn btn-amber btn-block" type="submit">${icon('send')} ارسال درخواست</button>
      <p class="req-privacy">${icon('info')} شمارهٔ شما فقط برای پاسخ به همین درخواست استفاده می‌شود.</p>
    </form>`;
  const form = body.querySelector('form');
  form.addEventListener('submit', e => {
    e.preventDefault();
    const nameEl = form.elements['name'];
    const phoneEl = form.elements['phone'];
    const name = (nameEl.value || '').trim();
    const phone = (phoneEl.value || '').replace(/[^\d]/g, '');
    if (name.length < 3) { toast('نام را کامل وارد کنید'); nameEl.focus(); return; }
    if (!/^09\d{9}$/.test(phone)) { toast('شمارهٔ موبایل باید ۱۱ رقم و با ۰۹ شروع شود'); phoneEl.focus(); return; }
    body.innerHTML = `<div class="req-success">
      <span class="req-success-icon">${icon('check')}</span>
      <b>درخواست شما ثبت شد</b>
      <p>کارشناس پارت‌یاب تا یک روز کاری با شما تماس می‌گیرد. شمارهٔ پیگیری نمایشی: <bdi>PY-۱۴۰۵۳۱</bdi></p>
      <button class="btn btn-ghost" data-close-request>بستن</button>
    </div>`;
    toast('درخواست ثبت شد (نمایشی)', 'success');
  });
  openModal('#requestModal');
}

/* ---------------- Command palette ---------------- */
function openCmd() {
  renderCmd('');
  openModal('#cmdModal');
  const input = $('#cmdInput');
  if (input) { input.value = ''; setTimeout(() => input.focus(), 40); }
}
function renderCmd(q) {
  const box = $('#cmdResults');
  if (!box) return;
  const nq = norm(q);
  const prods = (nq ? PRODUCTS.filter(p => norm(`${p.name} ${p.brand} ${p.sku} ${p.oem} ${(p.equivalents || []).join(' ')}`).includes(nq)) : PRODUCTS.filter(p => p.featured)).slice(0, 5);
  const cars = nq ? searchModels(nq).slice(0, 3) : [];
  const pages = [
    { label: 'فروشگاه قطعات', href: 'shop.html', icon: 'box' },
    { label: 'ترمز و ABS', href: 'shop.html?cat=brake', icon: 'brake' },
    { label: 'فیلتر و روغن', href: 'shop.html?cat=filter', icon: 'filter' },
    { label: 'انتخاب خودرو', action: 'vehicle', icon: 'car' }
  ].filter(p => !nq || norm(p.label).includes(nq));

  const section = (title, items) => items.length ? `<div class="cmd-group"><div class="cmd-group-label">${title}</div>${items}</div>` : '';

  box.innerHTML = `
    ${nq ? `<div class="cmd-group"><button class="cmd-item" data-cmd-search="${esc(q)}">${icon('search')}<span>جست‌وجوی «${esc(q)}» در فروشگاه</span>${icon('chevron-left')}</button></div>` : ''}
    ${section('قطعات', prods.map(p => `<a class="cmd-item" href="product.html?id=${p.id}">${icon('box')}<span>${esc(p.name)}<small>${p.brand} · ${p.oem}</small></span>${icon('chevron-left')}</a>`).join(''))}
    ${section('خودروها', cars.map(m => `<button class="cmd-item" data-cmd-car="${m.id}">${icon('car')}<span>${esc(m.name)}<small>${esc(m.makeName)}</small></span>${icon('chevron-left')}</button>`).join(''))}
    ${section('صفحه‌ها', pages.map(p => p.href
      ? `<a class="cmd-item" href="${p.href}">${icon(p.icon)}<span>${p.label}</span>${icon('chevron-left')}</a>`
      : `<button class="cmd-item" data-open-vehicle>${icon(p.icon)}<span>${p.label}</span>${icon('chevron-left')}</button>`).join(''))}
    ${!prods.length && !cars.length && !pages.length ? `<div class="cmd-empty">${icon('search')} چیزی پیدا نشد</div>` : ''}`;
  box.querySelectorAll('.cmd-item').forEach((el, i) => { if (i === 0) el.classList.add('is-focused'); });

  box.querySelector('[data-cmd-search]')?.addEventListener('click', e => {
    location.href = `shop.html?q=${encodeURIComponent(e.currentTarget.dataset.cmdSearch)}`;
  });
  box.querySelectorAll('[data-cmd-car]').forEach(btn => btn.addEventListener('click', () => {
    picker.modelId = btn.dataset.cmdCar;
    picker.view = 'years';
    picker.year = null;
    picker.engine = '';
    closeModal('#cmdModal');
    renderPicker();
    openModal('#vehicleModal');
  }));
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

  if (!document.querySelector('.skip-link')) {
    const skip = document.createElement('a');
    skip.className = 'skip-link';
    skip.href = '#main';
    skip.textContent = 'پرش به محتوای اصلی';
    document.body.prepend(skip);
  }

  ['#vehicleModal', '#requestModal', '#cmdModal'].forEach(sel => {
    const m = $(sel);
    if (m) m.addEventListener('keydown', trapFocus);
  });

  document.addEventListener('click', e => {
    const removeBtn = e.target.closest('[data-remove-vehicle]');
    if (removeBtn) {
      e.stopPropagation();
      removeVehicle(removeBtn.dataset.removeVehicle);
      renderVehicleMenu();
      if ($('#vehicleModal').classList.contains('is-open')) renderPicker();
      toast('خودرو از گاراژ حذف شد');
      return;
    }
    const pick = e.target.closest('[data-pick-vehicle]');
    if (pick) {
      e.stopPropagation();
      setActiveByKey(pick.dataset.pickVehicle);
      toggleGarageMenu(false);
      renderVehicleMenu();
      toast('خودروی فعال تغییر کرد', 'success');
      return;
    }
    if (e.target.closest('[data-garage-toggle]')) {
      e.preventDefault();
      if (!garageLoad().vehicles.length) openVehiclePicker();
      else toggleGarageMenu();
      return;
    }
    if (e.target.closest('[data-add-vehicle]') || e.target.closest('[data-manage-garage]') || e.target.closest('[data-open-vehicle]')) {
      e.preventDefault();
      toggleGarageMenu(false);
      openVehiclePicker();
      return;
    }
    const quick = e.target.closest('[data-quick-model]');
    if (quick) {
      picker.modelId = quick.dataset.quickModel;
      picker.view = 'years';
      picker.year = null;
      picker.engine = '';
      renderPicker();
      return;
    }
    if (e.target.closest('[data-picker-back]')) {
      picker.view = 'search';
      renderPicker();
      return;
    }
    const yearBtn = e.target.closest('[data-year]');
    if (yearBtn) {
      picker.year = Number(yearBtn.dataset.year);
      renderPicker();
      const confirm = $('[data-confirm-vehicle]');
      if (confirm) confirm.disabled = false;
      return;
    }
    const engChip = e.target.closest('[data-engine]');
    if (engChip) {
      picker.engine = picker.engine === engChip.dataset.engine ? '' : engChip.dataset.engine;
      renderPicker();
      return;
    }
    if (e.target.closest('[data-confirm-vehicle]')) {
      const m = findModel(picker.modelId);
      if (!m || !picker.year) { toast('سال ساخت را انتخاب کنید'); return; }
      const v = makeVehicle(m.makeId, m.id, picker.year, picker.engine);
      addVehicle(v);
      closeModal('#vehicleModal');
      toast(`«${m.name} · ${fa(picker.year)}» به گاراژ اضافه شد`, 'success');
      return;
    }
    if (e.target.closest('[data-close-vehicle-modal]')) {
      closeModal('#vehicleModal');
      return;
    }
    if (e.target.closest('[data-close-request]')) {
      closeModal('#requestModal');
      return;
    }
    if (e.target.closest('[data-close-cmd]')) {
      closeModal('#cmdModal');
      return;
    }
    if (e.target.closest('[data-open-cmd]')) {
      openCmd();
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
      return;
    }
    if (e.target.closest('[data-request-part]')) {
      openRequestModal({ mode: 'part' });
      return;
    }
    if (e.target.closest('[data-request-fitment]')) {
      openRequestModal({ mode: 'fitment' });
      return;
    }
    if (!e.target.closest('.vehicle-wrap')) toggleGarageMenu(false);
  });

  document.addEventListener('keydown', e => {
    const isCmdK = (e.key === 'k' || e.key === 'K') && (e.metaKey || e.ctrlKey);
    if (isCmdK) { e.preventDefault(); openCmd(); return; }
    if ((e.key === 'Enter' || e.key === ' ') && e.target.matches('[data-pick-vehicle]')) {
      e.preventDefault();
      e.target.click();
      return;
    }
    if (e.key === 'Escape') {
      closeModal('#vehicleModal');
      closeModal('#requestModal');
      closeModal('#cmdModal');
      toggleGarageMenu(false);
      const d = $('#navDrawer');
      if (d) d.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  });

  const cmdInput = $('#cmdInput');
  if (cmdInput) {
    cmdInput.addEventListener('input', () => renderCmd(cmdInput.value));
    cmdInput.addEventListener('keydown', e => {
      const items = $$('.cmd-item', $('#cmdResults'));
      const focused = $('.cmd-item.is-focused', $('#cmdResults'));
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        if (!items.length) return;
        let i = items.indexOf(focused);
        i = e.key === 'ArrowDown' ? Math.min(items.length - 1, i + 1) : Math.max(0, i - 1);
        items.forEach(x => x.classList.remove('is-focused'));
        items[i].classList.add('is-focused');
        items[i].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter') {
        e.preventDefault();
        (focused || items[0])?.click();
      }
    });
  }

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

/* ---------------- Home ---------------- */
function initHero() {
  const input = $('#heroCar');
  const list = $('#heroResults');
  if (!input || !list) return;
  const yearSel = $('#heroYear');
  const engineSel = $('#heroEngine');
  const state = { model: null, year: null, engine: '' };

  const resetYear = () => {
    yearSel.innerHTML = '<option value="">سال ساخت</option>';
    yearSel.disabled = true;
    engineSel.innerHTML = '<option value="">تیپ / موتور</option>';
    engineSel.disabled = true;
  };
  resetYear();

  const selectModel = m => {
    state.model = m;
    input.value = m.name;
    const years = [];
    for (let y = m.years[1]; y >= m.years[0]; y--) years.push(y);
    yearSel.innerHTML = '<option value="">سال ساخت</option>' + years.map(y => `<option value="${y}">${fa(y)}</option>`).join('');
    yearSel.disabled = false;
    engineSel.innerHTML = '<option value="">تیپ / موتور (اختیاری)</option>' +
      m.engines.map(e => `<option value="${e}">${esc(e)}</option>`).join('');
    engineSel.disabled = false;
    yearSel.focus();
  };

  bindCombobox(input, list, selectModel);
  yearSel.addEventListener('change', () => { state.year = Number(yearSel.value) || null; });
  engineSel.addEventListener('change', () => { state.engine = engineSel.value; });

  const go = () => {
    if (!state.model) {
      toast('مدل خودرو را از فهرست انتخاب کنید');
      input.focus();
      return;
    }
    const year = Number(yearSel.value);
    if (!year) {
      toast('سال ساخت خودرو را انتخاب کنید');
      yearSel.focus();
      return;
    }
    const v = makeVehicle(state.model.makeId, state.model.id, year, engineSel.value);
    addVehicle(v);
    toast(`خودروی شما ثبت شد: ${state.model.name} · ${fa(year)}`, 'success');
    location.href = 'shop.html';
  };
  $('[data-hero-go]')?.addEventListener('click', go);
  $('[data-hero-oem]')?.addEventListener('click', e => {
    e.preventDefault();
    const s = $('[data-search-form] input');
    if (s) { s.focus(); s.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
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
  if (brands) brands.innerHTML = BRANDS.map(b => `<div class="brand-cell"><span>${b}</span></div>`).join('');

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

/* ---------------- Shop ---------------- */
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
  if (sortSel) sortSel.addEventListener('change', () => { shopState.sort = sortSel.value; renderShop(false); });

  $('[data-open-filters]')?.addEventListener('click', () => {
    $('#filtersDrawer').classList.add('is-open');
    document.body.style.overflow = 'hidden';
  });

  document.addEventListener('click', e => {
    if (e.target.closest('[data-close-filters]')) {
      $('#filtersDrawer').classList.remove('is-open');
      document.body.style.overflow = '';
    }
    const rm = e.target.closest('[data-remove-chip]');
    if (rm) {
      const { removeChip } = rm.dataset;
      if (removeChip === 'q') shopState.q = '';
      if (removeChip === 'compat') shopState.compatOnly = false;
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
    if (e.target.closest('[data-page]')) toast('صفحه‌بندی در نسخهٔ دمو فقط یک صفحه دارد');
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
        <p class="filter-hint">${icon('info')} قطعات «سازگار با شرط» هم نمایش داده می‌شوند.</p>
        ${!v ? `<button class="btn btn-ghost btn-sm btn-block" style="margin-top:10px" data-open-vehicle>${icon('garage')} انتخاب خودرو از گاراژ</button>` : ''}
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
          <input type="range" min="0" max="5000000" step="50000" value="${st.maxPrice}" data-f="price" dir="ltr" aria-label="حداکثر قیمت">
          <div class="price-inputs">
            <span class="price-max-label">تا ${money(st.maxPrice)} تومان</span>
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

function matchesQuery(p, q) {
  if (!q) return true;
  const hay = norm([p.name, p.brand, p.sku, p.oem, ...(p.equivalents || [])].join(' '));
  return hay.includes(norm(q));
}

function filteredProducts() {
  const st = shopState;
  const v = getVehicle();
  return PRODUCTS.filter(p => {
    if (!matchesQuery(p, st.q)) return false;
    if (st.cats.size && !st.cats.has(p.cat)) return false;
    if (st.brands.size && !st.brands.has(p.brand)) return false;
    if (st.stockOnly && p.stock <= 0) return false;
    if (p.price > st.maxPrice) return false;
    if (st.compatOnly && v) {
      const s = fitInfo(p, v).state;
      if (s !== 'fit' && s !== 'conditional') return false;
    }
    return true;
  }).sort((a, b) => {
    const rank = { fit: 0, conditional: 1, universal: 2, unknown: 3, ask: 4, no: 5 };
    if (st.sort === 'relevance') return rank[fitInfo(a, v).state] - rank[fitInfo(b, v).state];
    if (st.sort === 'price-asc') return a.price - b.price;
    if (st.sort === 'price-desc') return b.price - a.price;
    if (st.sort === 'rating') return b.rating - a.rating;
    if (st.sort === 'newest') return 0;
    return 0;
  });
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
        <p>${st.compatOnly && v
          ? `هیچ قطعهٔ ثبت‌شده‌ای برای ${esc(v.modelName)} با فیلترهای فعلی پیدا نشد. فیلتر «فقط سازگار» را بردارید یا درخواست بررسی بدهید.`
          : 'می‌توانید فیلترها را پاک کنید یا درخواست قطعه ثبت کنید تا کارشناس پیدا کند.'}</p>
        <div class="empty-actions">
          <button class="btn btn-amber" data-request-part>${icon('send')} ثبت درخواست قطعه</button>
          <button class="btn btn-ghost" data-clear-filters>پاک کردن فیلترها</button>
        </div>
      </div>`;
    }
  }

  const countEl = $('#resultCount');
  if (countEl) {
    countEl.innerHTML = `<b>${fa(list.length)}</b> قطعه${v && st.compatOnly ? ` سازگار با <b>${esc(v.modelName)}</b>` : ''} پیدا شد`;
  }

  /* equivalent-code notice */
  const equiv = $('#equivNotice');
  if (equiv) {
    const hits = st.q ? PRODUCTS.filter(p => (p.equivalents || []).some(code => norm(code).includes(norm(st.q)) && norm(code) !== norm(p.oem))) : [];
    if (hits.length) {
      equiv.hidden = false;
      equiv.innerHTML = `${icon('copy')} <span>کد جست‌وجوشده به‌عنوان <b>کد معادل</b> ثبت شده است:</span>
        ${hits.slice(0, 3).map(p => `<a href="product.html?id=${p.id}">${esc(p.name)} <bdi>(${p.oem})</bdi></a>`).join('')}`;
    } else {
      equiv.hidden = true;
      equiv.innerHTML = '';
    }
  }

  /* chips */
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
    const g = garageLoad();
    bar.innerHTML = v
      ? `<div class="vbar-left">
           <span class="vb-icon">${icon('car')}</span>
           <div>
             <b>خودروی فعال: ${esc(v.makeName)} ${esc(v.modelName)} · ${fa(v.year)}${v.engine ? ` · ${esc(v.engine)}` : ''}</b>
             <span>${st.compatOnly ? 'فقط قطعاتی نمایش داده می‌شود که سازگاری آن‌ها تأیید شده است.' : 'همهٔ قطعات نمایش داده می‌شود؛ نشان سازگاری روی هر کارت مشخص است.'}${g.vehicles.length > 1 ? ` · ${fa(g.vehicles.length)} خودرو در گاراژ` : ''}</span>
           </div>
         </div>
         <div class="vbar-actions">
           <button class="btn btn-ghost btn-sm" data-open-vehicle>${icon('garage')} گاراژ</button>
           <button class="btn btn-ghost btn-sm" data-remove-vehicle="${v.key}">${icon('x')} حذف</button>
         </div>`
      : `<div class="vbar-left">
           <span class="vb-icon">${icon('car')}</span>
           <div>
             <b>خودرویتان را انتخاب کنید</b>
             <span>با انتخاب خودرو، فقط قطعات سازگار را می‌بینید و از خرید اشتباه جلوگیری می‌کنید.</span>
           </div>
         </div>
         <div class="vbar-actions">
           <button class="btn btn-amber btn-sm" data-open-vehicle>${icon('garage')} انتخاب خودرو</button>
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
  if (f === 'cat') { el.checked ? shopState.cats.add(el.value) : shopState.cats.delete(el.value); renderShop(true); }
  if (f === 'brand') { el.checked ? shopState.brands.add(el.value) : shopState.brands.delete(el.value); renderShop(true); }
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
    if (e.target.closest('[data-buy]')) { addToCart(p, pdpQty); return; }
    if (e.target.closest('[data-buy-now]')) {
      addToCart(p, pdpQty, true);
      toast('در نسخهٔ واقعی، کاربر به صفحهٔ پرداخت ووکامرس منتقل می‌شود');
      return;
    }
    const copyBtn = e.target.closest('[data-copy]');
    if (copyBtn) {
      const text = copyBtn.dataset.copy;
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => toast(`کد ${text} کپی شد`, 'success')).catch(() => toast('کپی نشد؛ کد را دستی انتخاب کنید'));
      } else {
        toast(`کد: ${text}`);
      }
      return;
    }
    if (e.target.closest('[data-add-kit]')) {
      const kit = kitProducts(p);
      kit.forEach(item => addToCart(item, 1, true));
      toast(`${fa(kit.length)} قطعهٔ همراه به سبد اضافه شد`, 'success');
      return;
    }
    const tab = e.target.closest('[data-tab]');
    if (tab) {
      $$('.tab-btn').forEach(b => b.classList.toggle('is-active', b === tab));
      $$('.tab-panel').forEach(panel => panel.classList.toggle('is-active', panel.dataset.panel === tab.dataset.tab));
    }
  });
}

function kitProducts(p) {
  return (p.kit || []).map(id => PRODUCTS.find(x => x.id === id)).filter(Boolean).slice(0, 3);
}

function pdpHTML(p) {
  const v = getVehicle();
  const info = fitInfo(p, v);
  const off = p.oldPrice ? Math.round((1 - p.price / p.oldPrice) * 100) : 0;
  let related = PRODUCTS.filter(x => x.cat === p.cat && x.id !== p.id).slice(0, 4);
  if (related.length < 2) related = related.concat(PRODUCTS.filter(x => x.id !== p.id && !related.includes(x)).slice(0, 4 - related.length));

  const fits = p.fits.map(f => {
    const m = findModel(f.model);
    const mine = v && v.modelId === f.model;
    const st = f.status === 'conditional' ? 'conditional' : 'fit';
    return `<tr class="${mine ? 'is-mine' : ''}">
      <td><b>${m ? m.name : f.model}</b>${mine ? ' <span class="mine-tag">(خودروی شما)</span>' : ''}</td>
      <td>${m ? m.makeName : ''}</td>
      <td class="mono">${fa(f.from)} تا ${fa(f.to)}</td>
      <td><span class="fit-status fit-status--${st}">${icon(st === 'conditional' ? 'alert' : 'check')} ${st === 'conditional' ? 'با شرط' : 'سازگار'}</span>
        ${f.note ? `<div class="fit-note">${esc(f.note)}</div>` : ''}</td>
    </tr>`;
  }).join('');

  const fitPanel = (() => {
    if (!v && info.state !== 'universal') {
      return `<div class="fitment-panel fitment-panel--ask">
        <div class="fp-head">${icon('car')} خودروی خود را انتخاب کنید</div>
        <div class="fp-body">برای دیدن وضعیت دقیق سازگاری، خودرویتان را از گاراژ انتخاب کنید یا خودروی جدیدی اضافه کنید.</div>
        <button class="btn btn-amber btn-sm" style="margin-top:10px" data-open-vehicle>${icon('garage')} انتخاب خودرو</button>
      </div>`;
    }
    const l = FIT[info.state];
    const askCta = (info.state === 'unknown' || info.state === 'conditional' || info.state === 'no')
      ? `<div class="fp-cta">
           <button class="btn btn-ghost btn-sm" data-request-fitment>${icon('headset')} استعلام از کارشناس</button>
           ${info.state === 'no' ? `<a class="btn btn-ghost btn-sm" href="#fit">${icon('car')} دیدن خودروهای سازگار</a>` : ''}
         </div>`
      : '';
    const vehicleLine = v
      ? `<div class="fp-vehicle">${icon('car')} ${esc(v.makeName)} ${esc(v.modelName)} · ${fa(v.year)}${v.engine ? ` · ${esc(v.engine)}` : ''}</div>`
      : '';
    return `<div class="fitment-panel fitment-panel--${l.tone}">
      <div class="fp-head">${icon(l.icon)} ${l.long}</div>
      <div class="fp-body">${fitMessage(p, v)}</div>
      ${vehicleLine}
      ${askCta}
    </div>`;
  })();

  const guarantee = (info.state === 'fit' || info.state === 'conditional')
    ? `<div class="guarantee">
        <span class="g-icon">${icon('check-shield')}</span>
        <div class="g-text">
          <b>تضمین سازگاری پارت‌یاب</b>
          <span>اگر این قطعه با خودروی انتخابی شما نسازد، تا ۷ روز بازگشت رایگان.</span>
        </div>
        <a href="#" class="g-link" data-request-fitment>شرایط</a>
      </div>`
    : '';

  const kit = kitProducts(p);
  const kitSum = kit.reduce((s, x) => s + x.price, 0) + p.price;
  const kitDisc = Math.round((kitSum * 0.05) / 1000) * 1000;
  const kitHTML = kit.length ? `
    <section class="section section--tight">
      <div class="section-head">
        <div>
          <span class="eyebrow">تکمیل تعمیر</span>
          <h2 class="section-title">همراه این قطعه بخرید</h2>
          <p class="section-sub">${esc(p.kitNote || 'این قطعات معمولاً همراه هم تعویض می‌شوند.')}</p>
        </div>
      </div>
      <div class="kit">
        <div class="kit-items">
          ${[p, ...kit].map(item => `
            <a class="kit-item" href="product.html?id=${item.id}">
              <img src="assets/img/${item.img}" alt="${esc(item.name)}" loading="lazy">
              <div class="ki-text"><b>${esc(item.name)}</b><small>${item.brand} · ${money(item.price)} تومان</small></div>
              ${item.id === p.id ? '<span class="ki-tag">این قطعه</span>' : ''}
            </a>`).join('')}
        </div>
        <div class="kit-summary">
          <div class="kit-price">
            <span class="old">${money(kitSum)}</span>
            <strong>${money(kitSum - kitDisc)} <small>تومان</small></strong>
            <span class="kit-off">۵٪ تخفیف ست</span>
          </div>
          <button class="btn btn-navy btn-block" data-add-kit>${icon('cart')} افزودن همه به سبد</button>
        </div>
      </div>
    </section>` : '';

  const reviews = [
    { label: 'سازگاری', score: 4.8 },
    { label: 'کیفیت', score: p.rating },
    { label: 'نصب', score: 4.4 }
  ];
  const dist = [{ s: 5, p: 68 }, { s: 4, p: 21 }, { s: 3, p: 6 }, { s: 2, p: 3 }, { s: 1, p: 2 }];

  const codes = (p.equivalents || []).filter(c => c && c !== '—');

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
          ${fitBadge(info.state)}
          <span class="gallery-zoom">${icon('zoom')}</span>
        </div>
        <div class="buy-meta" style="border:0;padding-top:10px;margin-top:2px">
          <span>${icon('shield')} اصالت کالا</span>
          <span>${icon('truck')} ارسال ۲۴ ساعته</span>
          <span>${icon('refresh')} ۷ روز بازگشت</span>
        </div>
        ${codes.length ? `
        <div class="codes-card">
          <div class="codes-head">${icon('copy')} کدهای فنی معادل <span>${fa(codes.length)} کد</span></div>
          <div class="codes-list">
            ${codes.map(c => `<button class="code-chip" data-copy="${esc(c)}" title="کپی ${esc(c)}">${esc(c)} ${icon('copy')}</button>`).join('')}
          </div>
          <p class="codes-note">اگر کد قدیمی قطعه را دارید، با یکی از این کدها جست‌وجو کنید.</p>
        </div>` : ''}
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
        ${guarantee}

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
              <button data-qty="+" aria-label="افزودن تعداد">${icon('plus')}</button>
              <span class="qty-val">${fa(pdpQty)}</span>
              <button data-qty="-" aria-label="کاهش تعداد">${icon('minus')}</button>
            </div>
            <button class="btn btn-amber" data-buy>${icon('cart')} افزودن به سبد خرید</button>
          </div>
          <div class="buy-meta">
            <span>${icon('shield')} ضمانت اصالت</span>
            <span>${icon('truck')} ارسال سراسری</span>
            <span>${icon('refresh')} بازگشت ۷ روزه</span>
          </div>
        </div>
      </div>
    </div>

    <div class="tabs-wrap" id="fit">
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
            <li>سازگاری اعلام‌شده بر اساس داده‌ای است که فروشگاه ثبت کرده؛ وضعیت «با شرط» را حتماً بخوانید.</li>
            <li>در صورت نبود اطلاعات برای خودروی شما، سفارش را نهایی نکنید و از پشتیبانی استعلام بگیرید.</li>
          </ul>
        </div>
      </div>

      <div class="tab-panel" data-panel="specs">
        <table class="spec-table">
          <tbody>
            ${Object.entries(p.specs).map(([k, val]) => `<tr><th>${k}</th><td>${val}</td></tr>`).join('')}
            <tr><th>کدهای معادل</th><td>${codes.length ? codes.join(' · ') : 'ثبت نشده'}</td></tr>
          </tbody>
        </table>
      </div>

      <div class="tab-panel" data-panel="fit">
        ${p.fitmentKnown ? `
          <p class="prose" style="margin-bottom:12px">این محصول برای خودروهای زیر در فروشگاه ثبت شده است. بازهٔ سال شامل هر دو سال ابتدا و انتها است.</p>
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
              <p>فروشنده هنوز فهرست خودروهای سازگار را وارد نکرده است. پیش از خرید استعلام بگیرید.</p>
              <button class="btn btn-ghost" data-request-fitment>${icon('headset')} استعلام سازگاری</button>
            </div>`}
      </div>

      <div class="tab-panel" data-panel="install">
        <div class="prose">
          <h4>راهنمای نصب</h4>
          <ul>
            <li>پیش از نصب، باتری خودرو را جدا کنید و اجازه دهید قطعات خنک شوند.</li>
            <li>قطعهٔ قدیمی را باز کنید و سطح نشیمن را از آلودگی و زنگ پاک کنید.</li>
            <li>گشتاور پیچ‌ها را طبق دفترچهٔ خودرو اعمال کنید؛ سفت‌کردن بیش از حد به قطعه آسیب می‌زند.</li>
            <li>پس از نصب، در مسیر خلوت آزمایش کنید.</li>
          </ul>
          <p style="font-size:11.5px;color:var(--muted)">${icon('alert')} نصب توسط تعمیرکار مجاز توصیه می‌شود؛ این راهنما جایگزین دفترچهٔ خودرو نیست.</p>
        </div>
      </div>

      <div class="tab-panel" data-panel="reviews">
        <div class="reviews">
          <div class="reviews-summary">
            <div class="rs-score"><b>${fa(p.rating)}</b><span>از ۵</span><small>${fa(p.reviews)} دیدگاه</small></div>
            <div class="rs-bars">
              ${dist.map(d => `<div class="rs-bar"><span>${fa(d.s)} ★</span><div class="rs-track"><i style="width:${d.p}%"></i></div><em>${fa(d.p)}٪</em></div>`).join('')}
            </div>
          </div>
          <div class="reviews-criteria">
            ${reviews.map(r => `<div class="rc-item"><span>${r.label}</span><b>${fa(r.score)}</b></div>`).join('')}
          </div>
          <p class="reviews-note">${icon('info')} در نسخهٔ واقعی، دیدگاه‌ها فقط از خریداران تأییدشده ثبت می‌شود.</p>
        </div>
      </div>
    </div>

    ${kitHTML}

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

/* ---------------- Boot ---------------- */
document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  const page = document.body.dataset.page;
  if (page === 'home') { initHero(); initHome(); }
  if (page === 'shop') initShop();
  if (page === 'product') initProduct();
  hydrateIcons();
});

window.pyToast = toast;
window.hydrateIcons = hydrateIcons;
