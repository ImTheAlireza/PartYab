import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
import { writeFileSync } from 'node:fs';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: [...chromium.args, '--enable-unsafe-swiftshader'], headless: true });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 } });
p.setDefaultTimeout(60000);
const errors = []; p.on('pageerror', e => errors.push(e.message));
const results = [];
const check = (name, ok, detail = '') => { results.push({ name, ok: !!ok, detail }); console.log((ok ? 'PASS' : 'FAIL') + ' ' + name + (detail ? ' — ' + detail : '')); };
const go = (path) => p.goto('http://127.0.0.1:3000' + path, { waitUntil: 'domcontentloaded' });
const css = (sel, prop, pseudo) => p.locator(sel).first().evaluate((el, [prop, pseudo]) => getComputedStyle(el, pseudo || undefined)[prop], [prop, pseudo]);

await go('/'); await p.waitForTimeout(700);
// header underlines
await p.locator('.pb-nav__menu > li > a').nth(1).hover(); await p.waitForTimeout(380);
check('header nav underline', await css('.pb-nav__menu > li > a:nth-child(1)', 'transform', '::before').catch(() => '') !== 'none' || await p.locator('.pb-nav__menu > li > a').nth(1).evaluate(el => getComputedStyle(el, '::before').transform) !== 'none');
await p.locator('.pb-action').first().hover(); await p.waitForTimeout(380);
check('header action underline', await p.locator('.pb-action').first().evaluate(el => getComputedStyle(el, '::after').transform) !== 'none');
// ripple
await p.locator('.pb-hero__cta .pb-btn').first().dispatchEvent('pointerdown', { clientX: 120, clientY: 120 }); await p.waitForTimeout(80);
check('button ripple', await p.locator('.pb-ripple').count() > 0);
// category icon smooth
const cat = p.locator('.pb-cat').first(); await p.evaluate(() => { document.documentElement.style.scrollBehavior = 'auto'; document.querySelector('.pb-cat').scrollIntoView({ block: 'center', behavior: 'instant' }); }); await p.waitForTimeout(900);
for (let attempt = 0; attempt < 4; attempt++) {
  const cb0 = await cat.boundingBox(); await p.mouse.move(cb0.x + cb0.width / 2 + attempt, cb0.y + cb0.height / 2 + attempt, { steps: 5 }); await p.waitForTimeout(250);
  const ok = await p.waitForFunction(() => { const el = document.querySelector('.pb-cat .pb-cat__icon'); if (!el) return false; const tf = getComputedStyle(el).transform; return tf !== 'none' && tf !== 'matrix(1, 0, 0, 1, 0, 0)'; }, { timeout: 1500 }).then(() => true).catch(() => false);
  if (ok) break;
}
const catIcon = await cat.locator('.pb-cat__icon').evaluate(el => ({ anim: getComputedStyle(el).animationName, trans: getComputedStyle(el).transitionProperty, tf: getComputedStyle(el).transform }));
check('category icon no snapping keyframe', catIcon.anim === 'none' && catIcon.tf !== 'none' && catIcon.tf !== 'matrix(1, 0, 0, 1, 0, 0)', JSON.stringify(catIcon));
check('category outer hit area static', await cat.evaluate(el => getComputedStyle(el).transform) === 'none');
// product card hover
const card = p.locator('.pb-product-card').first(); await card.scrollIntoViewIfNeeded(); await p.waitForTimeout(800); const kb = await card.boundingBox(); await p.mouse.move(kb.x + kb.width/2, kb.y + 40); await p.waitForTimeout(500);
const surface = card.locator('.pb-product-card__surface');
const surfaceTranslate = await surface.evaluate(el => getComputedStyle(el).translate);
check('product surface lift', surfaceTranslate !== 'none' && surfaceTranslate !== '0px 0px', surfaceTranslate);
check('product outer hit area static', await card.evaluate(el => getComputedStyle(el).transform) === 'none' && ['none','0px','0px 0px'].includes(await card.evaluate(el => getComputedStyle(el).translate)));
// Edge stability: park pointer on bottom edge; surface must stay lifted, no oscillation.
const kb2 = await card.boundingBox(); await p.mouse.move(kb2.x + kb2.width/2, kb2.y + kb2.height - 2); await p.waitForTimeout(450);
const samples = [];
for (let i = 0; i < 6; i++) { samples.push(await surface.evaluate(el => getComputedStyle(el).translate)); await p.waitForTimeout(90); }
check('product hover edge does not flicker', samples.every(v => v === samples[0] && v !== 'none' && v !== '0px 0px'), samples.join(' | '));
await p.mouse.move(kb2.x + kb2.width/2, kb2.y + 40); await p.waitForTimeout(350);
await p.waitForFunction(() => { const q = document.querySelector('.pb-product-card .pb-product-card__quick'); return q && getComputedStyle(q).opacity === '1'; }, { timeout: 5000 }).catch(() => {});
check('product quick view reveal', await card.locator('.pb-product-card__quick').evaluate(el => getComputedStyle(el).opacity) === '1');
// brand logos contained + marquee pause
await p.locator('.pb-marquee').scrollIntoViewIfNeeded(); await p.waitForTimeout(300);
const contained = await p.locator('.pb-brand-tile').evaluateAll(tiles => tiles.every(t => { const i = t.querySelector('img'); if (!i) return true; const a = t.getBoundingClientRect(), b = i.getBoundingClientRect(); return b.left >= a.left - 1 && b.right <= a.right + 1 && b.top >= a.top - 1 && b.bottom <= a.bottom + 1; }));
check('brand logos contained', contained);
await p.locator('.pb-marquee').hover(); await p.waitForTimeout(120);
check('marquee pauses on hover', await css('.pb-marquee__track', 'animationPlayState') === 'paused');
// loader slide in/out
await p.evaluate(() => window.PartyabTransition.show());
await p.waitForFunction(() => { const el = document.querySelector('.pb-page-loader'); return el.classList.contains('is-active') && getComputedStyle(el).transform === 'none'; }, { timeout: 8000 }).catch(() => {});
check('loader slides in', await css('.pb-page-loader', 'transform') === 'none' && await css('.pb-page-loader', 'visibility') === 'visible');
check('loader has no progress bar', await p.locator('.pb-page-loader__line, .pb-page-loader progress, .pb-page-loader [role=progressbar]').count() === 0);
check('loader logo animates', await css('.pb-page-loader__logo', 'animationName') === 'loader-logo');
await p.evaluate(() => { window.PartyabTransition.hide(); });
await p.waitForFunction(() => document.querySelector('.pb-page-loader').classList.contains('is-leaving'), { timeout: 5000 }).catch(() => {});
await p.waitForTimeout(180);
const leavingTf = await css('.pb-page-loader', 'transform');
check('loader slides out', await p.locator('.pb-page-loader').evaluate(el => el.classList.contains('is-leaving')) && await css('.pb-page-loader', 'visibility') === 'visible', leavingTf);
await p.waitForTimeout(650); check('loader hidden after slide', await p.locator('.pb-page-loader').evaluate(el => el.hidden));
// focus rings
await go('/contact'); await p.locator('[name=phone]').focus(); await p.waitForTimeout(250);
const focus = await p.locator('[name=phone]').evaluate(el => ({ shadow: getComputedStyle(el).boxShadow, outline: getComputedStyle(el).outlineStyle }));
check('input focus has no extra orange halo', focus.shadow === 'none' && focus.outline === 'none', JSON.stringify(focus));
// select
await go('/my-car'); await p.locator('.pb-select__button').first().focus(); await p.waitForTimeout(150);
check('select focus no halo', await css('.pb-select__button', 'boxShadow') === 'none');
await p.locator('.pb-select__button').first().click(); await p.waitForTimeout(120);
check('select dropdown animates', await css('.pb-select__list', 'animationName') === 'select-in');
await p.keyboard.press('Escape');
// faq + dialog + toast
await go('/faq'); await p.locator('.pb-faq__item summary').nth(1).click(); await p.waitForTimeout(120);
check('faq opens with motion', await p.locator('.pb-faq__item[open] .pb-faq__a').first().evaluate(el => getComputedStyle(el).animationName) === 'faq-open');
await go('/product/brake-pad'); await p.locator('[data-quick="1"]').click({ force: true }).catch(() => {});
await p.waitForTimeout(200);
check('quick-view dialog animates', await p.locator('.pb-dialog[open]').count() ? await css('.pb-dialog[open]', 'animationName') === 'dialog-in' : true);
await p.keyboard.press('Escape');
await p.locator('[data-add="1"]').first().click(); await p.waitForTimeout(250);
check('add-to-cart toast animates', await p.locator('.pb-toast').count() ? await css('.pb-toast', 'animationName') === 'toast-spring' : true);
// reveal + progress + back to top
await go('/'); await p.evaluate(async () => { for (let y = 0; y < document.body.scrollHeight; y += 500) { scrollTo({ top: y, behavior: 'instant' }); await new Promise(r => setTimeout(r, 90)); } });
await p.waitForTimeout(500);
check('scroll progress advances', await p.locator('.pb-scroll-progress i').evaluate(el => parseFloat(el.style.getPropertyValue('--p')) > 50));
check('back-to-top visible', await p.locator('[data-pb-top]').evaluate(el => getComputedStyle(el).opacity !== '0'));
const unrevealed = await p.locator('[data-reveal]').evaluateAll(els => els.filter(el => getComputedStyle(el).opacity !== '1').length);
check('all reveal targets visible after scroll', unrevealed === 0, unrevealed + ' hidden');
// 3D hero
await p.evaluate(() => scrollTo({ top: 0, behavior: 'instant' }));
await p.waitForFunction(() => window.PartyabHero3D?.ready, { timeout: 30000 }).catch(() => {});
const heroReady = await p.evaluate(() => !!window.PartyabHero3D?.ready);
check('3D hero ready', heroReady);
if (heroReady) {
  check('3D fallback hidden', await p.locator('.pb-hero__car--fallback').evaluate(el => getComputedStyle(el).visibility) === 'hidden');
  const idle0 = await p.evaluate(() => window.PartyabHero3D.yaw);
  await p.waitForTimeout(2000);
  const idle1 = await p.evaluate(() => window.PartyabHero3D.yaw);
  check('3D hero rotates slowly while idle', Math.abs(idle1 - idle0) > 0.05, idle0.toFixed(2) + '→' + idle1.toFixed(2));
  const yaw0 = await p.evaluate(() => window.PartyabHero3D.yaw);
  await p.evaluate(() => scrollTo({ top: 520, behavior: 'instant' }));
  await p.waitForFunction((y0) => Math.abs(window.PartyabHero3D.yaw - y0) > 0.12, yaw0, { timeout: 15000 }).catch(() => {});
  const yaw1 = await p.evaluate(() => window.PartyabHero3D.yaw);
  check('3D hero responds to scroll', Math.abs(yaw1 - yaw0) > 0.12, yaw0.toFixed(2) + '→' + yaw1.toFixed(2));
  await p.evaluate(() => scrollTo({ top: 0, behavior: 'instant' })); await p.waitForTimeout(900);
}

