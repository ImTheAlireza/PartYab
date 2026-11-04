// Build an original lightweight brick/technic-style hero car for PartYab.
// No external model, no textures, no Draco/WASM, no quantization extensions.
// Goal: render reliably on low-end GPUs and look playful rather than photoreal.
import { writeFileSync, unlinkSync } from 'node:fs';
import { stat } from 'node:fs/promises';
import * as THREE from 'three';
import { GLTFExporter } from 'three/addons/exporters/GLTFExporter.js';
import { mergeGeometries } from 'three/addons/utils/BufferGeometryUtils.js';
import { NodeIO } from '@gltf-transform/core';
import { ALL_EXTENSIONS } from '@gltf-transform/extensions';
import { prune } from '@gltf-transform/functions';

class FileReaderPolyfill {
  readAsArrayBuffer(blob) { blob.arrayBuffer().then(buf => { this.result = buf; this.onloadend?.(); }); }
  readAsDataURL(blob) { blob.arrayBuffer().then(buf => { this.result = 'data:' + (blob.type || 'application/octet-stream') + ';base64,' + Buffer.from(buf).toString('base64'); this.onloadend?.(); }); }
}
globalThis.FileReader = FileReaderPolyfill;

const OUT = process.argv[2] || 'theme/part-yab/assets/models/brick-car.glb';

const materials = {
  paint: new THREE.MeshStandardMaterial({ name: 'brick-paint', color: 0xff6a00, roughness: 0.35, metalness: 0.05 }),
  navy: new THREE.MeshStandardMaterial({ name: 'brick-navy', color: 0x0e2340, roughness: 0.5, metalness: 0.05 }),
  white: new THREE.MeshStandardMaterial({ name: 'brick-white', color: 0xf8fafc, roughness: 0.4, metalness: 0.0 }),
  black: new THREE.MeshStandardMaterial({ name: 'brick-black', color: 0x0b1220, roughness: 0.7, metalness: 0.0 }),
  tire: new THREE.MeshStandardMaterial({ name: 'brick-tire', color: 0x111827, roughness: 0.9, metalness: 0.0 }),
  rim: new THREE.MeshStandardMaterial({ name: 'brick-rim', color: 0xb6c2d1, roughness: 0.3, metalness: 0.55 }),
  glass: new THREE.MeshStandardMaterial({ name: 'brick-glass', color: 0x9bd7ff, roughness: 0.1, metalness: 0.0, transparent: true, opacity: 0.45, depthWrite: false, side: THREE.DoubleSide }),
  light: new THREE.MeshStandardMaterial({ name: 'brick-light', color: 0xfff7d6, roughness: 0.2, metalness: 0.0, emissive: 0xffe9a3, emissiveIntensity: 1 }),
  tail: new THREE.MeshStandardMaterial({ name: 'brick-tail', color: 0xef4444, roughness: 0.3, metalness: 0.0, emissive: 0xef4444, emissiveIntensity: 1 }),
};

const buckets = new Map();
function add(geo, mat, x = 0, y = 0, z = 0, rx = 0, ry = 0, rz = 0) {
  const m = new THREE.Matrix4().compose(
    new THREE.Vector3(x, y, z),
    new THREE.Quaternion().setFromEuler(new THREE.Euler(rx, ry, rz)),
    new THREE.Vector3(1, 1, 1),
  );
  geo.applyMatrix4(m);
  if (!buckets.has(mat)) buckets.set(mat, []);
  buckets.get(mat).push(geo);
}
function box(w, h, d, x, y, z, mat, rx = 0, ry = 0, rz = 0) {
  add(new THREE.BoxGeometry(w, h, d), mat, x, y, z, rx, ry, rz);
}
function cyl(rt, rb, h, x, y, z, mat, rx = 0, ry = 0, rz = 0, seg = 16) {
  add(new THREE.CylinderGeometry(rt, rb, h, seg), mat, x, y, z, rx, ry, rz);
}
function stud(x, y, z, mat = materials.paint) { cyl(0.09, 0.09, 0.07, x, y, z, mat, 0, 0, 0, 14); }

// Chassis + lower body. Front is +Z.
box(1.9, 0.22, 4.3, 0, 0.42, 0, materials.navy);
box(1.95, 0.34, 4.15, 0, 0.68, 0, materials.paint);
box(1.98, 0.16, 0.28, 0, 0.55, 2.12, materials.navy);
box(1.98, 0.16, 0.28, 0, 0.55, -2.12, materials.navy);
box(0.14, 0.18, 2.3, 0.98, 0.48, 0, materials.navy);
box(0.14, 0.18, 2.3, -0.98, 0.48, 0, materials.navy);

