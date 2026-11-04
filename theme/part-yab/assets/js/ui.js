/* Shared UI helpers and progressively enhanced page controls. */
(function () {
  'use strict';
  function icon(name) {
    return '<svg class="pb-icon" aria-hidden="true"><use href="#i-' + name + '"></use></svg>';
  }
  function toast(message) {
    var stack = document.querySelector('.pb-toast-stack');
    if (!stack) return;
    var item = document.createElement('div');
    item.className = 'pb-toast';
    item.innerHTML = icon('check');
    var text = document.createElement('span'); text.textContent = message; item.appendChild(text);
    stack.appendChild(item);
    setTimeout(function () { item.remove(); }, 4200);
  }
  function fly(from) {
    if (!from || !from.animate || (window.PartyabMotion && window.PartyabMotion.reduced())) return;
    var target = document.querySelector('.pb-action--cart');
    if (!target) return;
    var a = from.getBoundingClientRect(), b = target.getBoundingClientRect();
    var dot = document.createElement('span'); dot.className = 'pb-fly-dot'; dot.innerHTML = icon('cart');
    dot.style.left = (a.left + a.width / 2 - 16) + 'px'; dot.style.top = (a.top + a.height / 2 - 16) + 'px';
    document.body.appendChild(dot);
    var dx = b.left + b.width / 2 - a.left - a.width / 2, dy = b.top + b.height / 2 - a.top - a.height / 2;
    var animation = dot.animate([{ transform: 'translate(0,0) scale(1)', opacity: 1 }, { transform: 'translate(' + dx * .5 + 'px,' + (dy * .5 - 70) + 'px) scale(.8)', opacity: 1, offset: .6 }, { transform: 'translate(' + dx + 'px,' + dy + 'px) scale(.25)', opacity: 0 }], { duration: 700, easing: 'cubic-bezier(.38,0,.55,1)' });
    animation.onfinish = animation.oncancel = function () { dot.remove(); };
  }
  function bumpCart() {
    var count = document.querySelector('.pb-action__count');
    if (!count) return;
    count.classList.remove('bump'); void count.offsetWidth; count.classList.add('bump');
  }
  window.PartyabUI = { icon: icon, toast: toast, fly: fly, bumpCart: bumpCart };
  document.addEventListener('click', function (event) {
    var add = event.target.closest('.single_add_to_cart_button, .ajax_add_to_cart');
    if (add && !add.disabled) fly(add);
    var filter = event.target.closest('[data-filter-toggle]');
    if (filter) {
      var side = document.querySelector('.pb-shop__side');
      if (side) { var open = side.classList.toggle('is-open'); filter.setAttribute('aria-expanded', String(open)); }
    }
    var top = event.target.closest('[data-pb-top], .ft-top-btn');
    if (top) window.scrollTo({ top: 0, behavior: window.PartyabMotion && window.PartyabMotion.reduced() ? 'auto' : 'smooth' });
  });
  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    var nav = document.querySelector('.pb-nav.is-open'), toggle = document.querySelector('[data-pb-nav-toggle]');
    if (nav && toggle) { nav.classList.remove('is-open'); toggle.setAttribute('aria-expanded', 'false'); toggle.focus(); }
  });
  if (window.jQuery) window.jQuery(document.body).on('added_to_cart', bumpCart);

  document.querySelectorAll('[data-tabs]').forEach(function (tabs) {
    var buttons = tabs.querySelectorAll('[role="tab"]');
    function select(button) {
      buttons.forEach(function (item) {
        var active = item === button;
        item.setAttribute('aria-selected', String(active)); item.tabIndex = active ? 0 : -1;
        var panel = document.getElementById(item.getAttribute('aria-controls'));
        if (panel) panel.hidden = !active;
      });
    }
    buttons.forEach(function (button, index) {
      button.addEventListener('click', function () { select(button); });
      button.addEventListener('keydown', function (event) {
        var next = event.key === 'ArrowLeft' ? index + 1 : event.key === 'ArrowRight' ? index - 1 : event.key === 'Home' ? 0 : event.key === 'End' ? buttons.length - 1 : -1;
        if (next < 0) return;
        event.preventDefault(); next = (next + buttons.length) % buttons.length; select(buttons[next]); buttons[next].focus();
      });
    });
  });

  var faqSearch = document.querySelector('[data-faq-search]');
  if (faqSearch) faqSearch.addEventListener('input', function () {
    var query = faqSearch.value.trim();
    var count = 0;
    document.querySelectorAll('.pb-faq__item').forEach(function (item) { item.hidden = !item.textContent.includes(query); if (!item.hidden) count++; });
    var empty = document.querySelector('[data-faq-empty]'); if (empty) empty.hidden = count > 0;
  });
})();
