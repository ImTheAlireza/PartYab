// Page-transition regression: the arrival curtain must never stay stuck.
import assert from 'node:assert/strict';
import { writeFile } from 'node:fs/promises';
import { chromium as playwright } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';

await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br');
setupLambdaEnvironment('/tmp/al2023/lib');
const base = process.env.PREVIEW_URL || 'http://127.0.0.1:3000';
const browser = await playwright.launch({ executablePath: await chromium.executablePath(), args: [...chromium.args.filter(a => a !== '--single-process'), '--enable-unsafe-swiftshader'], headless: true });
const results = [];
const KEY = 'partyab-page-transition';

async function scenario(name, fn) {
  const context = await browser.newContext({ viewport: { width: 1280, height: 900 } });
  const page = await context.newPage();
  page.setDefaultTimeout(30000);
  const errors = [];
  page.on('pageerror', e => errors.push(e.message));
  const start = Date.now();
  try {
    await fn(page, context);
    assert.deepEqual(errors.filter(e => !/favicon|404|broken transition/.test(e)), []);
    results.push({ name, pass: true, ms: Date.now() - start });
  } catch (e) {
    results.push({ name, pass: false, ms: Date.now() - start, error: e.message, errors });
  } finally { await context.close(); }
  console.log(JSON.stringify(results.at(-1)));
}
async function loaderGone(page, timeout = 6000) {
  await page.waitForFunction(() => {
    const el = document.querySelector('.pb-page-loader');
    const root = document.documentElement;
    return !root.classList.contains('pb-page-arriving') && (!el || el.hidden || getComputedStyle(el).visibility === 'hidden');
  }, { timeout });
}
async function armArrival(page) {
  await page.evaluate(k => sessionStorage.setItem(k, JSON.stringify({ at: Date.now(), url: location.href })), KEY);
}

try {
  await scenario('normal link navigation clears loader', async page => {
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await page.click('a[href="/shop"]');
    await page.waitForURL('**/shop');
    await loaderGone(page);
    await page.click('a[href="/"]');
    await page.waitForURL(base + '/');
    await loaderGone(page);
  });

  await scenario('back/forward clears loader', async page => {
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await page.click('a[href="/contact"]');
    await page.waitForURL('**/contact');
    await loaderGone(page);
    await page.goBack();
    await loaderGone(page);
    await page.goForward();
    await loaderGone(page);
  });

  await scenario('blocked transition script still releases boot cover', async (page, context) => {
    await context.route('**/assets/js/page-transition.js', r => r.abort());
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await armArrival(page);
    await page.goto(base + '/shop', { waitUntil: 'domcontentloaded' });
    await loaderGone(page, 5000);
  });

  await scenario('delayed transition script does not re-stick cover', async (page, context) => {
    await context.route('**/assets/js/page-transition.js', async r => { await new Promise(res => setTimeout(res, 4000)); await r.continue(); });
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await armArrival(page);
    await page.goto(base + '/shop', { waitUntil: 'domcontentloaded' });
    await loaderGone(page, 5000);
    await page.waitForTimeout(2000);
    await loaderGone(page, 1000);
  });

  await scenario('broken transition script still releases boot cover', async (page, context) => {
    await context.route('**/assets/js/page-transition.js', r => r.fulfill({ contentType: 'text/javascript', body: 'throw new Error("broken transition");' }));
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await armArrival(page);
    await page.goto(base + '/shop', { waitUntil: 'domcontentloaded' });
    await loaderGone(page, 5000);
  });

  await scenario('reduced motion never shows arrival cover', async page => {
    await page.emulateMedia({ reducedMotion: 'reduce' });
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await armArrival(page);
    await page.goto(base + '/shop', { waitUntil: 'domcontentloaded' });
    const arriving = await page.evaluate(() => document.documentElement.classList.contains('pb-page-arriving'));
    assert.equal(arriving, false);
    await loaderGone(page, 2000);
  });

  await scenario('rapid consecutive navigations clear loader', async page => {
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await page.click('a[href="/shop"]');
    await page.waitForURL('**/shop');
    await loaderGone(page);
    await page.click('a[href="/faq"]');
    await page.waitForURL('**/faq');
    await loaderGone(page);
    await page.click('a[href="/contact"]');
    await page.waitForURL('**/contact');
    await loaderGone(page);
  });

  await scenario('GET search form navigation clears loader', async page => {
    await page.goto(base + '/', { waitUntil: 'domcontentloaded' });
    await page.fill('#demo-search', 'لنت');
    await page.press('#demo-search', 'Enter');
    await page.waitForURL('**/shop?**');
    await loaderGone(page);
  });
} finally {
  await browser.close();
  await writeFile('preview/transition-check.json', JSON.stringify(results, null, 2) + '\n');
}
const failed = results.filter(r => !r.pass);
console.log(`${results.length - failed.length}/${results.length} transition scenarios passed`);
if (failed.length) process.exitCode = 1;
