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
  layers: '<path d="m12 3.4 8.4 4.4-8.4 4.4-8.4-4.4 8.4-4.4Z"/><path d="m3.6 12 8.4 4.4 8.4-4.4M3.6 16.4 12 20.8l8.4-4.4"/>',
  'arrow-left': '<path d="M19.5 12H5"/><path d="m11 5.8-6.2 6.2 6.2 6.2"/>',
  'arrow-right': '<path d="M4.5 12H19"/><path d="m13 5.8 6.2 6.2L13 18.2"/>',
  'chevron-down': '<path d="m6.2 9.2 5.8 5.8 5.8-5.8"/>',
  'chevron-left': '<path d="M14.8 6.2 9 12l5.8 5.8"/>',
  'chevron-right': '<path d="M9.2 6.2 15 12l-5.8 5.8"/>',
  'map-pin': '<path d="M12 21.3s6.5-6.2 6.5-10.6a6.5 6.5 0 1 0-13 0c0 4.4 6.5 10.6 6.5 10.6Z"/><circle cx="12" cy="10.4" r="2.4"/>'
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
const QUALITY = {
  orig:  { label: 'اورجینال', hint: 'اصالت کارخانه', cls: 'q--orig' },
  after: { label: 'شرکتی',   hint: 'تولید برند معتبر', cls: 'q--after' },
  plan:  { label: 'طرح',     hint: 'کپی باکیفیت', cls: 'q--plan' },
  used:  { label: 'استوک',   hint: 'کارکردهٔ سالم', cls: 'q--used' }
};
const ENGINES = ['TU5', 'XU7', 'EF7', 'K4M'];
const SHOP_PHONE = '+982133947195';
const SHOP_PHONE_FA = '۰۲۱-۳۳۹۴۷۱۹۵';
const SHOP_WA = 'https://wa.me/989121234567';

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
  const wrap = $('#vehicleSlot');
  const menu = $('#vehicleMenu');
  if (!wrap || !menu || menu.hidden) return;
  const w = Math.min(340, window.innerWidth - 24);
  const r = wrap.getBoundingClientRect();
  const left = Math.max(12, Math.min(r.left, window.innerWidth - 12 - w));
  menu.style.width = w + 'px';
  menu.style.insetInlineStart = 'auto';
  menu.style.insetInlineEnd = 'auto';
  menu.style.left = (left - r.left) + 'px';
}

