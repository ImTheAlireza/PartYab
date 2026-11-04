import { chromium as pw } from 'playwright';
import chromium, { inflate, setupLambdaEnvironment } from '@sparticuz/chromium';
await inflate('node_modules/@sparticuz/chromium/bin/al2023.tar.br'); setupLambdaEnvironment('/tmp/al2023/lib');
const b = await pw.launch({ executablePath: await chromium.executablePath(), args: chromium.args, headless: true });
const p = await b.newPage({ viewport: { width: 1440, height: 1000 } });
const errors=[]; p.on('pageerror',e=>errors.push(e.message));
async function settle(){ await p.evaluate(async()=>{ for(let y=0;y<document.body.scrollHeight;y+=600){ scrollTo({top:y,behavior:'instant'}); await new Promise(r=>setTimeout(r,120)); } scrollTo({top:0,behavior:'instant'}); document.querySelectorAll('[data-reveal]').forEach(el=>{el.style.opacity='1';el.style.transform='none';}); }); await p.waitForTimeout(500); }
await p.goto('http://127.0.0.1:3000/',{waitUntil:'domcontentloaded'}); await settle(); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/round3-home.png',fullPage:true});
await p.locator('.pb-marquee').scrollIntoViewIfNeeded(); await p.waitForTimeout(300); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/round3-brands.png'});
await p.goto('http://127.0.0.1:3000/product/oil-filter',{waitUntil:'domcontentloaded'}); await p.waitForTimeout(600); await p.screenshot({animations:'disabled',timeout:60000,path:'preview/round3-product.png'});
await p.locator('[data-add="2"]').first().click(); await p.waitForTimeout(700); await p.evaluate(()=>scrollTo(0,0)); await p.waitForTimeout(300); await p.screenshot({timeout:60000,path:'preview/round3-cart-badge.png',clip:{x:0,y:0,width:1440,height:170}});
console.log('ERRORS', errors);
await b.close();
