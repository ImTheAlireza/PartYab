import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: chromium.args, headless: true });
const p = await b.newPage({ viewport: { width: 390, height: 844 } });
for (const path of ['/checkout','/my-car','/account','/cart','/contact','/product/brake-pad']) {
  await p.goto('http://127.0.0.1:3000'+path,{waitUntil:'networkidle'});
  const overflow = await p.evaluate(() => document.documentElement.scrollWidth > window.innerWidth + 1);
  console.log(path, 'overflow=' + overflow);
  if (path === '/my-car') { await p.locator('.pb-select__button').first().click(); await p.waitForTimeout(200); await p.screenshot({path:'preview/fix-select-mobile.png'}); }
  if (path === '/checkout') await p.screenshot({path:'preview/fix-checkout-mobile.png',fullPage:true});
}
await b.close();