// Hood, trunk, cabin.
box(1.72, 0.22, 1.25, 0, 0.95, 1.35, materials.paint);
box(1.72, 0.22, 0.9, 0, 0.95, -1.5, materials.paint);
// Dark interior makes the transparent blue glass read correctly.
box(1.5, 0.42, 1.5, 0, 1.08, -0.2, materials.black);
for (const x of [0.78, -0.78]) for (const z of [0.55, -0.95]) box(0.1, 0.46, 0.1, x, 1.12, z, materials.paint);
box(1.56, 0.14, 1.5, 0, 1.36, -0.2, materials.paint);
box(0.18, 0.1, 0.12, 0.95, 1.15, 0.55, materials.paint);
box(0.18, 0.1, 0.12, -0.95, 1.15, 0.55, materials.paint);

// Glasshouse.
box(1.44, 0.42, 0.07, 0, 1.15, 0.62, materials.glass, -0.32);
box(1.44, 0.42, 0.07, 0, 1.15, -1.02, materials.glass, 0.32);
box(0.06, 0.38, 1.28, 0.82, 1.12, -0.2, materials.glass);
box(0.06, 0.38, 1.28, -0.82, 1.12, -0.2, materials.glass);

// Racing stripes.
box(0.24, 0.03, 1.2, 0.28, 1.075, 1.35, materials.white);
box(0.24, 0.03, 1.2, -0.28, 1.075, 1.35, materials.white);
box(0.24, 0.03, 1.44, 0.28, 1.445, -0.2, materials.white);
box(0.24, 0.03, 1.44, -0.28, 1.445, -0.2, materials.white);
box(0.24, 0.03, 0.86, 0.28, 1.075, -1.5, materials.white);
box(0.24, 0.03, 0.86, -0.28, 1.075, -1.5, materials.white);

// Studs: hood, roof, trunk.
for (const x of [-0.62, -0.21, 0.21, 0.62]) for (const z of [1.0, 1.35, 1.7]) stud(x, 1.1, z);
for (const x of [-0.52, 0.52]) for (const z of [-0.65, -0.2, 0.25]) stud(x, 1.47, z);
for (const x of [-0.62, -0.21, 0.21, 0.62]) for (const z of [-1.75, -1.35]) stud(x, 1.1, z);

// Front fascia, lights, grille.
box(1.5, 0.26, 0.18, 0, 0.82, 2.05, materials.paint);
box(0.4, 0.14, 0.08, 0.62, 0.86, 2.16, materials.light);
box(0.4, 0.14, 0.08, -0.62, 0.86, 2.16, materials.light);
box(0.9, 0.16, 0.06, 0, 0.68, 2.18, materials.black);
box(0.42, 0.12, 0.08, 0.62, 0.82, -2.16, materials.tail);
box(0.42, 0.12, 0.08, -0.62, 0.82, -2.16, materials.tail);

// Spoiler + exhaust.
box(1.8, 0.08, 0.34, 0, 1.28, -2.0, materials.navy);
box(0.08, 0.26, 0.08, 0.6, 1.12, -2.0, materials.navy);
box(0.08, 0.26, 0.08, -0.6, 1.12, -2.0, materials.navy);
cyl(0.07, 0.07, 0.16, 0.45, 0.48, -2.2, materials.rim, Math.PI / 2);
cyl(0.07, 0.07, 0.16, -0.45, 0.48, -2.2, materials.rim, Math.PI / 2);

// Wheels + arches.
for (const sx of [1, -1]) for (const sz of [1, -1]) {
  const x = sx * 1.02, z = sz * 1.45;
  cyl(0.42, 0.42, 0.3, x, 0.42, z, materials.tire, 0, 0, Math.PI / 2, 20);
  cyl(0.24, 0.24, 0.32, x, 0.42, z, materials.rim, 0, 0, Math.PI / 2, 16);
  cyl(0.09, 0.09, 0.34, x, 0.42, z, materials.paint, 0, 0, Math.PI / 2, 12);
  box(0.26, 0.22, 0.95, sx * 0.98, 0.82, z, materials.paint);
}

const scene = new THREE.Scene();
scene.name = 'PartYabBrickCar';
let triangles = 0;
for (const [mat, geos] of buckets) {
  const merged = mergeGeometries(geos, false);
  const mesh = new THREE.Mesh(merged, mat);
  mesh.name = mat.name;
  scene.add(mesh);
  triangles += merged.index ? merged.index.count / 3 : merged.attributes.position.count / 3;
  geos.forEach(g => g.dispose());
}

const tmp = OUT + '.tmp.glb';
const glb = await new GLTFExporter().parseAsync(scene, { binary: true });
writeFileSync(tmp, Buffer.from(glb));
const io = new NodeIO().registerExtensions(ALL_EXTENSIONS);
const doc = await io.read(tmp);
await doc.transform(prune());
await io.write(OUT, doc);
unlinkSync(tmp);
const after = (await stat(OUT)).size;
console.log(JSON.stringify({ output: OUT, bytes: after, triangles: Math.round(triangles), drawCalls: buckets.size, runtimeDecoder: false, extensions: doc.getRoot().listExtensionsUsed().map(e => e.extensionName) }, null, 2));
