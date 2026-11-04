/* PartYab transition sound. Tiny synthesized cue, no audio file download. */
(function () {
  'use strict';
  var config = window.partyabSoundData || {};
  var siteEnabled = config.enabled !== false && config.enabled !== '0' && config.enabled !== 0;
  var muted = false;
  try { muted = localStorage.getItem('partyab-sound') === 'muted'; } catch (e) {}
  var ctx = null, master = null, plays = 0, lastPlay = 0;

  function enabled() { return siteEnabled && !muted; }
  function ensureContext() {
    if (!enabled()) return null;
    var AC = window.AudioContext || window.webkitAudioContext;
    if (!AC) return null;
    if (!ctx) {
      ctx = new AC();
      master = ctx.createGain();
      master.gain.value = 0.16;
      master.connect(ctx.destination);
    }
    if (ctx.state === 'suspended') ctx.resume().catch(function () {});
    return ctx;
  }
  function unlock() { ensureContext(); }
  function tone(freq, start, duration, peak, type) {
    var osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.type = type || 'sine';
    osc.frequency.setValueAtTime(freq, start);
    gain.gain.setValueAtTime(0.0001, start);
    gain.gain.exponentialRampToValueAtTime(peak, start + 0.018);
    gain.gain.exponentialRampToValueAtTime(0.0001, start + duration);
    osc.connect(gain); gain.connect(master);
    osc.start(start); osc.stop(start + duration + 0.02);
  }
  function schedule(c) {
    var t = c.currentTime + 0.005;
    tone(523.25, t, 0.16, 0.22, 'sine');
    tone(783.99, t + 0.07, 0.2, 0.16, 'sine');
  }
  function play() {
    if (!enabled()) return false;
    var now = performance.now();
    if (now - lastPlay < 450) return false;
    var c = ensureContext();
    if (!c) return false;
    lastPlay = now; plays++;
    if (c.state === 'running') schedule(c);
    else c.resume().then(function () { if (enabled() && ctx && ctx.state === 'running') schedule(ctx); }).catch(function () {});
    return true;
  }
  function syncButtons() {
    document.querySelectorAll('[data-sound-toggle]').forEach(function (button) {
      button.hidden = !siteEnabled;
      button.setAttribute('aria-pressed', String(!muted));
      var label = button.querySelector('span');
      if (label) label.textContent = muted ? 'روشن کردن صدا' : 'خاموش کردن صدا';
    });
  }
  function setMuted(value) {
    muted = !!value;
    try { localStorage.setItem('partyab-sound', muted ? 'muted' : 'on'); } catch (e) {}
    if (muted && ctx) ctx.suspend().catch(function () {});
    syncButtons();
  }
  document.addEventListener('pointerdown', unlock, { once: true, passive: true });
  document.addEventListener('keydown', unlock, { once: true });
  document.addEventListener('click', function (event) {
    var button = event.target.closest('[data-sound-toggle]');
    if (!button) return;
    setMuted(!muted);
    if (!muted) { unlock(); play(); }
  });
  syncButtons();
  window.PartyabSound = { enabled: enabled, play: play, unlock: unlock, setMuted: setMuted, get plays() { return plays; }, get muted() { return muted; } };
})();
