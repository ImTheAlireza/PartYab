/**
 * پیشنهاد زنده جستجو — بدون وابستگی، با کنترل کیبورد و ARIA.
 */
(function () {
	'use strict';

	var input = document.querySelector('[data-pb-suggest-input]');
	var box = document.querySelector('[data-pb-suggest-results]');

	if (!input || !box || typeof window.partyabData === 'undefined') {
		return;
	}

	var timer = null;
	var controller = null;
	var activeIndex = -1;

	function close() {
		box.hidden = true;
		box.innerHTML = '';
		input.setAttribute('aria-expanded', 'false');
		activeIndex = -1;
	}

	function esc(value) {
		var div = document.createElement('div');
		div.textContent = value == null ? '' : String(value);
		return div.innerHTML;
	}

	function row(item, index) {
		var stock = item.stock ? '' : '<span class="pb-suggest__out">ناموجود</span>';
		var badge = item.badge ? '<span class="pb-suggest__badge">' + esc(item.badge) + '</span>' : '';
		var image = item.image
			? '<img src="' + esc(item.image) + '" alt="" width="44" height="44" loading="lazy">'
			: '<span class="pb-suggest__noimg" aria-hidden="true"></span>';

		return '<a class="pb-suggest__item" role="option" id="pb-suggest-' + index + '" href="' + esc(item.url) + '">'
			+ image
			+ '<span class="pb-suggest__text"><strong>' + esc(item.title) + '</strong>'
			+ '<small>' + (item.sku ? esc(item.sku) : '') + '</small></span>'
			+ '<span class="pb-suggest__meta"><span class="pb-suggest__price">' + esc(item.price) + '</span>' + stock + badge + '</span>'
			+ '</a>';
	}

	function render(data) {
		var html = '';

		if (data.categories && data.categories.length) {
			html += '<div class="pb-suggest__group">دسته‌ها</div>';
			data.categories.forEach(function (cat) {
				html += '<a class="pb-suggest__cat" role="option" href="' + esc(cat.url) + '">' + esc(cat.name) + '</a>';
			});
		}

		if (data.items && data.items.length) {
			html += '<div class="pb-suggest__group">قطعات</div>';
			data.items.forEach(function (item, i) {
				html += row(item, i);
			});
		}

		if (!html) {
			html = '<div class="pb-suggest__empty">چیزی پیدا نشد. شماره فنی را کامل‌تر بنویس یا با پشتیبانی تماس بگیر.</div>';
		}

		if (data.total > data.items.length && data.items.length) {
			html += '<a class="pb-suggest__all" href="' + esc(input.form.action) + '?s=' + encodeURIComponent(input.value) + '&post_type=product">'
				+ 'همه ' + data.total + ' نتیجه ←</a>';
		}

		box.innerHTML = html;
		box.hidden = false;
		input.setAttribute('aria-expanded', 'true');
		activeIndex = -1;
	}

	function fetchSuggestions() {
		var term = input.value.trim();

		if (term.length < 2) {
			close();
			return;
		}

		if (controller) {
			controller.abort();
		}

		controller = new AbortController();

		var url = window.partyabData.ajaxUrl
			+ '?action=partyab_suggest&nonce=' + encodeURIComponent(window.partyabData.nonce)
			+ '&q=' + encodeURIComponent(term);

		fetch(url, { signal: controller.signal, credentials: 'same-origin' })
			.then(function (response) { return response.json(); })
			.then(function (json) {
				if (json && json.success && json.data) {
					render(json.data);
				}
			})
			.catch(function () { /* درخواست لغو شده یا شبکه قطع است */ });
	}

	input.addEventListener('input', function () {
		clearTimeout(timer);
		timer = setTimeout(fetchSuggestions, 220);
	});

	input.addEventListener('keydown', function (event) {
		var options = box.hidden ? [] : box.querySelectorAll('[role="option"]');

		if (event.key === 'Escape') {
			close();
			return;
		}

		if (!options.length) {
			return;
		}

		if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
			event.preventDefault();
			activeIndex = event.key === 'ArrowDown'
				? (activeIndex + 1) % options.length
				: (activeIndex - 1 + options.length) % options.length;

			options.forEach(function (option, i) {
				option.classList.toggle('is-active', i === activeIndex);
			});

			options[activeIndex].scrollIntoView({ block: 'nearest' });
		}

		if (event.key === 'Enter' && activeIndex > -1) {
			event.preventDefault();
			options[activeIndex].click();
		}
	});

	document.addEventListener('click', function (event) {
		if (!box.contains(event.target) && event.target !== input) {
			close();
		}
	});
})();
