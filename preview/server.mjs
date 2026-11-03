import { createServer } from 'node:http';
import { readFile, stat } from 'node:fs/promises';
import { extname, join, normalize } from 'node:path';
import { fileURLToPath } from 'node:url';
import { products } from './catalog.mjs';
import { layout } from './layout.mjs';
import { home } from './pages-home.mjs';
import { shop, product, myCar } from './pages-shop.mjs';
import { cart, checkout, order, login } from './pages-commerce.mjs';
import { account, orders, addresses, favorites, compare } from './pages-account.mjs';
import { faq, contact, guide, article, about, policy, sitemap, notFound } from './pages-content.mjs';

const root = fileURLToPath(new URL('../', import.meta.url));
const port = Number(process.env.PORT || 3000);
const types = { '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8', '.js': 'text/javascript; charset=utf-8', '.mjs': 'text/javascript; charset=utf-8', '.json': 'application/json; charset=utf-8', '.svg': 'image/svg+xml', '.png': 'image/png', '.jpg': 'image/jpeg', '.webp': 'image/webp', '.jpeg': 'image/jpeg', '.woff2': 'font/woff2', '.ico': 'image/x-icon' };
const aliases = { '/index': '/', '/home': '/', '/shop/': '/shop', '/cart/': '/cart', '/checkout/': '/checkout', '/account/': '/account', '/my-account': '/account', '/my-car/': '/my-car', '/faq/': '/faq', '/contact/': '/contact', '/guide/': '/guide', '/about/': '/about' };

function readCar(req) {
  try {
    const cookie = (req.headers.cookie || '').split(';').map(s => s.trim()).find(s => s.startsWith('partyab_demo_car='));
    if (!cookie) return null;
    const car = JSON.parse(decodeURIComponent(cookie.slice('partyab_demo_car='.length)));
    return car && car.brand && car.model && car.year ? car : null;
  } catch { return null; }
}
async function serveFile(res, base, urlPath) {
  const path = normalize(join(base, urlPath));
  if (!path.startsWith(base)) { res.writeHead(403); return res.end('Forbidden'); }
  try {
    const info = await stat(path); if (!info.isFile()) throw new Error('not file');
    const data = await readFile(path);
    res.writeHead(200, { 'Content-Type': types[extname(path)] || 'application/octet-stream', 'Cache-Control': 'no-cache', 'X-Content-Type-Options': 'nosniff' });
    res.end(data);
  } catch { res.writeHead(404); res.end('Not found'); }
}
const server = createServer(async (req, res) => {
  try {
    const url = new URL(req.url, 'http://preview.local');
    let path = decodeURIComponent(url.pathname);
    path = aliases[path] || path;
    if (path.startsWith('/assets/')) return serveFile(res, join(root, 'theme/part-yab/assets'), path.slice('/assets/'.length));
    if (path.startsWith('/demo-images/')) return serveFile(res, join(root, 'data/demo-images'), path.slice('/demo-images/'.length));
    if (path === '/preview/catalog.json') { res.writeHead(200, { 'Content-Type': types['.json'], 'Cache-Control': 'no-cache' }); return res.end(JSON.stringify(products)); }
    if (path.startsWith('/preview/') && /\.(css|js)$/.test(path)) return serveFile(res, join(root, 'preview'), path.slice('/preview/'.length));
    if (path === '/favicon.ico') return serveFile(res, join(root, 'theme/part-yab/assets/img'), 'logo-stacked.svg');
    if (path === '/health') { res.writeHead(200, { 'Content-Type': 'application/json' }); return res.end(JSON.stringify({ ok: true, demo: true })); }
    if (path === '/my-car' && url.searchParams.get('clear') === '1') { res.writeHead(302, { Location: '/my-car', 'Set-Cookie': 'partyab_demo_car=; Path=/; Max-Age=0; SameSite=Lax' }); return res.end(); }
    const car = readCar(req);
    let title = 'پارتیاب', body = '', status = 200;
    if (path === '/') { title = 'قطعه درست برای خودروی شما'; body = home(car); }
    else if (path === '/shop') { title = 'فروشگاه قطعات'; body = shop(url.searchParams, car); }
    else if (path.startsWith('/product/')) { const p = products.find(x => x.slug === path.slice('/product/'.length)); if (p) { title = p.name; body = product(p, car); } else { status = 404; title = 'پیدا نشد'; body = notFound(); } }
    else if (path === '/my-car') { title = 'خودروی من'; body = myCar(car); }
    else if (path === '/cart') { title = 'سبد خرید'; body = cart(); }
    else if (path === '/checkout') { title = 'تسویه حساب'; body = checkout(); }
    else if (path.startsWith('/order/')) { title = 'رسید سفارش'; body = order(path.slice('/order/'.length).replace(/[^A-Za-z0-9-]/g, '')); }
    else if (path === '/account') { title = 'حساب کاربری'; body = account(); }
    else if (path === '/orders') { title = 'سفارش‌ها'; body = orders(); }
    else if (path === '/addresses') { title = 'آدرس‌ها'; body = addresses(); }
    else if (path === '/favorites') { title = 'علاقه‌مندی‌ها'; body = favorites(); }
    else if (path === '/compare') { title = 'مقایسه قطعات'; body = compare(); }
    else if (path === '/login' || path === '/register') { title = path === '/login' ? 'ورود' : 'ثبت‌نام'; body = login(path.slice(1)); }
    else if (path === '/faq') { title = 'سؤال‌های پرتکرار'; body = faq(); }
    else if (path === '/contact') { title = 'تماس با ما'; body = contact(); }
    else if (path === '/guide') { title = 'دانشنامه فنی'; body = guide(); }
    else if (path.startsWith('/article/')) { const a = article(path.slice('/article/'.length)); if (a) { title = 'دانشنامه'; body = a; } else { status = 404; title = 'پیدا نشد'; body = notFound(); } }
    else if (path === '/about') { title = 'درباره پارتیاب'; body = about(); }
    else if (['/returns', '/warranty', '/privacy'].includes(path)) { title = 'خدمات مشتریان'; body = policy(path.slice(1)); }
    else if (path === '/pages') { title = 'همه صفحه‌ها'; body = sitemap(); }
    else { status = 404; title = 'صفحه پیدا نشد'; body = notFound(); }
    res.writeHead(status, { 'Content-Type': types['.html'], 'Cache-Control': 'no-cache', 'X-Content-Type-Options': 'nosniff' });
    res.end(layout({ title, body, path, car }));
  } catch (error) {
    console.error(error);
    res.writeHead(500, { 'Content-Type': 'text/plain; charset=utf-8' }); res.end('Preview error');
  }
});
server.listen(port, '0.0.0.0', () => console.log(`PartYab interactive preview on http://0.0.0.0:${port}`));
