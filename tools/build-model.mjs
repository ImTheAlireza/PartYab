// Reconstruct the uploaded Mustang Shelby GT500 (2.glb) for the web hero.
//
// The source is a rigged export whose rest-pose collapses the wheels onto the armature
// origin (3 wheels pile up at the center, the 4th floats). The body/chassis/interior are
// correct. There is NO skinning — every wheel is a rigid mesh parented to a bone. So we:
//   1. Bake every body mesh's world transform into its geometry (flat, stable hierarchy).
//   2. Take the one correctly-scaled front-left wheel+brake assembly as a template,
//      re-center it on its hub, and instance it at the four fender/hub axle positions.
//   3. Export a clean static GLB, then Draco-compress it.
import { readFileSync, writeFileSync } from 'node:fs';
import { stat } from 'node:fs/promises';
import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { GLTFExporter } from 'three/addons/exporters/GLTFExporter.js';
import { NodeIO } from '@gltf-transform/core';
import { ALL_EXTENSIONS } from '@gltf-transform/extensions';
import { draco, join, simplify, weld } from '@gltf-transform/functions';
import { MeshoptSimplifier } from 'meshoptimizer';
import draco3d from 'draco3dgltf';
await MeshoptSimplifier.ready;

// Minimal FileReader polyfill for GLTFExporter in Node (binary path only; no textures).
class FileReaderPolyfill {
  readAsArrayBuffer(blob) {
    blob.arrayBuffer().then((buf) => { this.result = buf; this.onloadend && this.onloadend(); });
  }
  readAsDataURL(blob) {
    blob.arrayBuffer().then((buf) => {
      this.result = 'data:' + (blob.type || 'application/octet-stream') + ';base64,' + Buffer.from(buf).toString('base64');
      this.onloadend && this.onloadend();
    });
  }
}
globalThis.FileReader = FileReaderPolyfill;

const SRC = process.argv[2] || '2.glb';
const OUT = process.argv[3] || 'theme/part-yab/assets/models/mustang-shelby-gt500.glb';

const buf = readFileSync(SRC);
const gltf = await new Promise((res, rej) => new GLTFLoader().parse(buf.buffer.slice(buf.byteOffset, buf.byteOffset + buf.byteLength), '', res, rej));
const src = gltf.scene;
src.updateMatrixWorld(true);

// Axle positions derived from the fenders (front) and rear hub in the source model.
const WHEEL_Y = 0.3828; // tire bottom flush with the body's lowest point (planted stance)
const FRONT_Z = 4.19, REAR_Z = 0.45, WHEEL_X = 1.21;
const FL_CENTER = new THREE.Vector3(1.2108, 0.4865, 2.4050); // template wheel hub (world)

// Collect meshes, split into body vs. wheel/brake.
const bodyMeshes = [], templateWheel = [];
src.traverse((o) => {
  if (!o.isMesh) return;
  const n = o.name;
  // Skip the studio ground plane / helper geometry — not part of the car.
  if (/^(plane|empty|ground|floor)$/i.test(n)) return;
  const isWheel = /GT500Wheel(Ft|Bk)(L|R)_/.test(n);
  const isBrake = /GT500WheelBrake/.test(n);
  if (isWheel || isBrake) {
    // Only the front-left wheel + its nearby brake are correctly scaled; use as template.
    if (/GT500WheelFtL_/.test(n) || /GT500WheelBrakeBkL_/.test(n)) templateWheel.push(o);
    return;
  }
  bodyMeshes.push(o);
});

const out = new THREE.Scene();
out.name = 'MustangShelbyGT500';

// Bake body meshes into world space.
for (const o of bodyMeshes) {
  const g = o.geometry.clone();
  g.applyMatrix4(o.matrixWorld);
  const m = new THREE.Mesh(g, o.material);
  m.name = o.name;
  out.add(m);
}

// Build a re-centered wheel template (geometry shared across the 4 instances).
const templateParts = templateWheel.map((o) => {
  const g = o.geometry.clone();
  g.applyMatrix4(o.matrixWorld);
  g.translate(-FL_CENTER.x, -FL_CENTER.y, -FL_CENTER.z);
  return { geometry: g, material: o.material, name: o.name.replace('FtL', '').replace('BkL', '') };
});

// Mirror a geometry across X with correct winding (so single-sided materials render it).
function mirrorGeometry(g) {
  const m = g.clone();
  const pos = m.attributes.position;
  for (let i = 0; i < pos.count; i++) pos.setX(i, -pos.getX(i));
  pos.needsUpdate = true;
  if (m.attributes.normal) {
    const n = m.attributes.normal;
    for (let i = 0; i < n.count; i++) n.setX(i, -n.getX(i));
    n.needsUpdate = true;
  }
  if (m.index) {
    const idx = m.index.array;
    for (let i = 0; i < idx.length; i += 3) { const t = idx[i + 1]; idx[i + 1] = idx[i + 2]; idx[i + 2] = t; }
    m.index.needsUpdate = true;
  }
  return m;
}
const mirroredParts = templateParts.map((p) => ({ ...p, geometry: mirrorGeometry(p.geometry) }));

const positions = [
  { x:  WHEEL_X, z: FRONT_Z, mirror: false, tag: 'FtL' },
  { x: -WHEEL_X, z: FRONT_Z, mirror: true,  tag: 'FtR' },
  { x:  WHEEL_X, z: REAR_Z,  mirror: false, tag: 'BkL' },
  { x: -WHEEL_X, z: REAR_Z,  mirror: true,  tag: 'BkR' },
];
for (const pos of positions) {
  const wheel = new THREE.Group();
  wheel.name = 'Wheel_' + pos.tag;
  wheel.position.set(pos.x, WHEEL_Y, pos.z);
  const parts = pos.mirror ? mirroredParts : templateParts;
  for (const part of parts) {
    const m = new THREE.Mesh(part.geometry, part.material);
    m.name = part.name + '_' + pos.tag;
    wheel.add(m);
  }
  out.add(wheel);
}

// Export static GLB.
const exporter = new GLTFExporter();
const glb = await exporter.parseAsync(out, { binary: true });
const tmp = OUT + '.tmp.glb';
writeFileSync(tmp, Buffer.from(glb));

// Draco-compress.
const io = new NodeIO().registerExtensions(ALL_EXTENSIONS).registerDependencies({
  'draco3d.encoder': await draco3d.createEncoderModule(),
  'draco3d.decoder': await draco3d.createDecoderModule(),
});
const doc = await io.read(tmp);
// The hierarchy is now flat + static, so simplification and draw-call joining are safe.
await doc.transform(
  weld(),
  simplify({ simplifier: MeshoptSimplifier, ratio: 0.35, error: 0.001 }),
  join(),
  draco({ method: 'edgebreaker', encodeSpeed: 5, decodeSpeed: 5, quantizePosition: 14, quantizeNormal: 10, quantizeTexcoord: 12, quantizeColor: 8, quantizeGeneric: 12 }),
);
await io.write(OUT, doc);

const before = (await stat(SRC)).size, after = (await stat(OUT)).size;
console.log(JSON.stringify({ source: SRC, output: OUT, before, after, reduction: +(1 - after / before).toFixed(3), bodyMeshes: bodyMeshes.length, wheels: positions.length }, null, 2));
