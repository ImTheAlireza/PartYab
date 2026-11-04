import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
import { writeFileSync } from 'node:fs';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: [...chromium.args, '--enable-unsafe-swiftshader'], headless: true });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 } });
p.setDefaultTimeout(30000);
const issues = [];
const note = (run, area, msg) => { issues.push({ run, area, msg }); console.log('ISSUE run' + run + ' ' + area + ': ' + msg); };
p.on('pageerror', e => note(current, 'pageerror', e.message));
p.on('console', m => { if (m.type() === 'error') note(current, 'console', m.text() + ' @ ' + m.location().url); });
p.on('response', r => { if (r.status() >= 400 && !r.url().endsWith('/404')) note(current, 'http', r.status() + ' ' + r.url()); });
let current = 0;
const go = path => p.goto('http://127.0.0.1:3000' + path, { waitUntil: 'domcontentloaded' });
const runs = [];
for (let run = 1; run <= 10; run++) {
  current = run;
  const r = { run, navDouble: 0, productFlicker: 0, loaderFail: 0, heroDelta: null, overflow: [] };
  // home + nav leave double underline
  await p.route('**/assets/js/hero-3d.js', route => route.fulfill({ contentType: 'text/javascript', body: '' }));
  await go('/'); await p.waitForTimeout(450);
  for (let i = 0; i < 4; i++) {
    const link = p.locator('.pb-nav__menu > li > a').nth(i + 1);
    await link.hover(); await p.waitForTimeout(360);
    await p.mouse.move(5, 5); await p.waitForTimeout(140);
    const layers = await link.evaluate(el => { const s = getComputedStyle(el), bf = getComputedStyle(el, '::before'); const bg = s.backgroundImage !== 'none' && parseFloat(s.backgroundSize) > 2; const ps = bf.content !== 'none' && bf.transform !== 'none' && !/^matrix\(0, 0, 0, 1/.test(bf.transform) && bf.transform !== 'matrix(1, 0, 0, 1, 0, 0)'; return { bg, ps, bgSize: s.backgroundSize, psTf: bf.transform }; });
    if (layers.bg && layers.ps) r.navDouble++;
  }
  // product edge 5 cycles
  const card = p.locator('.pb-product-card').first(); await card.scrollIntoViewIfNeeded(); await p.waitForTimeout(650);
  for (let i = 0; i < 5; i++) {
    const box = await card.boundingBox(); await p.mouse.move(box.x + box.width / 2, box.y + box.height - 2); await p.waitForTimeout(400);
    const vals = [];
    for (let j = 0; j < 3; j++) { vals.push(await card.locator('.pb-product-card__surface').evaluate(el => getComputedStyle(el).translate)); await p.waitForTimeout(80); }
    if (!vals.every(v => v === vals[0] && v !== 'none' && v !== '0px 0px')) r.productFlicker++;
    await p.mouse.move(box.x + box.width / 2, box.y - 40); await p.waitForTimeout(250);
  }
  // loader 3 cycles, wait for full cover and full hide
  await go('/contact'); await p.waitForTimeout(350);
  for (let i = 0; i < 3; i++) {
    await p.evaluate(() => { window.PartyabTransition.show(); });
    const covered = await p.waitForFunction(() => { const el = document.querySelector('.pb-page-loader'); return el.classList.contains('is-active') && getComputedStyle(el).transform === 'none'; }, { timeout: 4000 }).then(() => true).catch(() => false);
    await p.evaluate(() => { window.PartyabTransition.hide(); });
    const hidden = await p.waitForFunction(() => document.querySelector('.pb-page-loader').hidden, { timeout: 4000 }).then(() => true).catch(() => false);
    if (!covered || !hidden) r.loaderFail++;
  }
  // routes overflow
  for (const path of ['/shop', '/product/brake-pad', '/cart', '/checkout', '/account', '/my-car', '/faq']) { await go(path); await p.waitForTimeout(180); if (await p.evaluate(() => document.documentElement.scrollWidth > innerWidth + 1)) r.overflow.push(path); }
  // hero real rotation
  await p.unroute('**/assets/js/hero-3d.js');
  await go('/'); await p.waitForFunction(() => window.PartyabHero3D?.ready, { timeout: 25000 }).catch(() => {});
  if (await p.evaluate(() => !!window.PartyabHero3D?.ready)) { const y0 = await p.evaluate(() => window.PartyabHero3D.yaw); await p.waitForTimeout(2500); const y1 = await p.evaluate(() => window.PartyabHero3D.yaw); r.heroDelta = +(y1 - y0).toFixed(3); }
  runs.push(r);
  console.log('run', run, JSON.stringify(r));
}
writeFileSync('preview/audit-10x.json', JSON.stringify({ date: new Date().toISOString(), runs, issues }, null, 2));
console.log('SUMMARY', JSON.stringify({ navDouble: runs.reduce((a, r) => a + r.navDouble, 0), productFlicker: runs.reduce((a, r) => a + r.productFlicker, 0), loaderFail: runs.reduce((a, r) => a + r.loaderFail, 0), heroDeltas: runs.map(r => r.heroDelta), issues: issues.length }, null, 2));
await b.close();
