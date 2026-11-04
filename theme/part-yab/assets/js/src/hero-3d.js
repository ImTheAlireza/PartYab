/* PartYab 3D hero. Self-contained GLB (no WASM/worker dependency), slow turntable,
 * scroll/pointer offset, and a poster that stays visible until the first real frame. */
import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const canvas = document.querySelector('[data-hero-car]');
const sceneEl = canvas?.closest('[data-scene]');
const hero = canvas?.closest('.pb-hero');
if (canvas && sceneEl && hero) init();

function init() {
  const motion = () => window.PartyabMotion;
  const reduced = () => motion()?.reduced() ?? window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const state = { ready: false, status: 'loading', error: null, yaw: 1.55, idle: 0, progress: 0, reduced: reduced() };
  window.PartyabHero3D = state;
  sceneEl.dataset.heroState = 'loading';

  const events = new AbortController();
  const modelRequest = new AbortController();
  const observers = [];
  const lowPower = window.matchMedia('(pointer: coarse)').matches
    || (navigator.deviceMemory > 0 && navigator.deviceMemory <= 4)
    || (navigator.hardwareConcurrency > 0 && navigator.hardwareConcurrency <= 4);
  let renderer, scene = null, environmentTarget = null, modelRoot = null;
  let disposed = false, ready = false, visible = true, raf = 0;
  let yaw = 1.55, pitch = 0, idle = 0, radius = 2.6, lastT = 0;
  const replacedMaterials = new Set();
  const clamp = (v, a, b) => Math.min(b, Math.max(a, v));

  try {
    const options = { alpha: true, antialias: !lowPower, preserveDrawingBuffer: new URLSearchParams(location.search).has('hero-poster'), powerPreference: 'high-performance' };
    const context = canvas.getContext('webgl2', options);
    if (!context) { fallback('webgl2-unavailable'); return; }
    renderer = new THREE.WebGLRenderer({ canvas, context, ...options });
  } catch (e) { fallback('renderer-unavailable'); return; }
  renderer.setClearColor(0x000000, 0);
  renderer.outputColorSpace = THREE.SRGBColorSpace;
  renderer.toneMapping = THREE.ACESFilmicToneMapping;
  renderer.toneMappingExposure = 1.02;

  scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(36, 1, 0.1, 60);
  const target = new THREE.Vector3(0, 0.42, 0);
  const pmrem = new THREE.PMREMGenerator(renderer);
  const room = new RoomEnvironment();
  environmentTarget = pmrem.fromScene(room, 0.04);
  scene.environment = environmentTarget.texture;
  scene.environmentIntensity = 1.05;
  room.dispose(); pmrem.dispose();

  scene.add(new THREE.HemisphereLight(0xdce9ff, 0x16283f, 0.95));
  const key = new THREE.DirectionalLight(0xffffff, 1.7); key.position.set(3.5, 5.5, -2.5); scene.add(key);
  const rim = new THREE.DirectionalLight(0xffb27a, 0.9); rim.position.set(-4.5, 2.2, 3.2); scene.add(rim);
  const car = new THREE.Group(); scene.add(car);

  function fallback(reason) {
    if (disposed) return;
    ready = false;
    state.ready = false; state.status = 'fallback'; state.error = reason;
    sceneEl.dataset.heroState = 'fallback';
    sceneEl.classList.remove('pb-hero--3d');
    sceneEl.classList.add('pb-hero--fallback');
    cancelAnimationFrame(raf); raf = 0;
    modelRequest.abort(); events.abort();
    observers.forEach(o => o.disconnect());
    disposeScene();
    environmentTarget?.dispose();
    replacedMaterials.forEach(m => m.dispose());
    renderer?.dispose();
    disposed = true;
  }

  function disposeScene() {
    if (!scene) return;
    const geometries = new Set(), materials = new Set(), textures = new Set();
    scene.traverse(o => {
      if (o.geometry) geometries.add(o.geometry);
      const list = Array.isArray(o.material) ? o.material : o.material ? [o.material] : [];
      list.forEach(m => { materials.add(m); Object.values(m).forEach(v => { if (v?.isTexture) textures.add(v); }); });
    });
    geometries.forEach(g => g.dispose()); materials.forEach(m => m.dispose()); textures.forEach(t => t.dispose());
  }

  function tuneMaterials(model) {
    const paintCache = new Map();
    model.traverse(o => {
      if (!o.isMesh) return;
      const list = Array.isArray(o.material) ? o.material : [o.material];
      const tuned = list.map(m => {
        if (!m) return m;
        const n = (m.name || '').toLowerCase();
        if (n === 'carpaint' || n === 'carpaint.001') {
          if (!paintCache.has(n)) {
            const stripe = n === 'carpaint.001';
            const base = { color: m.color ? m.color.clone() : new THREE.Color(0xffffff), metalness: stripe ? 0.2 : 0.85, roughness: stripe ? 0.3 : 0.28, envMapIntensity: 1.1, side: THREE.FrontSide };
            // Clearcoat is a second specular lobe; skip it on phones / low-core devices.
            paintCache.set(n, lowPower ? new THREE.MeshStandardMaterial(base) : new THREE.MeshPhysicalMaterial({ ...base, clearcoat: stripe ? 0.4 : 0.9, clearcoatRoughness: 0.14 }));
            replacedMaterials.add(m);
          }
          return paintCache.get(n);
        }
        if (n.includes('glass')) {
          m.envMapIntensity = 1.1; m.transmission = 0; m.transparent = true; m.opacity = 0.55; m.depthWrite = false; m.side = THREE.DoubleSide; m.needsUpdate = true;
          return m;
        }
        if (n.includes('tire')) { m.metalness = 0.2; m.roughness = 0.85; m.side = THREE.FrontSide; return m; }
        m.envMapIntensity = 1.0;
        m.side = /leather|seat|lambert|roll/.test(n) ? THREE.DoubleSide : THREE.FrontSide;
        return m;
      });
      o.material = Array.isArray(o.material) ? tuned : tuned[0];
    });
  }

  async function load() {
    const slowTimer = setTimeout(() => { if (!ready && !disposed) { state.status = 'slow'; sceneEl.dataset.heroState = 'slow'; } }, 12000);
    try {
      const url = new URL(canvas.dataset.model || './assets/models/mustang-shelby-gt500.glb', document.baseURI).href;
      const response = await fetch(url, { signal: modelRequest.signal, credentials: 'same-origin' });
      if (!response.ok) throw new Error('model-http-' + response.status);
      const gltf = await new GLTFLoader().parseAsync(await response.arrayBuffer(), new URL('.', url).href);
      if (disposed) return;
      const model = gltf.scene;
      tuneMaterials(model);
      const box = new THREE.Box3().setFromObject(model);
      const size = box.getSize(new THREE.Vector3());
      if (!Number.isFinite(size.length()) || size.length() === 0 || Math.max(size.x, size.y, size.z) > 20) throw new Error('invalid-model-bounds');
      const center = box.getCenter(new THREE.Vector3());
      model.position.sub(center); model.position.y += size.y / 2;
      target.y = size.y * 0.42;
      radius = Math.max(size.x, size.z) * 0.58;
      modelRoot = model; car.add(model);

      const shadowCanvas = document.createElement('canvas'); shadowCanvas.width = shadowCanvas.height = 256;
      const ctx = shadowCanvas.getContext('2d');
      const grad = ctx.createRadialGradient(128, 128, 10, 128, 128, 126);
      grad.addColorStop(0, 'rgba(2,8,18,.55)'); grad.addColorStop(.55, 'rgba(2,8,18,.28)'); grad.addColorStop(1, 'rgba(2,8,18,0)');
      ctx.fillStyle = grad; ctx.fillRect(0, 0, 256, 256);
      const shadow = new THREE.Mesh(new THREE.PlaneGeometry(size.x * 2.1, size.z * 1.5), new THREE.MeshBasicMaterial({ map: new THREE.CanvasTexture(shadowCanvas), transparent: true, depthWrite: false, toneMapped: false }));
      shadow.rotation.x = -Math.PI / 2; shadow.position.y = 0.015; shadow.renderOrder = 1; scene.add(shadow);

      resize();
      car.rotation.y = yaw;
      // Draw BEFORE hiding the poster. On a slow GPU this is the difference between
      // "the car is loading" and an empty hero rectangle.
      renderer.render(scene, camera);
      ready = true; lastT = performance.now();
      Object.assign(state, { ready: true, status: 'ready', error: null, yaw, idle, progress: 0, reduced: reduced() });
      sceneEl.dataset.heroState = 'ready';
      sceneEl.classList.remove('pb-hero--fallback');
      sceneEl.classList.add('pb-hero--3d');
      window.dispatchEvent(new CustomEvent('partyab:hero-ready'));
      requestRender();
    } catch (e) {
      if (!disposed && e.name !== 'AbortError') fallback(String(e.message || e).slice(0, 160));
    } finally { clearTimeout(slowTimer); }
  }

  function resize() {
    const w = canvas.clientWidth || 1, h = canvas.clientHeight || 1;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, lowPower ? 1 : 1.5));
    renderer.setSize(w, h, false);
    camera.aspect = w / h;
    const fit = radius / Math.sin(THREE.MathUtils.degToRad(camera.fov / 2));
    const dist = fit * (camera.aspect < 1.35 ? 0.9 / camera.aspect : 0.68);
    camera.position.set(dist * 0.66, dist * 0.28, -dist * 0.72);
    camera.lookAt(target); camera.updateProjectionMatrix();
    requestRender();
  }

  function requestRender() { if (!raf && !disposed) raf = requestAnimationFrame(frame); }
  function frame(t) {
    raf = 0;
    if (!ready || disposed || !visible || document.hidden) return;
    const still = reduced();
    if (!still && t - lastT < (lowPower ? 40 : 30)) { requestRender(); return; }
    const rect = hero.getBoundingClientRect();
    const progress = still ? 0 : clamp(-rect.top / Math.max(1, rect.height * 0.85), 0, 1);
    const px = still ? 0 : parseFloat(sceneEl.style.getPropertyValue('--px')) || 0;
    const py = still ? 0 : parseFloat(sceneEl.style.getPropertyValue('--py')) || 0;
    const realDt = Math.min(0.25, (t - lastT) / 1000 || 0.016);
    const dt = Math.min(0.1, realDt); lastT = t;
    if (!still) idle += realDt * 0.2;
    const alpha = still ? 1 : 1 - Math.exp(-dt * 9);
    yaw += (1.55 + idle + progress * 0.35 + px * 0.1 - yaw) * alpha;
    pitch += (py * 0.05 - pitch) * alpha;
    car.rotation.y = yaw; car.rotation.x = pitch; car.position.y = 0;
    try { renderer.render(scene, camera); } catch (e) { fallback('render-failed'); return; }
    Object.assign(state, { yaw, idle, progress, reduced: still });
    if (!still) requestRender();
  }

  canvas.addEventListener('webglcontextlost', e => {
    e.preventDefault(); ready = false; state.ready = false; state.status = 'context-lost';
    sceneEl.dataset.heroState = 'context-lost'; sceneEl.classList.remove('pb-hero--3d');
    cancelAnimationFrame(raf); raf = 0;
  }, { signal: events.signal });
  canvas.addEventListener('webglcontextrestored', () => {
    try {
      resize(); renderer.render(scene, camera);
      ready = true; lastT = performance.now(); state.ready = true; state.status = 'ready';
      sceneEl.dataset.heroState = 'ready'; sceneEl.classList.add('pb-hero--3d'); requestRender();
    } catch (e) { fallback('context-restore-failed'); }
  }, { signal: events.signal });
  const resizeObserver = new ResizeObserver(resize); resizeObserver.observe(canvas); observers.push(resizeObserver);
  const intersectionObserver = new IntersectionObserver(([e]) => { visible = e.isIntersecting; if (visible) { lastT = performance.now(); requestRender(); } }, { threshold: 0.05 });
  intersectionObserver.observe(hero); observers.push(intersectionObserver);
  const mutationObserver = new MutationObserver(requestRender); mutationObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] }); observers.push(mutationObserver);
  document.addEventListener('visibilitychange', () => { if (!document.hidden) { lastT = performance.now(); requestRender(); } }, { signal: events.signal });
  window.addEventListener('scroll', requestRender, { passive: true, signal: events.signal });
  window.addEventListener('pageshow', () => { lastT = performance.now(); requestRender(); }, { signal: events.signal });
  window.addEventListener('pagehide', () => { cancelAnimationFrame(raf); raf = 0; }, { signal: events.signal });
  sceneEl.addEventListener('pointermove', requestRender, { passive: true, signal: events.signal });
  sceneEl.addEventListener('pointerleave', requestRender, { signal: events.signal });
  resize();
  load();
}
