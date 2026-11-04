/* Demo-only persistence. No personal checkout data leaves the browser. */
(function () {
  'use strict';
  var catalog = [];
  var key = 'partyab-demo-v2';
  var state = { cart: {}, favorites: [], compare: [], orders: [], coupon: '', car: null, address: null };
  try { var saved = JSON.parse(localStorage.getItem(key) || 'null'); if (saved && typeof saved === 'object') Object.keys(state).forEach(function (k) { if (saved[k] != null) state[k] = saved[k]; }); } catch (e) {}
  function persist() { try { localStorage.setItem(key, JSON.stringify(state)); } catch (e) {} document.dispatchEvent(new CustomEvent('demo:change')); }
  function product(id) { return catalog.find(function (p) { return p.id === String(id); }); }
  function cartEntries() { return Object.keys(state.cart).map(function (id) { var p = product(id); return p ? { product: p, qty: Math.max(1, Math.min(p.stock || 1, state.cart[id] | 0)) } : null; }).filter(Boolean); }
  function totals() {
    var entries = cartEntries();
    var subtotal = entries.reduce(function (s, e) { return s + e.product.price * e.qty; }, 0);
    var discount = state.coupon === 'PARTYAB' ? Math.round(subtotal * .1) : 0;
    var shipping = subtotal === 0 || subtotal - discount >= 5000000 ? 0 : 65000;
    return { entries: entries, subtotal: subtotal, discount: discount, shipping: shipping, total: subtotal - discount + shipping, count: entries.reduce(function (s, e) { return s + e.qty; }, 0) };
  }
  function add(id, qty) { var p = product(id); if (!p || !p.stock) return false; state.cart[p.id] = Math.min(p.stock, (state.cart[p.id] || 0) + Math.max(1, qty || 1)); persist(); return true; }
  function setQty(id, qty) { var p = product(id); if (!p) return; if (qty <= 0) delete state.cart[id]; else state.cart[id] = Math.min(p.stock, qty | 0); persist(); }
  function toggle(list, id) { id = String(id); if (!product(id)) return false; var arr = state[list]; var i = arr.indexOf(id); if (i >= 0) arr.splice(i, 1); else { if (list === 'compare' && arr.length >= 4) return 'full'; arr.push(id); } persist(); return i < 0; }
  function createOrder() {
    var t = totals(); if (!t.count) return null;
    var order = { id: 'PB-' + Date.now().toString(36).toUpperCase(), date: new Date().toISOString(), items: t.entries.map(function (e) { return { id: e.product.id, qty: e.qty, price: e.product.price }; }), subtotal: t.subtotal, discount: t.discount, shipping: t.shipping, total: t.total, status: 'processing' };
    state.orders.unshift(order); state.cart = {}; state.coupon = ''; persist(); return order;
  }
  function faDigits(value) { return String(value).replace(/[۰-۹]/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(d); }); }
  function carMatches(p, car) {
    car = car || state.car; if (!car || !p) return false;
    if (car.brand !== p.fit.brand || car.model !== p.fit.model) return false;
    var years = faDigits(p.fit.years).match(/\d{4}/g).map(Number); var year = Number(faDigits(car.year));
    return year >= years[0] && year <= (years[1] || years[0]);
  }
  window.DemoStore = {
    init: function (data) { catalog = data; document.dispatchEvent(new CustomEvent('demo:change')); },
    state: state, product: product, cartEntries: cartEntries, totals: totals, add: add, setQty: setQty,
    favorite: function (id) { return toggle('favorites', id); }, compare: function (id) { return toggle('compare', id); },
    clearCompare: function () { state.compare = []; persist(); },
    setCoupon: function (code) { state.coupon = String(code || '').trim().toUpperCase(); persist(); return state.coupon === 'PARTYAB'; },
    setCar: function (car) { state.car = car; persist(); document.cookie = 'partyab_demo_car=' + encodeURIComponent(JSON.stringify(car)) + ';path=/;max-age=31536000;SameSite=Lax'; },
    clearCar: function () { state.car = null; persist(); document.cookie = 'partyab_demo_car=;path=/;max-age=0'; },
    setAddress: function (address) { state.address = address; persist(); },
    createOrder: createOrder, carMatches: carMatches, faDigits: faDigits,
  };
})();
