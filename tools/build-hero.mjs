import { build } from 'esbuild';
await build({
  entryPoints: ['theme/part-yab/assets/js/src/hero-3d.js'],
  outfile: 'theme/part-yab/assets/js/hero-3d.js',
  bundle: true, minify: true, format: 'iife', target: ['es2020'], legalComments: 'eof',
});
console.log('hero-3d.js built (self-contained GLB loader, no WASM decoder)');
