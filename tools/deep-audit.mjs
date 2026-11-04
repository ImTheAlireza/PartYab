import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
import { writeFileSync } from 'node:fs';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: [...chromium.args, '--enable-unsafe-swiftshader'], headless: true });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 } });
p.setDefaultTimeout(45000);
const errors = [], failed = [];
p.on('pageerror', e => errors.push('pageerror: ' + e.message));
p.on('console', m => { if (m.type() === 'error') errors.push('console: ' + m.text()); });
p.on('response', r => { if (r.status() >= 400 && !r.url().endsWith('/404')) failed.push(r.status() + ' ' + r.url()); });
const results = [];
const check = (pass, name, ok, detail = '') => { results.push({ pass, name, ok: !!ok, detail }); console.log((ok ? 'PASS' : 'FAIL') + ' [' + pass + '] ' + name + (detail ? ' — ' + detail : '')); };
const go = path => p.goto('http://127.0.0.1:3000' + path, { waitUntil: 'domcontentloaded' });
const css = (sel, prop, pseudo) => p.locator(sel).first().evaluate((el, [prop, pseudo]) => getComputedStyle(el, pseudo || undefined)[prop], [prop, pseudo]);
const stubHero = () => p.route('**/assets/js/hero-3d.js', r => r.fulfill({ contentType: 'text/javascript', body: '' }));
const unstubHero = () => p.unroute('**/assets/js/hero-3d.js');
await stubHero();

// Pass 1: every route, overflow, title, asset errors
const paths = ['/', '/shop', '/product/brake-pad', '/cart', '/checkout', '/account', '/orders', '/addresses', '/favorites', '/compare', '/my-car', '/faq', '/contact', '/guide', '/article/brake-care', '/about', '/returns', '/warranty', '/privacy', '/login', '/register', '/pages', '/404'];
let routeFails = 0;
for (const path of paths) {
  const res = await go(path); await p.waitForTimeout(250);
  const overflow = await p.evaluate(() => document.documentElement.scrollWidth > innerWidth + 1);
  const ok = (path === '/404' ? res.status() === 404 : res.status() === 200) && !overflow;
  if (!ok) routeFails++;
  check(1, 'route ' + path, ok, 'status=' + res.status() + ' overflow=' + overflow);
}
check(1, 'no failed asset requests', failed.length === 0, failed.join(' | '));

// Pass 2: navbar underline layers
await go('/'); await p.waitForTimeout(500);
const navLinks = p.locator('.pb-nav__menu > li > a');
const navCount = await navLinks.count();
let doubleUnderline = 0;
for (let i = 0; i < navCount; i++) {
  const link = navLinks.nth(i); await link.hover(); await p.waitForTimeout(380);
  const layers = await link.evaluate(el => {
    const s = getComputedStyle(el), before = getComputedStyle(el, '::before');
    const bgLine = s.backgroundImage !== 'none' && parseFloat(s.backgroundSize) > 0;
    const pseudoLine = before.content !== 'none' && before.transform !== 'matrix(1, 0, 0, 1, 0, 0)' && before.transform !== 'none';
    return { bgLine, pseudoLine, bgSize: s.backgroundSize, beforeTf: before.transform };
  });
  if (layers.bgLine && layers.pseudoLine) doubleUnderline++;
  check(2, 'nav link ' + i + ' underline layers', !(layers.bgLine && layers.pseudoLine), JSON.stringify(layers));
  await p.mouse.move(10, 10); await p.waitForTimeout(120);
}
check(2, 'navbar has no double underline', doubleUnderline === 0, doubleUnderline + ' links with two lines');

// Pass 3: button motion inventory
await go('/'); await p.waitForTimeout(400);
const btn = p.locator('.pb-hero__cta .pb-btn').first(); await btn.hover(); await p.waitForTimeout(650);
const btnInfo = await btn.evaluate(el => ({ translate: el.style.translate, iconAnim: el.querySelector('.pb-icon') ? getComputedStyle(el.querySelector('.pb-icon')).animationName : '', beforeTf: getComputedStyle(el, '::before').transform, tf: getComputedStyle(el).transform }));
check(3, 'button hover inventory', true, JSON.stringify(btnInfo));
await btn.dispatchEvent('pointerdown', { clientX: 100, clientY: 100 }); await p.waitForTimeout(80);
check(3, 'ripple present', await p.locator('.pb-ripple').count() > 0);

