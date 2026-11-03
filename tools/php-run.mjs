/**
 * اجرای فایل PHP با php-wasm (بدون باینری php).
 * استفاده: node tools/php-run.mjs <script.php> [args...]
 */
import { PHP } from '@php-wasm/universal';
import { loadNodeRuntime } from '@php-wasm/node';
import fs from 'node:fs';
import path from 'node:path';

const REPO = path.resolve(import.meta.dirname, '..');
const script = process.argv[2];
const args = process.argv.slice(3);

const php = new PHP(await loadNodeRuntime('8.3', { emscriptenOptions: { processId: 1 } }));

function copyTree(dir, base = REPO) {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    if (['node_modules', '.git', 'releases', 'vendor'].includes(entry.name)) continue;
    const full = path.join(dir, entry.name);
    const rel = path.relative(base, full);
    if (entry.isDirectory()) {
      php.mkdirTree('/app/' + rel);
      copyTree(full, base);
    } else if (entry.name.endsWith('.php') || entry.name.endsWith('.csv')) {
      php.writeFile('/app/' + rel, fs.readFileSync(full, 'utf8'));
    }
  }
}

php.mkdirTree('/app');
php.mkdirTree('/app/plugins/partyab-quickstart/vendor');
copyTree(path.join(REPO, 'theme'));
copyTree(path.join(REPO, 'plugins'));
php.writeFile('/app/plugins/partyab-quickstart/vendor/compat-sample.csv', fs.readFileSync(path.join(REPO, 'data/compat-sample.csv'), 'utf8'));
php.writeFile('/run.php', fs.readFileSync(path.join(REPO, script), 'utf8'));

const argv = [script, ...args].map((a) => JSON.stringify(a)).join(',');
let r;
try {
  r = await php.run({ code: `<?php $argv = [${argv}]; $argc = count($argv); chdir('/app'); require '/run.php';` });
  console.log(r.text || '');
  if (r.errors) console.log('ERRORS: ' + String(r.errors).slice(0, 2000));
  process.exit(r.exitCode ?? 0);
} catch (e) {
  const out = e.stdout ?? e.text ?? e.message ?? '';
  console.log(Buffer.isBuffer(out) ? out.toString('utf8') : String(out));
  if (e.stderr) console.log('STDERR: ' + String(e.stderr).slice(0, 1500));
  process.exit(0);
}
