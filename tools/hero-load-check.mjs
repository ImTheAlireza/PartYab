// Real hero loading checks: no script stubs. Also exercises dependency failure paths.
import assert from 'node:assert/strict';
import { mkdir, writeFile } from 'node:fs/promises';
import { chromium as playwright } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';

await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br');
setupLambdaEnvironment('/tmp/al2023/lib');
const base = process.env.PREVIEW_URL || 'http://127.0.0.1:3000';
const browser = await playwright.launch({ executablePath: await chromium.executablePath(), args: [...chromium.args.filter(arg => arg !== '--single-process'), '--enable-unsafe-swiftshader'], headless: true });
const results = [];
await mkdir('preview', { recursive: true });

async function run(name, { viewport = { width: 1440, height: 1000 }, route, init, reducedMotion = 'no-preference' } = {}) {
  const context = await browser.newContext({ viewport, reducedMotion });
  const page = await context.newPage();
  page.setDefaultTimeout(90000);
  const errors = [], requests = [], failures = [];
  page.on('pageerror', e => errors.push(e.message));
  page.on('requestfailed', r => failures.push({ url: r.url(), reason: r.failure()?.errorText }));
  page.on('response', r => { if (/\.(glb|wasm)|draco_wasm_wrapper|hero-3d\.js/.test(r.url())) requests.push({ url: r.url(), status: r.status() }); });
  if (init) await page.addInitScript(init);
  if (route) await page.route('**/*', route);
  const start = Date.now();
  try {
    await page.goto(base + '/', { waitUntil: 'domcontentloaded', timeout: 90000 });
    if (name === 'slow model keeps poster') {
      await page.waitForTimeout(600);
      const early = await page.evaluate(() => ({ ready: window.PartyabHero3D?.ready, fallback: getComputedStyle(document.querySelector('.pb-hero__car--fallback')).visibility }));
      assert.equal(early.ready, false); assert.equal(early.fallback, 'visible');
    }
    const expect3d = ['desktop cold load','mobile cold load','legacy decoder blocked','slow model keeps poster'].includes(name);
    if (expect3d) {
      await page.waitForFunction(() => window.PartyabHero3D?.ready === true, { timeout: 90000 });
      // Wait until CSS has actually revealed the canvas and there are non-transparent car pixels.
      await page.waitForFunction(() => {
        const canvas = document.querySelector('[data-hero-car]');
        if (!canvas || getComputedStyle(canvas).opacity !== '1') return false;
        const sample = document.createElement('canvas'); sample.width = 80; sample.height = 60;
        const ctx = sample.getContext('2d'); ctx.drawImage(canvas, 0, 0, 80, 60);
        const pixels = ctx.getImageData(0, 0, 80, 60).data;
        let solid = 0; for (let i = 3; i < pixels.length; i += 4) if (pixels[i] > 80) solid++;
        return solid > 100;
      }, { timeout: 30000 });
    } else {
      await page.waitForTimeout(2200);
    }
    const state = await page.evaluate(() => {
      const c = document.querySelector('[data-hero-car]'), f = document.querySelector('.pb-hero__car--fallback');
      return { hero: window.PartyabHero3D || null, canvasOpacity: getComputedStyle(c).opacity, fallbackOpacity: getComputedStyle(f).opacity, fallbackVisibility: getComputedStyle(f).visibility, fallbackLoaded: f.complete && f.naturalWidth > 0, curtainHidden: document.querySelector('.pb-page-loader')?.hidden };
    });
    if (expect3d) {
      assert.equal(state.canvasOpacity, '1');
      assert.equal(state.fallbackVisibility, 'hidden');
      assert.equal(state.fallbackLoaded, true);
      assert.deepEqual(errors, []);
      assert.deepEqual(failures.filter(f => !/favicon/.test(f.url)), []);
      assert.equal(requests.some(r => /draco|\.wasm/.test(r.url)), false, 'hero must not need a runtime decoder');
      await page.screenshot({ path: `preview/hero-load-${name.startsWith('mobile') ? 'mobile' : 'desktop'}.png` });
    } else {
      assert.equal(state.fallbackLoaded, true);
      assert.equal(state.fallbackVisibility, 'visible');
      assert.notEqual(state.fallbackOpacity, '0');
      assert.equal(state.curtainHidden, true);
    }
    const result = { name, pass: true, ms: Date.now() - start, state, requests, errors, failures };
    results.push(result); console.log(JSON.stringify(result));
  } catch (error) {
    const result = { name, pass: false, ms: Date.now() - start, error: error.message, requests, errors, failures };
    results.push(result); console.log(JSON.stringify(result));
  } finally { await context.close(); }
}

try {
  await run('desktop cold load');
  await run('mobile cold load', { viewport: { width: 390, height: 844 } });
  await run('legacy decoder blocked', { route: async r => /draco/.test(r.request().url()) ? r.abort() : r.continue() });
  await run('slow model keeps poster', { route: async r => {
    if (/\.glb(?:\?|$)/.test(r.request().url())) await new Promise(res => setTimeout(res, 4000));
    await r.continue();
  } });
  await run('script unavailable', { route: async r => /hero-3d\.js/.test(r.request().url()) ? r.fulfill({ status: 500, body: '' }) : r.continue() });
  await run('model unavailable', { route: async r => /\.glb(?:\?|$)/.test(r.request().url()) ? r.fulfill({ status: 404, body: 'missing' }) : r.continue() });
  await run('WebGL unavailable', { init: () => {
    const original = HTMLCanvasElement.prototype.getContext;
    HTMLCanvasElement.prototype.getContext = function(type, ...args) { return /webgl/.test(type) ? null : original.call(this, type, ...args); };
  } });
} finally {
  await browser.close();
  await writeFile('preview/hero-load-check.json', JSON.stringify(results, null, 2) + '\n');
}
const failed = results.filter(r => !r.pass);
console.log(`${results.length - failed.length}/${results.length} hero loading scenarios passed`);
if (failed.length) process.exitCode = 1;
