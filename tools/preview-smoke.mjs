import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
import { join } from 'node:path';
await inflate(join(process.cwd(), 'node_modules/@sparticuz/chromium/bin/al2023.tar.br'));
setupLambdaEnvironment('/tmp/al2023/lib');
process.env.FONTCONFIG_PATH ||= '/tmp/fonts';
const browser = await pw.launch({ executablePath: await chromium.executablePath(), args: chromium.args, headless: true });
const page = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });
const errors = [];
page.on('pageerror', e => errors.push('pageerror: ' + e.message));
page.on('console', m => { if (m.type() === 'error' && !page.url().endsWith('/404')) errors.push('console: ' + m.text()); });
page.on('response', r => { if (r.status() >= 400 && !r.url().endsWith('/404')) errors.push('http ' + r.status() + ' ' + r.url()); });
async function scrollThrough(page) { await page.evaluate(async () => { const old = document.documentElement.style.scrollBehavior; document.documentElement.style.scrollBehavior = 'auto'; const h = document.body.scrollHeight; for (let y = 0; y < h; y += 400) { window.scrollTo({ top: y, behavior: 'instant' }); await new Promise(r => setTimeout(r, 90)); } window.scrollTo({ top: 0, behavior: 'instant' }); document.documentElement.style.scrollBehavior = old; }); await page.waitForTimeout(500); }
const paths = ['/', '/shop', '/product/brake-pad', '/cart', '/checkout', '/account', '/orders', '/addresses', '/favorites', '/compare', '/my-car', '/faq', '/contact', '/guide', '/article/brake-care', '/about', '/returns', '/warranty', '/privacy', '/login', '/register', '/pages', '/404'];
for (const path of paths) {
  const res = await page.goto('http://127.0.0.1:3000' + path, { waitUntil: 'networkidle' });
  const overflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth + 1);
  console.log(path, res.status(), 'overflow=' + overflow, 'title=' + await page.title());
}
await page.goto('http://127.0.0.1:3000/', { waitUntil: 'networkidle' });
await scrollThrough(page);
await page.screenshot({ path: '/home/user/PartYab/preview/home-desktop.png', fullPage: true });
await page.screenshot({ path: '/home/user/PartYab/preview/home-top.png' });
// add to cart, cart, coupon, checkout, order
await page.locator('[data-add="1"]').first().click();
await page.waitForTimeout(300);
await page.goto('http://127.0.0.1:3000/cart', { waitUntil: 'networkidle' });
console.log('cart count', await page.locator('[data-cart-count]').textContent());
await page.locator('[data-coupon] input').fill('PARTYAB');
await page.locator('[data-coupon] button').click();
await page.goto('http://127.0.0.1:3000/checkout', { waitUntil: 'networkidle' });
await page.fill('#first', 'علی'); await page.fill('#last', 'رضایی'); await page.fill('#phone', '09123456789'); await page.fill('#city', 'تهران'); await page.fill('#address', 'خیابان نمونه، پلاک ۱'); await page.locator('.demo-check input').check();
await page.locator('#place_order').click();
await page.waitForURL('**/order/**');
console.log('order url', page.url(), 'heading', await page.locator('#order-root h1').textContent());
await page.setViewportSize({ width: 1440, height: 1000 });
await page.goto('http://127.0.0.1:3000/product/brake-pad',{waitUntil:'networkidle'}); await page.locator('[data-add="1"]').first().click(); await page.waitForTimeout(300);
for (const [path, name] of [['/shop','shop'],['/product/brake-pad','product'],['/cart','cart'],['/account','account']]) { await page.goto('http://127.0.0.1:3000'+path,{waitUntil:'networkidle'}); await scrollThrough(page); await page.screenshot({path:'/home/user/PartYab/preview/'+name+'.png',fullPage:true}); }
// search
await page.goto('http://127.0.0.1:3000/shop?q=لنت', { waitUntil: 'networkidle' });
console.log('search cards', await page.locator('.pb-product-card').count());
// car selector
await page.goto('http://127.0.0.1:3000/my-car', { waitUntil: 'networkidle' });
await page.selectOption('[data-car-brand]', 'پراید'); await page.selectOption('[data-car-model]', '۱۱۱'); await page.locator('[name=year]').selectOption({ index: 6 });
await page.locator('[data-car-form] button[type=submit]').click();
await page.waitForURL('**/shop?car=**');
console.log('car filter cards', await page.locator('.pb-product-card').count());
// mobile
await page.setViewportSize({ width: 390, height: 844 });
await page.goto('http://127.0.0.1:3000/', { waitUntil: 'networkidle' });
const mOverflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth + 1);
console.log('mobile overflow', mOverflow);
await scrollThrough(page);
await page.screenshot({ path: '/home/user/PartYab/preview/home-mobile.png', fullPage: true });
await page.locator('[data-pb-nav-toggle]').click();
console.log('mobile nav open', await page.locator('.pb-nav').evaluate(el => el.classList.contains('is-open')));
// reduced motion
await page.emulateMedia({ reducedMotion: 'reduce' });
await page.goto('http://127.0.0.1:3000/', { waitUntil: 'networkidle' });
console.log('reduced reveal opacity', await page.locator('[data-reveal]').first().evaluate(el => getComputedStyle(el).opacity));
console.log('ERRORS', JSON.stringify(errors, null, 2));
await browser.close();
