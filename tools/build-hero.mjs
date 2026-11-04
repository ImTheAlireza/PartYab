import { build } from 'esbuild';
import { copyFile, mkdir } from 'node:fs/promises';
await mkdir('theme/part-yab/assets/models/draco', { recursive: true });
for (const f of ['draco_wasm_wrapper.js', 'draco_decoder.wasm']) {
  await copyFile(`node_modules/three/examples/jsm/libs/draco/gltf/${f}`, `theme/part-yab/assets/models/draco/${f}`);
}
await build({
  entryPoints: ['theme/part-yab/assets/js/src/hero-3d.js'],
  outfile: 'theme/part-yab/assets/js/hero-3d.js',
  bundle: true, minify: true, format: 'iife', target: ['es2020'], legalComments: 'eof',
});
console.log('hero-3d.js built');