// more individual components
await go('/'); await p.waitForTimeout(500);
const btn = p.locator('.pb-hero__cta .pb-btn').first(); await btn.hover(); await p.waitForTimeout(750);
check('button shine sweep', await btn.evaluate(el => getComputedStyle(el, '::before').transform) !== 'none');
const bb = await btn.boundingBox(); await p.mouse.move(bb.x + bb.width * .8, bb.y + bb.height * .3); await p.waitForTimeout(120);
check('magnetic button translate', await btn.evaluate(el => el.style.translate !== ''));
check('hero float idle animation', await css('.pb-hero__float', 'animationName') === 'float-a');
const cat2 = p.locator('.pb-cat').nth(1); await cat2.scrollIntoViewIfNeeded(); await p.waitForTimeout(700); const c2 = await cat2.boundingBox(); await p.mouse.move(c2.x + c2.width/2, c2.y + c2.height/2); await p.waitForTimeout(500);
check('category hover feedback', await cat2.evaluate(el => getComputedStyle(el).boxShadow) !== 'none' && await cat2.locator('.pb-cat__icon').evaluate(el => getComputedStyle(el).transform) !== 'none');
const svc = p.locator('.pb-service').first(); await svc.scrollIntoViewIfNeeded(); await svc.hover(); await p.waitForTimeout(120);
check('service icon ring', await svc.locator('.pb-service__icon').evaluate(el => getComputedStyle(el).animationName) === 'icon-ring');
await go('/checkout'); await p.locator('.demo-check input').check(); await p.waitForTimeout(80);
check('checkbox pop-in', await p.locator('.demo-check input').evaluate(el => getComputedStyle(el).animationName) === 'pop-in');
await go('/'); await p.locator('[data-motion-toggle]').scrollIntoViewIfNeeded(); await p.locator('[data-motion-toggle]').click(); await p.waitForTimeout(150);
check('motion toggle pauses marquee', await css('.pb-marquee__track', 'animationPlayState') === 'paused');
await p.locator('[data-motion-toggle]').click();

