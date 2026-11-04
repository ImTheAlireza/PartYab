/* PartYab 3D hero. Local GLB + Draco, slow idle turntable, scroll/pointer offset, graceful fallback. */
import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const canvas = document.querySelector('[data-hero-car]');
const sceneEl = canvas?.closest('[data-scene]');
const hero = canvas?.closest('.pb-hero');
if (canvas && sceneEl && hero) init();

function init() {
  const motion = () => window.PartyabMotion;
  const reduced = () => motion()?.reduced() ?? window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({ canvas, alpha: true, antialias: true, preserveDrawingBuffer: new URLSearchParams(location.search).has('hero-poster'), powerPreference: 'high-performance' });
  } catch (e) { return; }
  renderer.setClearColor(0x000000, 0);
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.02;

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(36, 1, 0.1, 60);
  const target = new THREE.Vector3(0, 0.42, 0);
  const pmrem = new THREE.PMREMGenerator(renderer);
  scene.environment = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
  scene.environmentIntensity = 1.05;

  scene.add(new THREE.HemisphereLight(0xdce9ff, 0x16283f, 0.95));
  const key = new THREE.DirectionalLight(0xffffff, 1.7); key.position.set(3.5, 5.5, -2.5); scene.add(key);
  const rim = new THREE.DirectionalLight(0xffb27a, 0.9); rim.position.set(-4.5, 2.2, 3.2); scene.add(rim);

  const car = new THREE.Group();
  scene.add(car);

  const draco = new DRACOLoader();
  draco.setDecoderPath(canvas.dataset.decoder || '/assets/models/draco/');
  draco.setDecoderConfig({ type: 'wasm' });
  const loader = new GLTFLoader();
  loader.setDRACOLoader(draco);

  let ready = false, visible = true, yaw = 1.55, pitch = 0, idle = 0, radius = 2.6, lastT = 0;
  const clamp = (v, a, b) => Math.min(b, Math.max(a, v));

  loader.load(canvas.dataset.model || '/assets/models/mustang-shelby-gt500.glb', (gltf) => {
    const model = gltf.scene;
    // Preserve the model's authored PBR materials; only tune paint/glass for the hero lighting.
    const paintCache = new Map();
    model.traverse((o) => {
      if (!o.isMesh) return;
      const list = Array.isArray(o.material) ? o.material : [o.material];
      const tuned = list.map((m) => {
        if (!m) return m;
        const n = (m.name || '').toLowerCase();
        if (n === 'carpaint' || n === 'carpaint.001') {
          if (!paintCache.has(n)) {
            const stripe = n === 'carpaint.001';
            paintCache.set(n, new THREE.MeshPhysicalMaterial({
              color: m.color ? m.color.clone() : new THREE.Color(0xffffff),
              metalness: stripe ? 0.2 : 0.85,
              roughness: stripe ? 0.3 : 0.28,
              clearcoat: stripe ? 0.4 : 0.9,
              clearcoatRoughness: 0.14,
              envMapIntensity: 1.1,
              side: THREE.FrontSide,
            }));
          }
          return paintCache.get(n);
        }
        if (n.includes('glass')) {
          // Replace costly KHR transmission (extra scene render pass) with cheap alpha blending.
          m.envMapIntensity = 1.1;
          if (m.transmission > 0) { m.transmission = 0; m.transparent = true; m.opacity = 0.55; m.depthWrite = false; }
          return m;
        }
        if (n.includes('tire')) { m.metalness = 0.2; m.roughness = 0.85; m.side = THREE.FrontSide; return m; }
        m.envMapIntensity = 1.0;
        m.side = THREE.FrontSide;
        return m;
      });
      o.material = Array.isArray(o.material) ? tuned : tuned[0];
    });
    car.add(model);
    const box = new THREE.Box3().setFromObject(model);
    const size = box.getSize(new THREE.Vector3());
    const center = box.getCenter(new THREE.Vector3());
    model.position.sub(center); model.position.y += size.y / 2;
    target.y = size.y * 0.42;
    radius = Math.max(size.x, size.z) * 0.58;
    const shadowCanvas = document.createElement('canvas');
    shadowCanvas.width = shadowCanvas.height = 256;
    const ctx = shadowCanvas.getContext('2d');
    const grad = ctx.createRadialGradient(128, 128, 10, 128, 128, 126);
    grad.addColorStop(0, 'rgba(2,8,18,.55)'); grad.addColorStop(.55, 'rgba(2,8,18,.28)'); grad.addColorStop(1, 'rgba(2,8,18,0)');
    ctx.fillStyle = grad; ctx.fillRect(0, 0, 256, 256);
    const shadowTex = new THREE.CanvasTexture(shadowCanvas);
    const shadow = new THREE.Mesh(new THREE.PlaneGeometry(size.x * 2.1, size.z * 1.5), new THREE.MeshBasicMaterial({ map: shadowTex, transparent: true, depthWrite: false, toneMapped: false }));
    shadow.rotation.x = -Math.PI / 2; shadow.position.y = 0.015; shadow.renderOrder = 1;
    scene.add(shadow);
    ready = true;
    sceneEl.classList.add('pb-hero--3d');
    resize(); requestRender();
    window.dispatchEvent(new CustomEvent('partyab:hero-ready'));
  }, undefined, () => { renderer.dispose(); });

  function resize() {
    const w = canvas.clientWidth || 1, h = canvas.clientHeight || 1;
    const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
    renderer.setPixelRatio(dpr); renderer.setSize(w, h, false);
    camera.aspect = w / h;
    const fit = radius / Math.sin(THREE.MathUtils.degToRad(camera.fov / 2));
    const dist = fit * (camera.aspect < 1.35 ? 0.9 / camera.aspect : 0.68);
    camera.position.set(dist * 0.66, dist * 0.28, -dist * 0.72);
    camera.lookAt(target); camera.updateProjectionMatrix();
    requestRender();
  }

  let raf = 0;
  function requestRender() { if (!raf) raf = requestAnimationFrame(frame); }
  function frame(t) {
    raf = 0;
    if (!ready || !visible || document.hidden) return;
    if (!reduced() && t - lastT < 30) { requestRender(); return; }
    const still = reduced();
    const rect = hero.getBoundingClientRect();
    const progress = still ? 0 : clamp(-rect.top / Math.max(1, rect.height * 0.85), 0, 1);
    const px = still ? 0 : parseFloat(sceneEl.style.getPropertyValue('--px')) || 0;
    const py = still ? 0 : parseFloat(sceneEl.style.getPropertyValue('--py')) || 0;
    const realDt = Math.min(0.5, (t - lastT) / 1000 || 0.016);
    const dt = Math.min(0.1, realDt); lastT = t;
    if (!still) idle += realDt * 0.2;
    const targetYaw = 1.55 + idle + progress * 0.35 + px * 0.1;
    const targetPitch = py * 0.05;
    const alpha = still ? 1 : 1 - Math.exp(-dt * 9);
    yaw += (targetYaw - yaw) * alpha;
    pitch += (targetPitch - pitch) * alpha;
    car.rotation.y = yaw; car.rotation.x = pitch;
    car.position.y = 0;
    renderer.render(scene, camera);
    window.PartyabHero3D = { ready: true, yaw, idle, progress, reduced: still };
    if (!still) requestRender();
  }

  canvas.addEventListener('webglcontextlost', (e) => { e.preventDefault(); ready = false; sceneEl.classList.remove('pb-hero--3d'); });
  new ResizeObserver(resize).observe(canvas);
  new IntersectionObserver(([e]) => { visible = e.isIntersecting; if (visible) requestRender(); }, { threshold: 0.05 }).observe(hero);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) requestRender(); });
  window.addEventListener('scroll', requestRender, { passive: true });
  sceneEl.addEventListener('pointermove', requestRender, { passive: true });
  sceneEl.addEventListener('pointerleave', requestRender);
  new MutationObserver(requestRender).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
  resize();
}