function coverageFor(v) {
  if (!v) return null;
  const total = PRODUCTS.length;
  const ok = PRODUCTS.filter(p => ['fit', 'conditional', 'universal'].includes(fitInfo(p, v).state)).length;
  return { ok, total, pct: Math.round((ok / total) * 100) };
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

/* ---------------- Cart (line items) ---------------- */
const KEY_CART = 'py_cart_v2';
const KEY_CART_LEGACY = 'py_cart_v1';
let cartAck = false;

function cartLoad() {
  let items = [];
  try { items = JSON.parse(localStorage.getItem(KEY_CART) || '[]'); } catch (err) { items = []; }
  if (!Array.isArray(items)) items = [];
  items = items
    .filter(it => it && PRODUCTS.some(p => p.id === it.id) && Number(it.qty) > 0)
    .map(it => ({ id: it.id, qty: Math.min(99, Math.max(1, Math.round(Number(it.qty) || 1))) }));
  if (!items.length) {
    const legacy = Number(localStorage.getItem(KEY_CART_LEGACY) || 0);
    if (legacy > 0) {
      items = PRODUCTS.slice(0, Math.min(legacy, 3)).map(p => ({ id: p.id, qty: 1 }));
      localStorage.removeItem(KEY_CART_LEGACY);
      localStorage.setItem(KEY_CART, JSON.stringify(items));
    }
  }
  return items;
}
function cartSave(items) {
  const clean = items
    .filter(it => Number(it.qty) > 0)
    .map(it => ({ id: it.id, qty: Math.min(99, Math.round(it.qty)) }));
  localStorage.setItem(KEY_CART, JSON.stringify(clean));
  cartAck = false;
  cartBadge();
  return clean;
}
const cartLines = () => cartLoad()
  .map(it => ({ ...it, product: PRODUCTS.find(p => p.id === it.id) }))
  .filter(it => it.product);
const cartCount = () => cartLoad().reduce((s, it) => s + it.qty, 0);
const cartTotals = () => cartLines().reduce((t, it) => {
  t.qty += it.qty;
  t.sum += it.product.price * it.qty;
  t.old += (it.product.oldPrice || it.product.price) * it.qty;
  return t;
}, { qty: 0, sum: 0, old: 0 });

function cartBadge() {
  const n = cartCount();
  $$('.cart-count').forEach(el => {
    el.textContent = fa(n);
    el.classList.toggle('is-zero', n === 0);
    el.style.animation = 'none';
    void el.offsetWidth;
    el.style.animation = 'pop .35s ease';
  });
}
function addToCart(p, qty = 1, silent = false) {
  if (p.priceOnRequest) {
    toast('قیمت این قطعه استعلامی است؛ برای ثبت سفارش تماس بگیرید', 'warn');
    return;
  }
  const items = cartLoad();
  const line = items.find(it => it.id === p.id);
  if (line) line.qty = Math.min(99, line.qty + qty);
  else items.push({ id: p.id, qty: Math.min(99, qty) });
  cartSave(items);
  const state = fitInfo(p, getVehicle()).state;
  if (!silent) {
    const warn = state === 'no' ? ' — با خودروی شما سازگار نیست' : state === 'conditional' ? ' — سازگار با شرط' : '';
    toast(`«${p.name}» به سبد اضافه شد${warn}`, state === 'no' ? 'info' : 'success');
    openCart();
  }
  renderCart();
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
  const fitText = (() => {
    if (info.state === 'fit') return v ? `سازگار با ${esc(v.modelName)} ${fa(v.year)}` : 'سازگار';
    if (info.state === 'conditional') return `سازگار با شرط${info.note ? ` · ${esc(info.note)}` : ''}`;
    if (info.state === 'no') return `ناسازگار با ${esc(v.modelName)}`;
    if (info.state === 'unknown') return 'سازگاری ثبت نشده';
    if (info.state === 'universal') return 'عمومی برای همهٔ خودروها';
    return 'سازگاری را بررسی کنید';
  })();
  const eq = (p.equivalents || []).filter(c => c && c !== '—').length;
  const q = QUALITY[p.quality];
  const askPrice = !!p.priceOnRequest;
  return `
  <article class="p-card ${info.state === 'no' ? 'p-card--dim' : ''}" data-product="${p.id}">
    <span class="p-rail p-rail--${info.state}" aria-hidden="true"></span>
    <a class="p-media" href="product.html?id=${p.id}" aria-label="${esc(p.name)}">
      <img src="assets/img/${p.img}" alt="${esc(p.name)}" loading="lazy">
      <span class="p-code">${esc(p.oem)}</span>
      ${off ? `<span class="off-badge">−${fa(off)}٪</span>` : ''}
    </a>
    <div class="p-body">
      <div class="p-head">
        <span class="p-brand">${p.brand}</span>
        ${q ? `<span class="p-q ${q.cls}" title="${q.hint}">${q.label}</span>` : ''}
        ${eq > 1 ? `<span class="p-equiv">${icon('copy')} ${fa(eq)} کد</span>` : ''}
      </div>
      <h3 class="p-title"><a href="product.html?id=${p.id}">${p.name}</a></h3>
      <div class="p-fit p-fit--${info.state}">${icon(FIT[info.state].icon)} ${fitText}</div>
      <div class="p-foot">
        ${askPrice ? `<div class="p-price p-price--ask">
          <span class="p-ask">استعلام قیمت</span>
          <small>قیمت این قطعه نوسانی است</small>
        </div>
        <a class="p-call" href="tel:${SHOP_PHONE}" aria-label="تماس برای استعلام قیمت ${esc(p.name)}">${icon('phone')}</a>` : `<div class="p-price">
          ${p.oldPrice ? `<span class="old">${money(p.oldPrice)}</span>` : ''}
          <strong>${money(p.price)} <small>تومان</small></strong>
        </div>
        <button class="p-add" data-add="${p.id}" aria-label="افزودن ${esc(p.name)} به سبد خرید">${icon('plus')}</button>`}
      </div>
    </div>
  </article>`;
}

function headerHTML() {
  const page = document.body.dataset.page;
  const link = (href, label, key) =>
    `<a href="${href}" class="${page === key ? 'is-active' : ''}">${label}</a>`;
  return `
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

      <div class="header-search">
        <form role="search" data-search-form>
          <input type="search" name="q" placeholder="کد فنی، نام قطعه یا برند" aria-label="جست‌وجو">
          <button class="search-kbd" type="button" data-open-cmd aria-label="جست‌وجوی سریع"><kbd dir="ltr">⌘K</kbd></button>
          <button class="search-submit" type="submit" aria-label="جست‌وجو">${icon('search')}<span>جست‌وجو</span></button>
        </form>
      </div>

      <div class="header-actions">
        <button class="nav-toggle icon-btn" data-open-nav aria-label="منو">${icon('menu')}</button>
        <a class="icon-btn" href="garage.html" aria-label="حساب کاربری و گاراژ من">${icon('user')}</a>
        <button class="cart-btn" data-cart aria-label="سبد خرید">
          ${icon('cart')}<span class="cart-label">سبد خرید</span>
          <span class="cart-count">${fa(cartCount())}</span>
        </button>
      </div>
    </div>

    <div class="passport-bar">
      <div class="container passport-inner">
        <div class="passport-slot" id="vehicleSlot"></div>
        <nav class="passport-nav" aria-label="ناوبری اصلی">
          ${link('index.html', 'خانه', 'home')}
          ${link('shop.html', 'فروشگاه قطعات', 'shop')}
          ${link('code.html', 'کد فنی', 'code')}
          ${link('shop.html?cat=filter', 'فیلتر و روغن')}
          ${link('shop.html?cat=brake', 'ترمز')}
          ${link('shop.html?cat=suspension', 'جلوبندی')}
          ${link('index.html#guides', 'راهنمای خرید')}
        </nav>
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
        <a class="drawer-link" href="code.html">${icon('search')} جست‌وجوی کد فنی</a>
        <a class="drawer-link" href="garage.html">${icon('garage')} گاراژ من</a>
        <a class="drawer-link" href="shop.html?cat=brake">${icon('brake')} ترمز و ABS</a>
        <a class="drawer-link" href="shop.html?cat=filter">${icon('filter')} فیلتر و روغن</a>
        <a class="drawer-link" href="shop.html?cat=suspension">${icon('shock')} جلوبندی و تعلیق</a>
        <a class="drawer-link" href="shop.html?cat=electrical">${icon('light')} برق و روشنایی</a>
        <a class="drawer-link" href="index.html#guides">${icon('file')} راهنمای خرید</a>
        <a class="drawer-link" href="trust.html">${icon('shield')} ضمانت و بازگشت</a>
        <a class="drawer-link" href="garage.html">${icon('user')} حساب کاربری و گاراژ</a>
      </div>
      <div class="drawer-foot">
        <a class="btn btn-amber btn-block" href="tel:${SHOP_PHONE}">${icon('phone')} تماس با پشتیبانی</a>
      </div>
    </aside>
  </div>

  <div class="modal modal--sheet" id="vehicleModal" aria-hidden="true">
    <div class="modal-backdrop" data-close-vehicle-modal></div>
    <div class="modal-card sheet-card" role="dialog" aria-modal="true" aria-labelledby="vmTitle">
      <div class="sheet-head">
        <div>
          <span class="sheet-eyebrow">${icon('garage')} پاسپورت خودرو</span>
          <h3 id="vmTitle">انتخاب خودرو</h3>
        </div>
        <button class="modal-close" data-close-vehicle-modal aria-label="بستن">${icon('x')}</button>
      </div>
      <div class="modal-body sheet-body" id="vehicleModalBody"></div>
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

  <div class="drawer drawer--cart" id="cartDrawer" aria-hidden="true">
    <div class="drawer-backdrop" data-close-cart></div>
    <aside class="drawer-panel cart-panel" role="dialog" aria-modal="true" aria-label="سبد خرید">
      <div class="drawer-head">
        <b>سبد خرید</b>
        <span class="cart-head-note" id="cartHeadNote"></span>
        <button class="modal-close" data-close-cart aria-label="بستن">${icon('x')}</button>
      </div>
      <div class="cart-passport" id="cartPassport"></div>
      <div class="drawer-body cart-body" id="cartBody"></div>
      <div class="drawer-foot cart-foot" id="cartFoot"></div>
    </aside>
  </div>

  <nav class="mobile-nav" aria-label="ناوبری موبایل">
    <a href="index.html" class="${document.body.dataset.page === 'home' ? 'is-active' : ''}">${icon('grid')}<span>خانه</span></a>
    <a href="shop.html" class="${document.body.dataset.page === 'shop' ? 'is-active' : ''}">${icon('box')}<span>دسته‌ها</span></a>
    <a href="garage.html" class="${document.body.dataset.page === 'garage' ? 'is-active' : ''}">${icon('garage')}<span>گاراژ</span></a>
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
          <a href="#" data-soon="صفحهٔ اینستاگرام پارت‌یاب به‌زودی" aria-label="اینستاگرام">${icon('instagram')}</a>
          <a href="#" data-soon="کانال تلگرام پارت‌یاب به‌زودی" aria-label="تلگرام">${icon('telegram')}</a>
          <a href="${SHOP_WA}" target="_blank" rel="noopener" aria-label="واتساپ پارت‌یاب">${icon('whatsapp')}</a>
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
        <a href="garage.html">گاراژ من</a>
        <a href="index.html#guides">انتخاب خودرو</a>
        <a href="code.html">جست‌وجوی کد فنی</a>
        <a href="index.html#guides">کد فنی یا OEM چیست؟</a>
        <a href="index.html#states">معنی نشان‌های سازگاری</a>
        <a href="trust.html#shipping">شرایط ارسال</a>
        <a href="trust.html#returns">بازگشت کالا</a>
      </div>
      <div class="footer-col footer-col--hide">
        <b>پارت‌یاب</b>
        <a href="trust.html#about">دربارهٔ ما</a>
        <a href="trust.html#support">تماس با ما</a>
        <a href="trust.html#rules">قوانین و مقررات</a>
        <a href="trust.html#privacy">حریم خصوصی</a>
        <a href="trust.html#support">همکاری با ما</a>
      </div>
      <div class="footer-col">
        <b>ارتباط با ما</b>
        <ul class="footer-contact">
          <li>${icon('map-pin')}<span>تهران، خیابان ولیعصر، پلاک ۱۲۴۵، طبقهٔ سوم</span></li>
          <li>${icon('phone')}<span>${SHOP_PHONE_FA}</span></li>
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
function passportHTML() {
  const v = getVehicle();
  const g = garageLoad();
  const count = g.vehicles.length;

  if (!v) {
    return `<button class="pp pp--empty" data-open-vehicle aria-label="ثبت خودرو">
      <span class="pp-plate pp-plate--empty">${icon('car')}</span>
      <span class="pp-id">
        <small>پلاک خودرو خالی است</small>
        <b>خودرو خود را ثبت کنید</b>
      </span>
      <span class="pp-cov">با ثبت خودرو فقط قطعات سازگار نمایش داده می‌شود</span>
      <span class="pp-btn pp-btn--amber">${icon('plus')} ثبت خودرو</span>
    </button>`;
  }

  const cov = coverageFor(v);
  return `<div class="pp is-set">
    <button class="pp-main" data-garage-toggle aria-expanded="false" aria-haspopup="true" aria-label="گاراژ من">
      <span class="pp-plate">${icon('car')}</span>
      <span class="pp-id">
        <small>خودروی من${count > 1 ? ` · ${fa(count)} خودرو` : ''}</small>
        <b>${esc(v.modelName)} <span class="pp-year">${fa(v.year)}</span>${v.engine ? ` <span class="pp-eng">${esc(v.engine)}</span>` : ''}</b>
      </span>
      <span class="pp-chev">${icon('chevron-down')}</span>
    </button>
    <span class="pp-meter" role="img" aria-label="پوشش کاتالوگ ${fa(cov.pct)} درصد"><i style="width:${cov.pct}%"></i></span>
    <span class="pp-cov">${fa(cov.ok)} از ${fa(cov.total)} قطعه سازگار</span>
    <a class="pp-btn pp-btn--link" href="garage.html">${icon('garage')} گاراژ</a>
    <button class="pp-btn" data-open-vehicle>${icon('refresh')} تغییر</button>
  </div>`;
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
    slot.innerHTML = passportHTML() + `<div class="vehicle-menu" id="vehicleMenu" hidden>${vehicleMenuHTML()}</div>`;
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

function pickerStepsHTML(active) {
  const items = [['۱', 'مدل'], ['۲', 'سال'], ['۳', 'تیپ']];
  return `<ol class="pp-steps" aria-label="مراحل انتخاب خودرو">${items.map((it, i) => {
    const n = i + 1;
    const cls = n < active ? 'is-done' : n === active ? 'is-current' : '';
    return `<li class="${cls}"><span>${n < active ? icon('check') : it[0]}</span>${it[1]}</li>`;
  }).join('')}</ol>`;
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
    ${pickerStepsHTML(1)}
    ${garage}
    <div class="picker-search">
      <label class="picker-label" for="vSearchInput">${icon('search')} مدل خودرو را بنویسید</label>
      <div class="combo combo--inline" id="pickerCombo">
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
    ${pickerStepsHTML(picker.year ? 3 : 2)}
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
  const render = (q, open = true) => {
    list.innerHTML = comboResultsHTML(q);
    items = $$('.combo-opt', list);
    if (!open) return;
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
  render('', false);
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
      closeCart();
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
      if (p && p.priceOnRequest) {
        toast('قیمت این قطعه استعلامی است؛ برای ثبت سفارش تماس بگیرید', 'warn');
        return;
      }
      if (p) addToCart(p);
      return;
    }
    if (e.target.closest('[data-cart]')) {
      openCart();
      return;
    }
    if (e.target.closest('[data-request-part]')) {
      openRequestModal({ mode: 'part' });
      return;
    }
    if (e.target.closest('[data-request-fitment]')) {
      closeCart();
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
      if ($('#cartDrawer')?.classList.contains('is-open')) closeCart();
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
    if ($('#cartDrawer')) renderCart();
    if (document.body.dataset.page === 'checkout') renderCheckout();
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

/* countdown to local midnight, used by the flash-sale band */
function startFlashClock() {
  const el = $('[data-flash-clock]');
  if (!el) return;
  const pad = n => fa(String(n).padStart(2, '0'));
  const tick = () => {
    const now = new Date();
    const end = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1, 0, 0, 0);
    const left = Math.max(0, Math.floor((end - now) / 1000));
    el.querySelector('[data-cd=h]').textContent = pad(Math.floor(left / 3600));
    el.querySelector('[data-cd=m]').textContent = pad(Math.floor((left % 3600) / 60));
    el.querySelector('[data-cd=s]').textContent = pad(left % 60);
  };
  tick();
  setInterval(tick, 1000);
}

function initHome() {
  const grid = $('#featuredGrid');
  if (grid) {
    const list = [...PRODUCTS].filter(p => p.featured).concat(PRODUCTS.filter(p => !p.featured)).slice(0, 8);
    grid.innerHTML = list.map(productCard).join('');
  }
  const flashGrid = $('#flashGrid');
  if (flashGrid) {
    const deals = [...PRODUCTS]
      .filter(p => p.oldPrice && p.oldPrice > p.price)
      .sort((a, b) => (1 - b.price / b.oldPrice) - (1 - a.price / a.oldPrice))
      .slice(0, 4);
    flashGrid.innerHTML = (deals.length ? deals : PRODUCTS.slice(0, 4)).map(productCard).join('');
    startFlashClock();
  }
  const catGrid = $('#catGrid');
  if (catGrid) {
    catGrid.innerHTML = CATEGORIES.map((c, i) => {
      const count = PRODUCTS.filter(p => p.cat === c.id).length;
      return `<a class="cat-card" href="shop.html?cat=${c.id}">
        <span class="cat-num">${fa(String(i + 1).padStart(2, '0'))}</span>
        <span class="cat-icon">${icon(c.icon)}</span>
        <span class="cat-name">${c.name}</span>
        <span class="cat-lead" aria-hidden="true"></span>
        <span class="cat-count">${count ? `${fa(count * 37)} قطعه` : 'به‌زودی'}</span>
        <span class="cat-arrow">${icon('chevron-left')}</span>
      </a>`;
    }).join('');
  }
  const brands = $('#brandStrip');
  if (brands) brands.innerHTML = BRANDS.map(b => `<div class="brand-cell"><span>${b}</span></div>`).join('');

  const guides = $('#guideGrid');
  if (guides) {
    guides.innerHTML = GUIDES.map(g => `<a class="guide-card" href="#" data-soon="مقالهٔ «${g.title}» به‌زودی منتشر می‌شود">
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
    quality: new Set((params.get('quality') || '').split(',').filter(Boolean)),
    engines: new Set((params.get('engine') || '').split(',').filter(Boolean)),
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
      if (removeChip.startsWith('quality:')) shopState.quality.delete(removeChip.slice(8));
      if (removeChip.startsWith('engine:')) shopState.engines.delete(removeChip.slice(7));
      if (removeChip === 'stock') shopState.stockOnly = false;
      renderFilters();
      renderShop(true);
    }
    if (e.target.closest('[data-clear-filters]')) {
      shopState.cats.clear();
      shopState.brands.clear();
      shopState.quality.clear();
      shopState.engines.clear();
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
      <div class="f-group-title">کیفیت قطعه</div>
      <div class="f-group-body">
        ${Object.keys(QUALITY).map(k => {
          const n = PRODUCTS.filter(p => p.quality === k).length;
          if (!n) return '';
          return `<label class="check check--q">
            <input type="checkbox" data-f="quality" value="${k}" ${st.quality.has(k) ? 'checked' : ''}>
            <span class="q-dot ${QUALITY[k].cls}"></span>
            <span>${QUALITY[k].label}</span><span class="count">${fa(n)}</span>
          </label>`;
        }).join('')}
      </div>
    </div>
    <div class="f-group is-open">
      <div class="f-group-title">تیپ / موتور</div>
      <div class="f-group-body">
        ${ENGINES.map(en => {
          const n = PRODUCTS.filter(p => (p.engines || []).includes(en)).length;
          return `<label class="check">
            <input type="checkbox" data-f="engine" value="${en}" ${st.engines.has(en) ? 'checked' : ''}>
            <span class="mono" style="font-size:11px">${en}</span><span class="count">${fa(n)}</span>
          </label>`;
        }).join('')}
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
  /* بازسازی پنل فیلتر نباید اسکرول و فوکوس کاربر را از بین ببرد */
  const act = document.activeElement;
  const key = act && act.dataset && act.dataset.f
    ? `[data-f="${act.dataset.f}"]${act.value ? `[value="${act.value}"]` : ''}`
    : null;
  ['#filtersDesktop', '#filtersDrawerBody'].forEach(sel => {
    const box = $(sel);
    if (!box) return;
    const top = box.scrollTop;
    box.innerHTML = filterHTML();
    box.scrollTop = top;
  });
  if (key) {
    const el = document.querySelector(key);
    if (el) el.focus({ preventScroll: true });
  }
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
    if (st.quality.size && !st.quality.has(p.quality)) return false;
    if (st.engines.size && !(p.engines || []).some(en => st.engines.has(en))) return false;
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
    st.quality.forEach(q => items.push(`<span class="chip">${QUALITY[q].label} <button data-remove-chip="quality:${q}" aria-label="حذف">${icon('x')}</button></span>`));
    st.engines.forEach(en => items.push(`<span class="chip mono" style="font-size:11px">${en} <button data-remove-chip="engine:${en}" aria-label="حذف">${icon('x')}</button></span>`));
    if (st.stockOnly) items.push(`<span class="chip">موجود در انبار <button data-remove-chip="stock" aria-label="حذف">${icon('x')}</button></span>`);
    if (items.length) items.push('<button class="chip chip--clear" data-clear-filters>حذف همه</button>');
    chips.innerHTML = items.join('');
    chips.style.display = items.length ? 'flex' : 'none';
  }

  /* vehicle bar */
  const bar = $('#vehicleBar');
  if (bar) {
    bar.className = v ? 'vbar' : 'vbar vbar--empty';
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
  if (f === 'quality') { el.checked ? shopState.quality.add(el.value) : shopState.quality.delete(el.value); renderShop(true); }
  if (f === 'engine') { el.checked ? shopState.engines.add(el.value) : shopState.engines.delete(el.value); renderShop(true); }
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
  if (related.length < 4) related = related.concat(PRODUCTS.filter(x => x.id !== p.id && !related.includes(x)).slice(0, 4 - related.length));

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
              <div class="ki-text"><b>${esc(item.name)}</b><small>${item.brand}</small></div>
              ${item.id === p.id ? '<span class="ki-tag">این قطعه</span>' : ''}
              <span class="ki-price">${money(item.price)} <small>تومان</small></span>
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

      </div>

      <div class="pdp-info">
        <div class="pdp-top">
          <div class="pdp-brandline">
            <span class="brand-chip">${p.brand}</span>
            ${QUALITY[p.quality] ? `<span class="p-q ${QUALITY[p.quality].cls}">${QUALITY[p.quality].label}</span>` : ''}
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
          <div class="code-cell"><span>کد فنی <bdi dir="ltr">OEM</bdi></span><b>${p.oem}</b></div>
          <div class="code-cell"><span>کد کالا <bdi dir="ltr">SKU</bdi></span><b>${p.sku}</b></div>
          <div class="code-cell"><span>برند</span><b style="font-family:Vazirmatn;direction:ltr">${p.brand}</b></div>
        </div>

        ${fitPanel}
        ${guarantee}

        <div class="buybox">
          <div class="buybox-row">
            <div class="price-big">
              ${p.priceOnRequest ? `<span class="p-ask">استعلام قیمت</span>
              <div class="stock-line">قیمت این قطعه نوسانی است؛ برای آخرین قیمت تماس بگیرید.</div>` : `${p.oldPrice ? `<span class="old">${money(p.oldPrice)} تومان</span>` : ''}
              <strong>${money(p.price)} <small>تومان</small></strong>
              <div class="stock-line">${p.stock < 6 ? `فقط ${fa(p.stock)} عدد در انبار` : 'موجود در انبار فروشگاه'}</div>`}
            </div>
            ${off && !p.priceOnRequest ? `<span class="off-tag">${fa(off)}٪ تخفیف</span>` : ''}
          </div>
          ${p.priceOnRequest ? `<div class="ask-actions">
            <a class="btn btn-navy" href="tel:${SHOP_PHONE}">${icon('phone')} تماس و استعلام</a>
            <a class="btn btn-ghost" href="${SHOP_WA}" target="_blank" rel="noopener">${icon('whatsapp')} واتساپ</a>
            <span class="ask-note">${icon('clock')} پاسخ در ساعات کاری · ${SHOP_PHONE_FA}</span>
          </div>` : `<div class="buy-actions">
            <div class="qty">
              <button data-qty="+" aria-label="افزودن تعداد">${icon('plus')}</button>
              <span class="qty-val">${fa(pdpQty)}</span>
              <button data-qty="-" aria-label="کاهش تعداد">${icon('minus')}</button>
            </div>
            <button class="btn btn-amber" data-buy>${icon('cart')} افزودن به سبد خرید</button>
          </div>`}
          <div class="buy-meta">
            <a href="trust.html">${icon('shield')} ضمانت اصالت</a>
            <a href="trust.html">${icon('truck')} ارسال سراسری</a>
            <a href="trust.html">${icon('refresh')} بازگشت ۷ روزه</a>
          </div>
        </div>

        ${codes.length ? `
        <div class="codes-card">
          <div class="codes-head">${icon('copy')} کدهای فنی معادل <span>${fa(codes.length)} کد</span></div>
          <div class="codes-list">
            ${codes.map(c => `<button class="code-chip" data-copy="${esc(c)}" title="کپی ${esc(c)}">${esc(c)} ${icon('copy')}</button>`).join('')}
          </div>
          <p class="codes-note">اگر کد قدیمی قطعه را دارید، در <a href="code.html?code=${encodeURIComponent(p.oem || codes[0] || '')}">قطعه‌یاب کد فنی</a> جست‌وجو کنید تا معادل‌ها و سازگاری را ببینید.</p>
        </div>` : ''}

        ${(p.engines || []).length ? `
        <div class="codes-card">
          <div class="codes-head">${icon('engine')} مناسب این تیپ‌ها <span>${fa(p.engines.length)} تیپ</span></div>
          <div class="codes-list">
            ${p.engines.map(en => `<a class="code-chip" href="shop.html?engine=${encodeURIComponent(en)}" title="دیدن قطعات تیپ ${esc(en)}">${esc(en)} ${icon('arrow-left')}</a>`).join('')}
          </div>
          <p class="codes-note">تیپ موتور روی سازگاری اثر می‌گذارد؛ با انتخاب تیپ، قطعات همان موتور را ببینید.</p>
        </div>` : ''}
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
    ${p.priceOnRequest
      ? `<div class="price-big"><span class="p-ask">استعلام قیمت</span></div>
         <a class="btn btn-amber" href="tel:${SHOP_PHONE}">${icon('phone')} تماس برای قیمت</a>`
      : `<div class="price-big">
           ${p.oldPrice ? `<span class="old">${money(p.oldPrice)}</span>` : ''}
           <strong>${money(p.price)} <small>تومان</small></strong>
         </div>
         <button class="btn btn-amber" data-buy>${icon('cart')} افزودن به سبد</button>`}
  </div>`;
}

function renderPDP() {
  const id = new URLSearchParams(location.search).get('id') || PRODUCTS[0].id;
  const p = PRODUCTS.find(x => x.id === id) || PRODUCTS[0];
  $('#pdpRoot').innerHTML = pdpHTML(p);
  document.title = `${p.name} | پارت‌یاب`;
}

/* ---------------- Cart drawer ---------------- */
function openCart() {
  const d = $('#cartDrawer');
  if (!d) return;
  renderCart();
  d.classList.add('is-open');
  d.setAttribute('aria-hidden', 'false');
  document.body.style.overflow = 'hidden';
  const close = $('.modal-close', d);
  if (close) close.focus({ preventScroll: true });
}
function closeCart() {
  const d = $('#cartDrawer');
  if (!d) return;
  d.classList.remove('is-open');
  d.setAttribute('aria-hidden', 'true');
  document.body.style.overflow = '';
}

function cartFitText(p, v, long = true) {
  const info = fitInfo(p, v);
  const car = v ? `${v.modelName} ${fa(v.year)}` : '';
  if (info.state === 'fit') return long ? `سازگار با ${car}` : `سازگار با ${v.modelName}`;
  if (info.state === 'conditional') return info.note ? `سازگار با شرط: ${info.note}` : 'سازگار با شرط';
  if (info.state === 'no') return `ناسازگار با ${car}`;
  if (info.state === 'unknown') return 'سازگاری ثبت نشده — نامشخص، نه ناسازگار';
  return 'قطعهٔ عمومی برای همهٔ خودروها';
}

function cartIssueCount(lines, v) {
  return lines.reduce((acc, it) => {
    const st = fitInfo(it.product, v).state;
    acc[st] = (acc[st] || 0) + 1;
    return acc;
  }, {});
}

function cartCanCheckout(lines, v) {
  if (!lines.length) return false;
  const bad = lines.some(it => fitInfo(it.product, v).state === 'no');
  return !bad || cartAck;
}

function cartRowHTML(it, v) {
  const p = it.product;
  const info = fitInfo(p, v);
  return `
  <article class="cart-row" data-line="${p.id}">
    <span class="p-rail p-rail--${info.state}" aria-hidden="true"></span>
    <a class="cart-thumb" href="product.html?id=${p.id}" tabindex="-1" aria-hidden="true">
      <img src="assets/img/${p.img}" alt="">
    </a>
    <div class="cart-main">
      <div class="cart-top">
        <a class="cart-name" href="product.html?id=${p.id}">${esc(p.name)}</a>
        <button class="cart-del" type="button" data-remove-line aria-label="حذف ${esc(p.name)} از سبد">${icon('x')}</button>
      </div>
      <div class="cart-meta">
        <span class="mono" dir="ltr">${esc(p.oem)}</span>
        <span>${esc(p.brand)}</span>
      </div>
      <div class="p-fit p-fit--${info.state}">${icon(FIT[info.state].icon)}<span>${esc(cartFitText(p, v))}</span></div>
      <div class="cart-bottom">
        <div class="qty" role="group" aria-label="تعداد ${esc(p.name)}">
          <button type="button" data-qty-inc aria-label="افزودن یکی">${icon('plus')}</button>
          <span class="mono" aria-live="polite">${fa(it.qty)}</span>
          <button type="button" data-qty-dec aria-label="کم کردن یکی" ${it.qty <= 1 ? 'data-qty-min' : ''}>${icon('minus')}</button>
        </div>
        <div class="cart-price">
          ${p.oldPrice ? `<span class="old">${money(p.oldPrice * it.qty)}</span>` : ''}
          <strong>${money(p.price * it.qty)}</strong>
          <small>تومان</small>
        </div>
      </div>
    </div>
  </article>`;
}

function renderCart() {
  const body = $('#cartBody');
  if (!body) return;
  const foot = $('#cartFoot');
  const pass = $('#cartPassport');
  const note = $('#cartHeadNote');
  const v = getVehicle();
  const lines = cartLines();
  const t = cartTotals();

  if (note) note.textContent = lines.length ? `${fa(t.qty)} قلم` : 'خالی';

  if (pass) {
    pass.innerHTML = v
      ? `<span class="cp-icon">${icon('car')}</span>
         <div class="cp-txt"><b>${esc(v.modelName)} ${fa(v.year)}</b><span>${esc(v.makeName || '')}${v.engine ? ` · ${esc(v.engine)}` : ''}</span></div>
         <button class="cp-change" type="button" data-open-vehicle>تغییر</button>`
      : `<span class="cp-icon cp-icon--empty">${icon('car')}</span>
         <div class="cp-txt"><b>خودرو انتخاب نشده</b><span>برای بررسی سازگاری، خودرو را ثبت کنید</span></div>
         <button class="cp-change" type="button" data-open-vehicle>انتخاب</button>`;
  }

  if (!lines.length) {
    body.innerHTML = `
      <div class="cart-empty">
        <span class="ce-icon">${icon('cart')}</span>
        <b>سبد خرید خالی است</b>
        <p>قطعهٔ مناسب خودروی خود را از فروشگاه انتخاب کنید؛ سازگاری هر قطعه روی کارتش نوشته شده است.</p>
        <a class="btn btn-amber" href="shop.html">رفتن به فروشگاه</a>
      </div>`;
    if (foot) foot.innerHTML = '';
    return;
  }

  const issues = cartIssueCount(lines, v);
  body.innerHTML = `<div class="cart-list">${lines.map(it => cartRowHTML(it, v)).join('')}</div>`;

  const bad = issues.no || 0;
  const unknown = issues.unknown || 0;
  const ask = issues.ask || 0;
  const canGo = cartCanCheckout(lines, v);
  const save = t.old - t.sum;

  if (foot) {
    foot.innerHTML = `
      <div class="cart-sum">
        <div class="cs-row"><span>جمع کالاها (${fa(t.qty)} قلم)</span><b>${money(t.sum)} تومان</b></div>
        ${save > 0 ? `<div class="cs-row cs-row--save"><span>سود شما از این خرید</span><b>−${money(save)} تومان</b></div>` : ''}
        <div class="cs-row cs-row--muted"><span>هزینهٔ ارسال</span><span>در مرحلهٔ بعد</span></div>
      </div>
      ${bad ? `<div class="cart-warn" id="cartWarn">
        <div class="cw-head">${icon('alert')} <b>${fa(bad)} قطعه با ${esc(v.modelName)} شما سازگار نیست</b></div>
        <p>این قطعات را حذف کنید یا با آگاهی ادامه دهید. سفارش‌های ناسازگار مشمول ضمانت بازگشت سازگاری نیستند.</p>
        <label class="cw-check"><input type="checkbox" data-ack-mismatch ${cartAck ? 'checked' : ''}><span>با وجود ناسازگاری ادامه می‌دهم</span></label>
      </div>` : ''}
      ${unknown ? `<div class="cart-note">${icon('info')}<span>${fa(unknown)} قطعه سازگاری ثبت‌شده ندارد (نامشخص، نه ناسازگار).</span><button type="button" data-request-fitment>درخواست بررسی</button></div>` : ''}
      ${ask ? `<div class="cart-note">${icon('car')}<span>خودرو انتخاب نشده؛ سازگاری قابل بررسی نیست.</span><button type="button" data-open-vehicle>انتخاب خودرو</button></div>` : ''}
      <a class="btn btn-amber btn-block cart-cta ${canGo ? '' : 'is-blocked'}" href="checkout.html" data-cart-cta ${canGo ? '' : 'aria-disabled="true"'}>
        ${icon('check-shield')} ادامهٔ سفارش
      </a>
      <div class="cart-trust"><a href="trust.html">${icon('refresh')} ۷ روز ضمانت بازگشت</a> · <a href="trust.html">${icon('truck')} ارسال به سراسر کشور</a></div>`;
  }
}

function initCart() {
  cartBadge();
  document.addEventListener('click', e => {
    const inc = e.target.closest('[data-qty-inc]');
    const dec = e.target.closest('[data-qty-dec]');
    const del = e.target.closest('[data-remove-line]');
    const cta = e.target.closest('[data-cart-cta]');
    if (inc || dec) {
      e.preventDefault();
      const row = (inc || dec).closest('[data-line]');
      if (!row) return;
      const items = cartLoad();
      const line = items.find(it => it.id === row.dataset.line);
      if (!line) return;
      if (dec && line.qty <= 1) {
        toast('برای حذف، از دکمهٔ ضربدر استفاده کنید');
        return;
      }
      line.qty = Math.min(99, line.qty + (inc ? 1 : -1));
      cartSave(items);
      renderCart();
      return;
    }
    if (del) {
      e.preventDefault();
      const row = del.closest('[data-line]');
      if (!row) return;
      const p = PRODUCTS.find(x => x.id === row.dataset.line);
      cartSave(cartLoad().filter(it => it.id !== row.dataset.line));
      renderCart();
      toast(`«${p ? p.name : 'قطعه'}» از سبد حذف شد`);
      return;
    }
    if (cta && cta.classList.contains('is-blocked')) {
      e.preventDefault();
      cartAck = false;
      const w = $('#cartWarn');
      if (w) {
        w.classList.remove('is-pulse');
        void w.offsetWidth;
        w.classList.add('is-pulse');
        w.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
      }
      toast('اول ناسازگاری را تأیید کنید یا قطعه را حذف کنید');
    }
  });
  document.addEventListener('change', e => {
    if (e.target.matches('[data-ack-mismatch]')) {
      cartAck = e.target.checked;
      renderCart();
    }
  });
}

/* =========================================================
   Checkout (demo) — آدرس، ارسال، پرداخت در محل، تأیید سفارش
   ========================================================= */
const PROVINCES = ['تهران', 'البرز', 'اصفهان', 'خراسان رضوی', 'فارس', 'آذربایجان شرقی', 'آذربایجان غربی', 'خوزستان', 'گیلان', 'مازندران', 'قم', 'کرمان', 'یزد', 'هرمزگان', 'کردستان', 'سایر استان‌ها'];

const SHIP_OPTIONS = [
  { id: 'peyk', label: 'پیک موتوری', price: 85000, note: 'تحویل ۲۴ ساعته · تهران و کرج', icon: 'truck' },
  { id: 'post', label: 'پست پیشتاز', price: 45000, note: '۲ تا ۴ روز کاری · سراسر کشور', icon: 'box' },
  { id: 'pickup', label: 'تحویل حضوری', price: 0, note: 'فروشگاه پارت‌یاب، تهران', icon: 'map-pin' }
];
const PAY_OPTIONS = [
  { id: 'cod', label: 'پرداخت در محل', note: 'نقدی یا کارتخوان هنگام تحویل', icon: 'truck', badge: 'پیشنهاد ما' },
  { id: 'card', label: 'کارت به کارت', note: 'شمارهٔ کارت پس از ثبت سفارش پیامک می‌شود', icon: 'copy' },
  { id: 'online', label: 'پرداخت آنلاین', note: 'درگاه بانکی، همین حالا', icon: 'shield' }
];
const coState = { ship: 'peyk', pay: 'cod', ack: false };

const latinDigits = s => String(s)
  .replace(/[۰-۹]/g, d => String('۰۱۲۳۴۵۶۷۸۹'.indexOf(d)))
  .replace(/[٠-٩]/g, d => String('٠١٢٣٤٥٦٧٨٩'.indexOf(d)));

const coTotals = () => {
  const t = cartTotals();
  const ship = (SHIP_OPTIONS.find(x => x.id === coState.ship) || SHIP_OPTIONS[0]).price;
  return { ...t, ship, pay: t.sum + ship, save: Math.max(0, t.old - t.sum) };
};

function coEmptyHTML() {
  return `<div class="co-empty">
    <span class="ce-icon">${icon('cart')}</span>
    <h1>سبدی برای تسویه نیست</h1>
    <p>اول قطعهٔ سازگار با خودرویتان را انتخاب کنید؛ بعد از اینجا سفارش را نهایی می‌کنید.</p>
    <a class="btn btn-amber" href="shop.html">رفتن به فروشگاه</a>
  </div>`;
}

function coRadioHTML(kind, opt, active) {
  const price = kind !== 'ship' ? '' : (opt.price ? `${money(opt.price)} <small>تومان</small>` : 'رایگان');
  return `<label class="co-opt ${active ? 'is-active' : ''}">
    <input type="radio" name="${kind}" value="${opt.id}" ${active ? 'checked' : ''}>
    <span class="co-opt-icon">${icon(opt.icon)}</span>
    <span class="co-opt-txt"><b>${opt.label}${opt.badge ? `<em>${opt.badge}</em>` : ''}</b><span>${opt.note}</span></span>
    ${price ? `<span class="co-opt-price">${price}</span>` : ''}
  </label>`;
}

function coSummaryHTML(v, lines, t) {
  const bad = lines.filter(it => fitInfo(it.product, v).state === 'no').length;
  const fitNote = !v
    ? `${icon('car')} خودرو انتخاب نشده؛ سازگاری بررسی نشده است.`
    : bad
      ? `${icon('alert')} ${fa(bad)} قطعه با ${esc(v.modelName)} شما سازگار نیست.`
      : `${icon('check-shield')} همهٔ قطعات برای ${esc(v.modelName)} ${fa(v.year)} تأیید شده‌اند.`;
  return `<div class="co-card co-summary">
    <div class="co-card-head"><span class="co-num">${icon('box')}</span><h2>خلاصهٔ سفارش</h2><span class="co-count">${fa(t.qty)} قلم</span></div>
    <div class="co-lines">
      ${lines.map(it => `<div class="co-line">
        <span class="co-thumb"><img src="assets/img/${it.product.img}" alt=""></span>
        <span class="co-line-txt"><b>${esc(it.product.name)}</b><span class="mono" dir="ltr">${esc(it.product.oem)}</span></span>
        <span class="co-line-qty">×${fa(it.qty)}</span>
        <span class="co-line-price">${money(it.product.price * it.qty)}</span>
      </div>`).join('')}
    </div>
    <div class="co-fitnote ${bad ? 'is-bad' : ''}">${fitNote}</div>
    <div class="co-total-rows">
      <div><span>جمع کالاها</span><b>${money(t.sum)} تومان</b></div>
      <div><span>ارسال</span><b>${t.ship ? `${money(t.ship)} تومان` : 'رایگان'}</b></div>
      ${t.save ? `<div class="is-save"><span>سود شما</span><b>−${money(t.save)} تومان</b></div>` : ''}
      <div class="co-grand"><span>قابل پرداخت</span><b>${money(t.pay)} <small>تومان</small></b></div>
    </div>
    ${bad ? `<label class="cw-check cw-check--co"><input type="checkbox" data-ack-mismatch ${coState.ack ? 'checked' : ''}><span>با وجود ناسازگاری ادامه می‌دهم</span></label>` : ''}
    <button class="btn btn-amber btn-block co-submit" type="submit" form="coForm" data-co-submit ${bad && !coState.ack ? 'aria-disabled="true"' : ''}>
      ${icon('check-shield')} ثبت سفارش
    </button>
    <div class="cart-trust"><a href="trust.html">${icon('refresh')} ۷ روز ضمانت بازگشت</a> · <a href="tel:${SHOP_PHONE}">${icon('phone')} پشتیبانی ${SHOP_PHONE_FA}</a></div>
  </div>`;
}

function coFieldsHTML(v) {
  return `<section class="co-card">
    <div class="co-card-head"><span class="co-num">۱</span><h2>گیرنده</h2></div>
    <div class="co-fields">
      <label class="co-field" data-field="name"><span>نام و نام خانوادگی</span>
        <input type="text" name="name" autocomplete="name" placeholder="مثلاً علی رضایی">
        <em class="co-err"></em>
      </label>
      <label class="co-field" data-field="phone"><span>موبایل</span>
        <input type="tel" name="phone" inputmode="numeric" autocomplete="tel" dir="ltr" placeholder="0912 345 6789">
        <em class="co-err"></em>
      </label>
      <label class="co-field" data-field="province"><span>استان</span>
        <select name="province">
          <option value="">انتخاب کنید</option>
          ${PROVINCES.map(x => `<option>${x}</option>`).join('')}
        </select>
        <em class="co-err"></em>
      </label>
      <label class="co-field" data-field="city"><span>شهر</span>
        <input type="text" name="city" autocomplete="address-level2" placeholder="مثلاً تهران">
        <em class="co-err"></em>
      </label>
      <label class="co-field co-field--full" data-field="address"><span>نشانی کامل</span>
        <textarea name="address" rows="2" autocomplete="street-address" placeholder="خیابان، کوچه، پلاک، واحد"></textarea>
        <em class="co-err"></em>
      </label>
      <label class="co-field" data-field="postal"><span>کد پستی <em class="co-opt-tag">اختیاری</em></span>
        <input type="text" name="postal" inputmode="numeric" dir="ltr" placeholder="۱۰ رقم">
        <em class="co-err"></em>
      </label>
      <label class="co-field" data-field="note"><span>یادداشت برای سفارش <em class="co-opt-tag">اختیاری</em></span>
        <input type="text" name="note" placeholder="مثلاً زنگ واحد ۲ را بزنید">
        <em class="co-err"></em>
      </label>
    </div>
  </section>`;
}

function renderCheckout() {
  const root = $('#checkoutRoot');
  if (!root) return;
  const stored = sessionStorage.getItem('py_last_order');
  if (stored) {
    try { renderOrderDone(JSON.parse(stored)); return; } catch (err) { sessionStorage.removeItem('py_last_order'); }
  }
  document.body.classList.remove('is-ordered');
  const v = getVehicle();
  const lines = cartLines();
  if (!lines.length) {
    root.innerHTML = coEmptyHTML();
    return;
  }
  const t = coTotals();
  root.innerHTML = `
  <div class="co-grid">
    <form class="co-form" id="coForm" novalidate>
      ${coFieldsHTML(v)}
      <section class="co-card">
        <div class="co-card-head"><span class="co-num">۲</span><h2>روش ارسال</h2></div>
        <div class="co-opts">${SHIP_OPTIONS.map(o => coRadioHTML('ship', o, coState.ship === o.id)).join('')}</div>
      </section>
      <section class="co-card">
        <div class="co-card-head"><span class="co-num">۳</span><h2>روش پرداخت</h2></div>
        <div class="co-opts">${PAY_OPTIONS.map(o => coRadioHTML('pay', o, coState.pay === o.id)).join('')}</div>
        <p class="co-hint">${icon('info')} پرداخت در محل برای تهران و کرج فعال است؛ برای شهرهای دیگر پست پیشتاز با پرداخت آنلاین یا کارت به کارت.</p>
      </section>
      <button class="btn btn-amber btn-block co-submit co-submit--end" type="submit" data-co-submit ${lines.some(it => fitInfo(it.product, v).state === 'no') && !coState.ack ? 'aria-disabled="true"' : ''}>
        ${icon('check-shield')} ثبت سفارش · ${money(t.pay)} تومان
      </button>
    </form>
    <aside class="co-side">${coSummaryHTML(v, lines, t)}</aside>
  </div>`;
}

function coReadForm() {
  const f = $('#coForm');
  const get = n => (f.elements[n] ? String(f.elements[n].value || '').trim() : '');
  return {
    name: get('name'), phone: latinDigits(get('phone')).replace(/[\s-]/g, ''),
    province: get('province'), city: get('city'), address: get('address'),
    postal: latinDigits(get('postal')).replace(/[\s-]/g, ''), note: get('note')
  };
}

function coValidate(data) {
  const errors = {};
  if (data.name.length < 3) errors.name = 'نام و نام خانوادگی را کامل بنویسید';
  if (!/^09\d{9}$/.test(data.phone)) errors.phone = 'شمارهٔ موبایل باید ۱۱ رقم و با ۰۹ شروع شود';
  if (!data.province) errors.province = 'استان را انتخاب کنید';
  if (data.city.length < 2) errors.city = 'نام شهر را بنویسید';
  if (data.address.length < 10) errors.address = 'نشانی را کامل‌تر بنویسید';
  if (data.postal && !/^\d{10}$/.test(data.postal)) errors.postal = 'کد پستی باید ۱۰ رقم باشد';
  return errors;
}

function renderOrderDone(order) {
  const root = $('#checkoutRoot');
  if (!root) return;
  document.body.classList.add('is-ordered');
  const ship = SHIP_OPTIONS.find(o => o.id === order.ship) || SHIP_OPTIONS[0];
  const pay = PAY_OPTIONS.find(o => o.id === order.pay) || PAY_OPTIONS[0];
  const eta = new Date(Date.now() + (order.ship === 'peyk' ? 1 : order.ship === 'post' ? 3 : 0) * 86400000);
  let etaText = ship.note;
  try { etaText = eta.toLocaleDateString('fa-IR', { weekday: 'long', day: 'numeric', month: 'long' }); } catch (err) { /* keep note */ }
  root.innerHTML = `
  <div class="co-done">
    <span class="done-ring">${icon('check')}</span>
    <span class="done-eyebrow">سفارش ثبت شد</span>
    <h1>ممنون، ${esc(order.name.split(' ')[0])} عزیز</h1>
    <p class="done-sub">کد پیگیری سفارش شما</p>
    <div class="done-code mono" dir="ltr">${esc(order.code)}</div>
    <div class="done-meta">
      <div><span>مبلغ</span><b>${money(order.total)} تومان</b></div>
      <div><span>پرداخت</span><b>${pay.label}</b></div>
      <div><span>تحویل</span><b>${etaText}</b></div>
      <div><span>گیرنده</span><b>${esc(order.name)}</b></div>
    </div>
    <div class="done-addr">${icon('map-pin')} <span>${esc(order.province)}، ${esc(order.city)} — ${esc(order.address)}<br><span class="mono" dir="ltr">${esc(order.phone)}</span></span></div>
    <div class="done-items">
      ${order.items.map(it => `<div class="done-item">
        <span class="done-thumb"><img src="assets/img/${it.img}" alt=""></span>
        <span class="done-item-txt"><b>${esc(it.name)}</b><span class="mono" dir="ltr">${esc(it.oem)}</span></span>
        <span class="mono">×${fa(it.qty)}</span>
        <span>${money(it.price * it.qty)}</span>
      </div>`).join('')}
    </div>
    <div class="done-note ${order.badFit ? 'is-bad' : ''}">${order.fitNote}</div>
    <div class="done-actions">
      <a class="btn btn-amber" href="shop.html">${icon('grid')} ادامهٔ خرید</a>
      <button class="btn btn-ghost" type="button" data-new-order>سفارش جدید</button>
    </div>
  </div>`;
}

function placeOrder(data) {
  const lines = cartLines();
  const v = getVehicle();
  const t = coTotals();
  const bad = lines.filter(it => fitInfo(it.product, v).state === 'no').length;
  const code = `PY-${Math.floor(100000 + Math.random() * 899999)}`;
  const order = {
    code, at: Date.now(),
    name: data.name, phone: data.phone, province: data.province, city: data.city,
    address: data.address, note: data.note, ship: coState.ship, pay: coState.pay,
    total: t.pay,
    items: lines.map(it => ({ id: it.product.id, name: it.product.name, oem: it.product.oem, img: it.product.img, qty: it.qty, price: it.product.price })),
    badFit: bad,
    fitNote: !v
      ? `${icon('info')} خودرو انتخاب نشده بود؛ سازگاری این سفارش بررسی نشده است.`
      : bad
        ? `${icon('alert')} ${fa(bad)} قطعه با ${esc(v.modelName)} ${fa(v.year)} سازگار نیست و مشمول ضمانت بازگشت سازگاری نمی‌شود.`
        : `${icon('check-shield')} همهٔ ${fa(lines.length)} قلم با ${esc(v.modelName)} ${fa(v.year)} تأیید شده‌اند.`
  };
  sessionStorage.setItem('py_last_order', JSON.stringify(order));
  ordersPush(order);
  cartSave([]);
  renderCart();
  renderOrderDone(order);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function coRefreshSummary() {
  const v = getVehicle();
  const lines = cartLines();
  const side = $('.co-side');
  if (side && lines.length) side.innerHTML = coSummaryHTML(v, lines, coTotals());
  const end = $('.co-submit--end');
  if (end) {
    const bad = lines.some(it => fitInfo(it.product, v).state === 'no');
    end.setAttribute('aria-disabled', bad && !coState.ack ? 'true' : 'false');
    end.innerHTML = `${icon('check-shield')} ثبت سفارش · ${money(coTotals().pay)} تومان`;
  }
}

function initCheckout() {
  renderCheckout();
  const form = () => $('#coForm');

  document.addEventListener('submit', e => {
    const f = e.target;
    if (!f.matches('#coForm')) return;
    e.preventDefault();
    const data = coReadForm();
    const errors = coValidate(data);
    const fields = $$('.co-field', f);
    fields.forEach(el => {
      const key = el.dataset.field;
      const msg = errors[key];
      el.classList.toggle('is-error', !!msg);
      const em = $('.co-err', el);
      if (em) em.textContent = msg || '';
    });
    if (Object.keys(errors).length) {
      const first = $('.co-field.is-error', f);
      if (first) first.scrollIntoView({ block: 'center', behavior: 'smooth' });
      const input = first && first.querySelector('input,select,textarea');
      if (input) input.focus({ preventScroll: true });
      toast('چند فیلد ناقص است؛ موارد قرمز را کامل کنید');
      return;
    }
    placeOrder(data);
  });

  document.addEventListener('change', e => {
    const t = e.target;
    if (t.name === 'ship' && t.matches('#coForm input[name="ship"]')) {
      coState.ship = t.value;
      $$('#coForm input[name="ship"]').forEach(i => i.closest('.co-opt').classList.toggle('is-active', i.checked));
      coRefreshSummary();
      return;
    }
    if (t.name === 'pay' && t.matches('#coForm input[name="pay"]')) {
      coState.pay = t.value;
      $$('#coForm input[name="pay"]').forEach(i => i.closest('.co-opt').classList.toggle('is-active', i.checked));
      coRefreshSummary();
      return;
    }
    if (t.matches('[data-ack-mismatch]')) {
      coState.ack = t.checked;
      cartAck = t.checked;
      coRefreshSummary();
      return;
    }
    const field = t.closest('.co-field');
    if (field && field.classList.contains('is-error')) {
      field.classList.remove('is-error');
      const em = $('.co-err', field);
      if (em) em.textContent = '';
    }
  });

  document.addEventListener('click', e => {
    const btn = e.target.closest('[data-co-submit]');
    if (btn && btn.getAttribute('aria-disabled') === 'true') {
      e.preventDefault();
      const chk = $('.cw-check--co');
      if (chk) {
        chk.classList.remove('is-pulse');
        void chk.offsetWidth;
        chk.classList.add('is-pulse');
        chk.scrollIntoView({ block: 'center', behavior: 'smooth' });
      }
      toast('اول ناسازگاری را تأیید کنید یا قطعه را از سبد حذف کنید');
      return;
    }
    if (e.target.closest('[data-new-order]')) {
      sessionStorage.removeItem('py_last_order');
      renderCheckout();
      toast('سبد جدید آماده است');
    }
  });
}

/* =========================================================
   Garage — پاسپورت‌های خودرو، قطعات تأییدشده، سرویس و سفارش‌ها
   ========================================================= */
const SERVICE_KEY = 'py_service_v1';
const ORDERS_KEY = 'py_orders_v1';

const SERVICE_TASKS = [
  { id: 'oil', label: 'روغن موتور و فیلتر روغن', note: 'هر ۱۰٬۰۰۰ کیلومتر', cat: 'filter' },
  { id: 'air', label: 'فیلتر هوا و کابین', note: 'هر ۲۰٬۰۰۰ کیلومتر', cat: 'filter' },
  { id: 'brake', label: 'لنت و دیسک ترمز', note: 'هر ۳۰٬۰۰۰ کیلومتر یا ۲ سال', cat: 'brake' },
  { id: 'spark', label: 'شمع و سیستم برق', note: 'هر ۴۰٬۰۰۰ کیلومتر', cat: 'electrical' }
];

function serviceLoad() {
  try { return JSON.parse(localStorage.getItem(SERVICE_KEY)) || {}; } catch (e) { return {}; }
}
function serviceSave(all) { localStorage.setItem(SERVICE_KEY, JSON.stringify(all)); }

function ordersLoad() {
  try {
    const list = JSON.parse(localStorage.getItem(ORDERS_KEY));
    return Array.isArray(list) ? list : [];
  } catch (e) { return []; }
}
function ordersPush(order) {
  const list = ordersLoad();
  list.unshift(order);
  localStorage.setItem(ORDERS_KEY, JSON.stringify(list.slice(0, 8)));
}

function openPickerFor(modelId) {
  picker.modelId = modelId;
  picker.view = 'years';
  picker.year = null;
  picker.engine = '';
  renderPicker();
  openModal('#vehicleModal');
}

function garageCardHTML(v, active) {
  const cov = coverageFor(v) || { ok: 0, total: PRODUCTS.length, pct: 0 };
  return `<article class="gv-card ${active ? 'is-active' : ''}">
    <div class="gv-top">
      <span class="gv-plate">${icon('car')}</span>
      <span class="gv-tag ${active ? 'is-active' : ''}">${active ? 'خودروی فعال' : 'ذخیره‌شده'}</span>
    </div>
    <h3 class="gv-name">${esc(v.modelName)} <span class="mono">${fa(v.year)}</span></h3>
    <dl class="gv-specs">
      <div><dt>سازنده</dt><dd>${esc(v.makeName || '—')}</dd></div>
      <div><dt>تیپ موتور</dt><dd>${esc(v.engine || 'نامشخص')}</dd></div>
      <div><dt>پوشش کاتالوگ</dt><dd class="mono">${fa(cov.ok)} از ${fa(cov.total)}</dd></div>
    </dl>
    <span class="gv-meter" role="img" aria-label="پوشش کاتالوگ ${fa(cov.pct)} درصد"><i style="width:${cov.pct}%"></i></span>
    <div class="gv-actions">
      ${active
        ? `<span class="gv-active">${icon('check')} فعال</span>`
        : `<button class="btn btn-ghost btn-sm" type="button" data-pick-vehicle="${v.key}">فعال کن</button>`}
      <button class="gv-del" type="button" data-remove-vehicle="${v.key}" aria-label="حذف ${esc(v.modelName)} از گاراژ">${icon('x')}</button>
    </div>
  </article>`;
}

function garageEmptyHTML() {
  const chips = ['206', 'pride', 'samand', 'quick', 'shahin', 'dena', 'tara', 'pars']
    .map(id => findModel(id)).filter(Boolean)
    .map(m => `<button class="make-pill" type="button" data-garage-model="${m.id}">${esc(m.name)}</button>`).join('');
  return `<div class="g-empty">
    <span class="g-empty-icon">${icon('garage')}</span>
    <h2>گاراژ شما خالی است</h2>
    <p>خودرویتان را ثبت کنید تا هر قطعه با پلاک شما بررسی شود، پوشش کاتالوگ و چک‌لیست سرویس هم همین‌جا ساخته می‌شود.</p>
    <div class="g-empty-cta">
      <button class="btn btn-amber" type="button" data-open-vehicle>${icon('plus')} ثبت خودرو</button>
      <a class="btn btn-ghost" href="shop.html">${icon('box')} دیدن فروشگاه</a>
    </div>
    <div class="g-empty-models">
      <span>پرجست‌وجوترین خودروها:</span>
      <div class="make-pills">${chips}</div>
    </div>
  </div>`;
}

function serviceHTML(v) {
  const all = serviceLoad();
  const done = all[v.key] || {};
  return `<section class="g-panel">
    <div class="g-panel-head">
      <h2>${icon('wrench')} سرویس دوره‌ای</h2>
      <span class="g-panel-note">${fa(SERVICE_TASKS.filter(t => done[t.id]).length)} از ${fa(SERVICE_TASKS.length)} انجام شد</span>
    </div>
    <div class="svc-list">
      ${SERVICE_TASKS.map(task => {
        const p = PRODUCTS.find(x => x.cat === task.cat && ['fit', 'conditional', 'universal'].includes(fitInfo(x, v).state));
        return `<label class="svc-row ${done[task.id] ? 'is-done' : ''}">
          <input type="checkbox" data-service="${task.id}" ${done[task.id] ? 'checked' : ''}>
          <span class="svc-tick">${icon('check')}</span>
          <span class="svc-txt"><b>${task.label}</b><span>${task.note}${p ? ` · ${esc(p.name)}` : ''}</span></span>
          ${p ? `<a class="svc-link" href="product.html?id=${p.id}" aria-label="دیدن ${esc(p.name)}">${icon('chevron-left')}</a>` : ''}
        </label>`;
      }).join('')}
    </div>
    <p class="g-panel-foot">${icon('info')} بازه‌ها تقریبی و بر اساس سرویس دوره‌ای رایج خودروهای ایرانی است.</p>
  </section>`;
}

function ordersHTML() {
  const list = ordersLoad();
  if (!list.length) {
    return `<section class="g-panel">
      <div class="g-panel-head"><h2>${icon('box')} سفارش‌های من</h2></div>
      <p class="g-empty-line">هنوز سفارشی ثبت نکرده‌اید. اولین سفارش را از سبد خرید نهایی کنید.</p>
    </section>`;
  }
  return `<section class="g-panel">
    <div class="g-panel-head"><h2>${icon('box')} سفارش‌های من</h2><span class="g-panel-note">${fa(list.length)} سفارش</span></div>
    <div class="ord-list">
      ${list.slice(0, 3).map(o => {
        let date = '';
        try { date = new Date(o.at).toLocaleDateString('fa-IR', { day: 'numeric', month: 'short' }); } catch (err) { date = ''; }
        return `<div class="ord-row">
          <span class="ord-code mono" dir="ltr">${esc(o.code)}</span>
          <span class="ord-meta">${fa((o.items || []).length)} قلم · ${date}</span>
          <span class="ord-total mono">${money(o.total)}</span>
          <span class="ord-state">در حال پردازش</span>
        </div>`;
      }).join('')}
    </div>
  </section>`;
}

function renderGarage() {
  const root = $('#garageRoot');
  if (!root) return;
  const g = garageLoad();
  const v = getVehicle();

  if (!g.vehicles.length) {
    root.innerHTML = garageEmptyHTML();
    return;
  }

  const fit = v ? PRODUCTS.filter(p => ['fit', 'conditional', 'universal'].includes(fitInfo(p, v).state)) : [];
  const cov = v ? coverageFor(v) : null;

  root.innerHTML = `
  <div class="g-cards">
    ${g.vehicles.map(x => garageCardHTML(x, x.key === g.active)).join('')}
    ${g.vehicles.length < 3 ? `<button class="gv-add" type="button" data-open-vehicle>
      <span>${icon('plus')}</span>
      <b>افزودن خودرو</b>
      <small>تا ۳ خودرو در گاراژ</small>
    </button>` : ''}
  </div>

  <div class="g-body">
    <div class="g-main">
      <div class="g-section-head">
        <div>
          <span class="eyebrow">${icon('check-shield')} سازگاری تأییدشده</span>
          <h2>قطعات مناسب ${v ? `${esc(v.modelName)} ${fa(v.year)}` : 'خودروی شما'}</h2>
        </div>
        <a class="lnk-more" href="shop.html">همهٔ قطعات سازگار ${icon('arrow-left')}</a>
      </div>
      ${fit.length
        ? `<div class="p-grid">${fit.slice(0, 4).map(productCard).join('')}</div>`
        : `<div class="g-empty-line">${icon('info')} برای این خودرو قطعهٔ تأییدشده‌ای در کاتالوگ نیست؛ از فروشگاه فیلتر را بردارید.</div>`}
      ${cov ? `<div class="g-covbar">
        <span class="g-cov-txt">${icon('car')} پوشش کاتالوگ برای ${esc(v.modelName)}: <b>${fa(cov.ok)} از ${fa(cov.total)}</b> قطعه (${fa(cov.pct)}٪)</span>
        <span class="g-cov-meter"><i style="width:${cov.pct}%"></i></span>
      </div>` : ''}
    </div>
    <aside class="g-side">
      ${v ? serviceHTML(v) : ''}
      ${ordersHTML()}
    </aside>
  </div>`;
}

function initGarage() {
  renderGarage();
  document.addEventListener('py:vehicle', () => renderGarage());
  document.addEventListener('click', e => {
    const chip = e.target.closest('[data-garage-model]');
    if (chip) { openPickerFor(chip.dataset.garageModel); return; }
  });
  document.addEventListener('change', e => {
    const box = e.target.closest('[data-service]');
    if (!box) return;
    const v = getVehicle();
    if (!v) return;
    const all = serviceLoad();
    all[v.key] = all[v.key] || {};
    all[v.key][box.dataset.service] = box.checked;
    serviceSave(all);
    const row = box.closest('.svc-row');
    if (row) row.classList.toggle('is-done', box.checked);
    const note = $('.g-panel-note', $('.g-side') || document);
    const done = SERVICE_TASKS.filter(t => all[v.key][t.id]).length;
    if (note && note.textContent.includes('از')) note.textContent = `${fa(done)} از ${fa(SERVICE_TASKS.length)} انجام شد`;
    toast(box.checked ? 'سرویس انجام‌شده ثبت شد' : 'علامت سرویس برداشته شد', box.checked ? 'success' : 'info');
  });
}

/* ---------------- Boot ---------------- */
/* ---------------- sticky contact (call / whatsapp) ---------------- */
function initContactFab() {
  if (document.querySelector('.contact-fab')) return;
  const el = document.createElement('div');
  el.className = 'contact-fab';
  el.innerHTML = `
    <a class="cfab cfab--call" href="tel:${SHOP_PHONE}" aria-label="تماس با فروشگاه ${SHOP_PHONE_FA}">${icon('phone')}<span>تماس</span></a>
    <a class="cfab cfab--wa" href="${SHOP_WA}" target="_blank" rel="noopener" aria-label="واتساپ فروشگاه">${icon('whatsapp')}<span>واتساپ</span></a>`;
  document.body.appendChild(el);
  hydrateIcons(el);
}

/* لینک‌های دمو که صفحه‌ای ندارند: به‌جای dead-link، پیام روشن بده */
document.addEventListener('click', e => {
  const soon = e.target.closest('[data-soon]');
  if (!soon) return;
  e.preventDefault();
  toast(soon.dataset.soon || 'این بخش در نسخهٔ دمو فعال نیست');
});

document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  initCart();
  const page = document.body.dataset.page;
  if (page === 'home') { initHero(); initHome(); }
  if (page === 'shop') initShop();
  if (page === 'product') initProduct();
  if (page === 'checkout') initCheckout();
  if (page === 'garage') initGarage();
  initContactFab();
  hydrateIcons();
});

window.pyToast = toast;
window.hydrateIcons = hydrateIcons;