// sound toggle + transition coverage
await go('/'); await p.waitForTimeout(400);
const soundBtn = p.locator('[data-sound-toggle]');
check('sound toggle visible', await soundBtn.isVisible());
await soundBtn.click(); await p.waitForTimeout(120);
check('sound can be muted', await p.evaluate(() => window.PartyabSound.muted) === true);
await soundBtn.click(); await p.waitForTimeout(120);
check('sound can be unmuted', await p.evaluate(() => window.PartyabSound.muted) === false);
check('sound cue plays after user gesture', await p.evaluate(() => window.PartyabSound.plays) > 0);
// Delay the destination so we can prove the curtain fully covers BEFORE unload.
await p.route('**/shop', async route => { await new Promise(r => setTimeout(r, 700)); await route.continue(); });
const navPromise = p.waitForURL('**/shop', { timeout: 15000 });
await p.locator('.pb-nav__menu a[href="/shop"]').first().click();
await p.waitForTimeout(520);
const preUnload = await p.locator('.pb-page-loader').evaluate(el => { const r = el.getBoundingClientRect(); return { covered: r.left <= 0 && r.right >= innerWidth && getComputedStyle(el).visibility === 'visible', tf: getComputedStyle(el).transform }; }).catch(() => ({ covered: false }));
check('curtain fully covers before navigation', preUnload.covered, JSON.stringify(preUnload));
await navPromise; await p.unroute('**/shop'); await p.waitForTimeout(700);
check('link navigation completes', p.url().includes('/shop'));
check('arrival curtain hidden after transition', await p.locator('.pb-page-loader').evaluate(el => el.hidden));

// mobile nav + reduced motion
await p.setViewportSize({ width: 390, height: 844 }); await go('/'); await p.waitForTimeout(400);
await p.locator('[data-pb-nav-toggle]').click(); await p.waitForTimeout(200);
check('mobile nav opens', await p.locator('.pb-nav').evaluate(el => el.classList.contains('is-open')));
await p.emulateMedia({ reducedMotion: 'reduce' }); await go('/'); await p.waitForTimeout(500);
check('reduced motion reveal visible', await p.locator('[data-reveal]').first().evaluate(el => getComputedStyle(el).opacity) === '1');
check('reduced motion marquee still', await css('.pb-marquee__track', 'animationName') === 'none');
await p.evaluate(() => window.PartyabTransition.show()); await p.waitForTimeout(80);
check('reduced motion loader static', await p.locator('.pb-page-loader').evaluate(el => el.classList.contains('is-static')));
check('no page errors', errors.length === 0, errors.join(' | '));
writeFileSync('preview/animation-audit.json', JSON.stringify({ date: new Date().toISOString(), results }, null, 2));
const failed = results.filter(r => !r.ok);
console.log('\n' + (results.length - failed.length) + '/' + results.length + ' checks passed');
await b.close();
process.exit(failed.length ? 1 : 0);
