import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
import { writeFileSync } from 'node:fs';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: [...chromium.args, '--enable-unsafe-swiftshader'], headless: true });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 } });
p.setDefaultTimeout(60000);
const out = { navDouble: 0, productFlicker: 0, loaderFail: 0, heroDeltas: [], errors: [] };
p.on('pageerror', e => out.errors.push(e.message));
p.on('console', m => { if (m.type() === 'error' && !m.location().url.endsWith('/404')) out.errors.push(m.text()); });
const go = path => p.goto('http://127.0.0.1:3000' + path, { waitUntil: 'domcontentloaded' });
await p.route('**/assets/js/hero-3d.js', r => r.fulfill({ contentType: 'text/javascript', body: '' }));
await go('/'); await p.waitForTimeout(600);
for (let i = 0; i < 40; i++) {
  const link = p.locator('.pb-nav__menu > li > a').nth((i % 7) + 1);
  await link.hover(); await p.waitForTimeout(330);
  await p.mouse.move(5, 5); await p.waitForTimeout(140);
  const layers = await link.evaluate(el => { const s = getComputedStyle(el), bf = getComputedStyle(el, '::before'); const bg = s.backgroundImage !== 'none' && parseFloat(s.backgroundSize) > 2; const ps = bf.content !== 'none' && bf.transform !== 'none' && !/^matrix\(0, 0, 0, 1/.test(bf.transform) && bf.transform !== 'matrix(1, 0, 0, 1, 0, 0)'; return bg && ps; });
  if (layers) out.navDouble++;
}
const card = p.locator('.pb-product-card').first(); await card.scrollIntoViewIfNeeded(); await p.waitForTimeout(900);
for (let i = 0; i < 30; i++) {
  const box = await card.boundingBox(); await p.mouse.move(box.x + box.width / 2, box.y + box.height - 2);
  await p.waitForFunction(() => { const el = document.querySelector('.pb-product-card .pb-product-card__surface'); return el && getComputedStyle(el).translate === '0px -6px'; }, { timeout: 3000 }).catch(() => {});
  const vals = [];
  for (let j = 0; j < 4; j++) { vals.push(await card.evaluate(el => ({ tr: getComputedStyle(el.querySelector('.pb-product-card__surface')).translate, hover: el.matches(':hover') }))); await p.waitForTimeout(80); }
  const settled = vals.every(v => v.tr === '0px -6px' && v.hover);
  if (!settled) out.productFlicker++;
  await p.mouse.move(box.x + box.width / 2, box.y - 40); await p.waitForTimeout(260);
}
await go('/contact'); await p.waitForTimeout(400);
for (let i = 0; i < 30; i++) {
  await p.evaluate(() => { window.PartyabTransition.show(); });
  const covered = await p.waitForFunction(() => { const el = document.querySelector('.pb-page-loader'); return el.classList.contains('is-active') && getComputedStyle(el).transform === 'none'; }, { timeout: 4000 }).then(() => true).catch(() => false);
  await p.evaluate(() => { window.PartyabTransition.hide(); });
  const hidden = await p.waitForFunction(() => document.querySelector('.pb-page-loader').hidden, { timeout: 4000 }).then(() => true).catch(() => false);
  if (!covered || !hidden) out.loaderFail++;
}
await p.unroute('**/assets/js/hero-3d.js');
for (let i = 0; i < 1; i++) {
  await go('/'); await p.waitForFunction(() => window.PartyabHero3D?.ready, { timeout: 45000 }).catch(() => {});
  const y0 = await p.evaluate(() => window.PartyabHero3D?.yaw); await p.waitForTimeout(2500); const y1 = await p.evaluate(() => window.PartyabHero3D?.yaw);
  out.heroDeltas.push(+(y1 - y0).toFixed(3));
}
writeFileSync('preview/verify-fixes.json', JSON.stringify(out, null, 2));
console.log(JSON.stringify(out, null, 2));
await b.close();
process.exit(out.navDouble || out.productFlicker || out.loaderFail || out.errors.length ? 1 : 0);