// Pass 4: product hover edge, 10 cycles
await go('/'); await p.waitForTimeout(400);
const card = p.locator('.pb-product-card').first(); await card.scrollIntoViewIfNeeded(); await p.waitForTimeout(700);
let flicker = 0;
for (let i = 0; i < 10; i++) {
  const r = await card.boundingBox(); await p.mouse.move(r.x + r.width / 2, r.y + r.height - 2); await p.waitForTimeout(380);
  const samples = [];
  for (let j = 0; j < 4; j++) { samples.push(await card.locator('.pb-product-card__surface').evaluate(el => getComputedStyle(el).translate)); await p.waitForTimeout(70); }
  if (!samples.every(v => v === samples[0] && v !== 'none' && v !== '0px 0px')) flicker++;
  await p.mouse.move(r.x + r.width / 2, r.y - 30); await p.waitForTimeout(220);
}
check(4, 'product hover edge stable x10', flicker === 0, flicker + ' flickers');

// Pass 5: categories, brands, services
await go('/'); await p.waitForTimeout(400);
const cat = p.locator('.pb-cat').first(); await cat.scrollIntoViewIfNeeded(); await p.waitForTimeout(600); await cat.hover(); await p.waitForTimeout(500);
check(5, 'category icon smooth', await cat.locator('.pb-cat__icon').evaluate(el => getComputedStyle(el).animationName) === 'none');
await p.locator('.pb-marquee').scrollIntoViewIfNeeded(); await p.waitForTimeout(300);
check(5, 'brand logos contained', await p.locator('.pb-brand-tile').evaluateAll(ts => ts.every(t => { const i = t.querySelector('img'); if (!i) return true; const a = t.getBoundingClientRect(), b = i.getBoundingClientRect(); return b.left >= a.left - 1 && b.right <= a.right + 1 && b.top >= a.top - 1 && b.bottom <= a.bottom + 1; })));
await p.locator('.pb-marquee').hover(); await p.waitForTimeout(120);
check(5, 'marquee pauses', await css('.pb-marquee__track', 'animationPlayState') === 'paused');

// Pass 6: loader 10 cycles + navigation coverage
await go('/contact'); await p.waitForTimeout(400);
let loaderFails = 0;
for (let i = 0; i < 10; i++) {
  await p.evaluate(() => { window.PartyabTransition.show(); });
  await p.waitForFunction(() => document.querySelector('.pb-page-loader').classList.contains('is-active'), { timeout: 5000 }).catch(() => {});
  const active = await css('.pb-page-loader', 'transform') === 'none';
  await p.evaluate(() => { window.PartyabTransition.hide(); });
  await p.waitForFunction(() => document.querySelector('.pb-page-loader').hidden, { timeout: 5000 }).catch(() => {});
  const hidden = await p.locator('.pb-page-loader').evaluate(el => el.hidden);
  if (!active || !hidden) loaderFails++;
}
check(6, 'loader show/hide stable x10', loaderFails === 0, loaderFails + ' failures');
check(6, 'loader has no progress bar', await p.locator('.pb-page-loader__line, .pb-page-loader [role=progressbar]').count() === 0);
check(6, 'loader logo animates', await css('.pb-page-loader__logo', 'animationName') === 'loader-logo');
await p.route('**/shop', async r => { await new Promise(res => setTimeout(res, 700)); await r.continue(); });
const nav = p.waitForURL('**/shop', { timeout: 15000 });
await p.locator('.pb-nav__menu a[href="/shop"]').first().click(); await p.waitForTimeout(520);
const covered = await p.locator('.pb-page-loader').evaluate(el => { const r = el.getBoundingClientRect(); return r.left <= 0 && r.right >= innerWidth && getComputedStyle(el).visibility === 'visible'; }).catch(() => false);
check(6, 'curtain covers before navigation', covered);
await nav; await p.unroute('**/shop'); await p.waitForTimeout(700);
check(6, 'arrival curtain hidden', await p.locator('.pb-page-loader').evaluate(el => el.hidden));

