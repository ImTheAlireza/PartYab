import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: chromium.args, headless: true });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 } });
await p.route('**/assets/js/hero-3d.js', r => r.fulfill({ contentType: 'text/javascript', body: '' }));
const errors=[]; p.on('pageerror',e=>errors.push(e.message)); p.on('console',m=>{if(m.type()==='error')errors.push(m.text())});
await p.goto('http://127.0.0.1:3000/',{waitUntil:'networkidle'}); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/fix-home.png'});
// loader
await p.evaluate(() => window.PartyabTransition.show()); await p.waitForTimeout(650); await p.screenshot({timeout:60000,path:'preview/fix-loader.png'}); await p.evaluate(() => window.PartyabTransition.hide()); await p.goto('http://127.0.0.1:3000/shop',{waitUntil:'networkidle'});
// checkout
await p.goto('http://127.0.0.1:3000/product/brake-pad',{waitUntil:'domcontentloaded'}); await p.locator('[data-add="1"]').first().click({timeout:60000}); await p.goto('http://127.0.0.1:3000/checkout',{waitUntil:'networkidle'}); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/fix-checkout.png',fullPage:true});
// my-car custom select
await p.goto('http://127.0.0.1:3000/my-car',{waitUntil:'networkidle'}); await p.locator('.pb-select__button').first().click(); await p.waitForTimeout(250); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/fix-select.png'});
console.log('select options', await p.locator('.pb-select__option').count());
await p.locator('.pb-select__option', {hasText:'پراید'}).click(); await p.waitForTimeout(200);
console.log('model options', await p.locator('[data-car-model] option').count());
// year no grouping
await p.goto('http://127.0.0.1:3000/my-car',{waitUntil:'networkidle'}); const yearText = await p.locator('[name=year] option').nth(1).textContent(); console.log('year sample', yearText);
// account
await p.goto('http://127.0.0.1:3000/account',{waitUntil:'networkidle'}); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/fix-account.png'});
// contact
await p.goto('http://127.0.0.1:3000/contact',{waitUntil:'networkidle'}); await p.locator('[name=sku]').focus(); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/fix-contact.png',fullPage:true});
console.log('ERRORS', errors);
await b.close();
