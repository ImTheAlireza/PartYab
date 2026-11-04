import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: [...chromium.args, '--enable-unsafe-swiftshader'], headless: true });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 } });
p.setDefaultTimeout(60000);
const no3d = () => p.route('**/assets/js/hero-3d.js', r => r.fulfill({ contentType: 'text/javascript', body: '' }));
const yes3d = () => p.unroute('**/assets/js/hero-3d.js');
await no3d();
await p.goto('http://127.0.0.1:3000/', { waitUntil: 'domcontentloaded' }); await p.waitForTimeout(700);
// product hover: outer static, surface lifted
const card = p.locator('.pb-product-card').first(); await card.scrollIntoViewIfNeeded(); await p.waitForTimeout(800);
const kb = await card.boundingBox(); await p.mouse.move(kb.x + kb.width / 2, kb.y + 40); await p.waitForTimeout(500);
await p.screenshot({ path: 'preview/round5-product-hover.png', clip: { x: Math.max(0, kb.x - 30), y: Math.max(0, kb.y - 30), width: Math.min(1440, kb.width + 60), height: kb.height + 60 } });
// loader logo only
await p.evaluate(() => { window.PartyabTransition.show(); }); await p.waitForTimeout(650);
await p.screenshot({ path: 'preview/round5-loader-logo.png' });
await p.evaluate(() => { window.PartyabTransition.hide(); }); await p.waitForTimeout(180);
await p.screenshot({ path: 'preview/round5-loader-slide.png' });
await p.waitForTimeout(500);
// footer sound toggle
await p.locator('[data-sound-toggle]').scrollIntoViewIfNeeded(); await p.waitForTimeout(400);
await p.screenshot({ path: 'preview/round5-sound-toggle.png', clip: { x: 0, y: 700, width: 1440, height: 300 } });
// hero with new lighting + idle rotation
await yes3d();
await p.goto('http://127.0.0.1:3000/', { waitUntil: 'domcontentloaded' });
await p.waitForFunction(() => window.PartyabHero3D?.ready, { timeout: 30000 }); await p.waitForTimeout(1800);
await p.screenshot({ path: 'preview/round5-hero.png' });
await p.setViewportSize({ width: 390, height: 844 }); await p.waitForTimeout(1000);
await p.screenshot({ path: 'preview/round5-hero-mobile.png' });
console.log('round5 shots done');
await b.close();