// Pass 7: forms/selects/checkout controls
await go('/contact'); await p.locator('[name=phone]').focus(); await p.waitForTimeout(200);
check(7, 'input focus no halo', await p.locator('[name=phone]').evaluate(el => getComputedStyle(el).boxShadow === 'none' && getComputedStyle(el).outlineStyle === 'none'));
await go('/my-car'); await p.locator('.pb-select__button').first().click(); await p.waitForTimeout(150);
check(7, 'select dropdown animates', await css('.pb-select__list', 'animationName') === 'select-in');
await p.keyboard.press('Escape');
await go('/checkout'); await p.locator('.demo-check input').check(); await p.waitForTimeout(80);
check(7, 'checkbox pop-in', await p.locator('.demo-check input').evaluate(el => getComputedStyle(el).animationName) === 'pop-in');

// Pass 8: commerce flow + account overflow
await go('/product/brake-pad'); await p.locator('[data-add="1"]').first().click(); await p.waitForTimeout(250);
await go('/cart'); await p.waitForTimeout(250);
check(8, 'cart count', (await p.locator('[data-cart-count]').textContent()).trim() !== '۰');
await p.locator('[data-coupon] input').fill('PARTYAB'); await p.locator('[data-coupon] button').click(); await p.waitForTimeout(150);
await go('/checkout'); await p.fill('#first', 'علی'); await p.fill('#last', 'رضایی'); await p.fill('#phone', '09123456789'); await p.fill('#city', 'تهران'); await p.fill('#address', 'خیابان نمونه'); await p.locator('.demo-check input').check(); await p.locator('#place_order').click(); await p.waitForURL('**/order/**');
check(8, 'demo order created', p.url().includes('/order/'));
for (const path of ['/account', '/orders', '/addresses', '/favorites', '/compare']) { await go(path); await p.waitForTimeout(200); check(8, 'account route overflow ' + path, !(await p.evaluate(() => document.documentElement.scrollWidth > innerWidth + 1))); }

// Pass 9: hero rotation, 10 samples
await unstubHero();
await go('/'); await p.waitForFunction(() => window.PartyabHero3D?.ready, { timeout: 30000 }).catch(() => {});
const heroReady = await p.evaluate(() => !!window.PartyabHero3D?.ready);
check(9, 'hero ready', heroReady);
if (heroReady) {
  check(9, 'fallback hidden', await p.locator('.pb-hero__car--fallback').evaluate(el => getComputedStyle(el).visibility) === 'hidden');
  const yaws = [];
  for (let i = 0; i < 10; i++) { yaws.push(await p.evaluate(() => window.PartyabHero3D.yaw)); await p.waitForTimeout(600); }
  const delta = Math.abs(yaws[9] - yaws[0]);
  check(9, 'hero rotates over 10 samples', delta > 0.25, yaws.map(v => v.toFixed(2)).join('→') + ' delta=' + delta.toFixed(2));
  check(9, 'motion not paused', await p.evaluate(() => !window.PartyabMotion.reduced()));
}
await stubHero();

// Pass 10: reduced motion, pause, mobile nav
await p.emulateMedia({ reducedMotion: 'reduce' }); await go('/'); await p.waitForTimeout(400);
check(10, 'reduced reveal visible', await p.locator('[data-reveal]').first().evaluate(el => getComputedStyle(el).opacity) === '1');
check(10, 'reduced marquee still', await css('.pb-marquee__track', 'animationName') === 'none');
await p.evaluate(() => { window.PartyabTransition.show(); }); await p.waitForTimeout(80);
check(10, 'reduced loader static', await p.locator('.pb-page-loader').evaluate(el => el.classList.contains('is-static')));
await p.evaluate(() => { window.PartyabTransition.hide(); });
await p.emulateMedia({ reducedMotion: 'no-preference' });
await p.setViewportSize({ width: 390, height: 844 }); await go('/'); await p.waitForTimeout(400);
await p.locator('[data-pb-nav-toggle]').click(); await p.waitForTimeout(200);
check(10, 'mobile nav opens', await p.locator('.pb-nav').evaluate(el => el.classList.contains('is-open')));
check(10, 'mobile no overflow', !(await p.evaluate(() => document.documentElement.scrollWidth > innerWidth + 1)));
check(10, 'no page/console errors', errors.length === 0, errors.join(' | '));

writeFileSync('preview/deep-audit.json', JSON.stringify({ date: new Date().toISOString(), results, errors, failed }, null, 2));
const fails = results.filter(r => !r.ok);
console.log('\n' + (results.length - fails.length) + '/' + results.length + ' checks passed; ' + fails.length + ' failures');
await b.close();
process.exit(fails.length ? 1 : 0);
