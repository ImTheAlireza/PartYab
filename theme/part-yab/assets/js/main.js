/**
 * PartYab — اسکریپت پایه (بدون jQuery، بدون وابستگی)
 * قاعده: هر قابلیت جدید یک ماژول مستقل با کلیدواژه data-pb است.
 */
(function () {
	'use strict';

	var doc = document;

	function ready(fn) {
		if (doc.readyState !== 'loading') {
			fn();
		} else {
			doc.addEventListener('DOMContentLoaded', fn);
		}
	}

	/* ۱. افزایش/کاهش تعداد در صفحه محصول */
	function initQuantity() {
		doc.querySelectorAll('.quantity').forEach(function (wrap) {
			if (wrap.querySelector('[data-pb-qty]') || !wrap.querySelector('input[type="number"]')) {
				return;
			}
			var input = wrap.querySelector('input[type="number"]');

			['minus', 'plus'].forEach(function (kind) {
				var btn = doc.createElement('button');
				btn.type = 'button';
				btn.dataset.pbQty = kind;
				btn.className = 'pb-qty-btn pb-qty-btn--' + kind;
				btn.setAttribute('aria-label', kind === 'plus' ? 'افزایش تعداد' : 'کاهش تعداد');
				btn.textContent = kind === 'plus' ? '+' : '−';

				btn.addEventListener('click', function () {
					var step = parseInt(input.step, 10) || 1;
					var min = parseInt(input.min, 10) || 1;
					var max = parseInt(input.max, 10) || Infinity;
					var next = (parseInt(input.value, 10) || min) + (kind === 'plus' ? step : -step);

					input.value = Math.min(max, Math.max(min, next));
					input.dispatchEvent(new Event('change', { bubbles: true }));
				});

				if (kind === 'minus') {
					wrap.insertBefore(btn, input);
				} else {
					wrap.appendChild(btn);
				}
			});
		});
	}

	/* ۲. بازگشت به بالا (فقط اگر صفحه بلند باشد) */
	function initBackToTop() {
		var btn = doc.querySelector('[data-pb-top]');
		if (!btn) {
			return;
		}

		var toggle = function () {
			btn.classList.toggle('is-visible', window.scrollY > 600);
		};


		window.addEventListener('scroll', toggle, { passive: true });
		toggle();
	}

	/* ۳. باز و بسته کردن منوی موبایل */
	function initNavToggle() {
		var toggle = doc.querySelector('[data-pb-nav-toggle]');
		var nav = doc.querySelector('.pb-nav');

		if (!toggle || !nav) {
			return;
		}

		toggle.addEventListener('click', function () {
			var isOpen = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
		});
	}

	/* ۴. کلاس آماده‌بودن برای انیمیشن‌های CSS (بدون FOUC) */
	function markReady() {
		doc.documentElement.classList.add('pb-ready');
	}

	ready(function () {
		initQuantity();
		initBackToTop();
		initNavToggle();
		markReady();
	});
})();
