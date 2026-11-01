/*!
 * PartYab front-end — vehicle picker, garage, compat toggle, toasts.
 * No dependencies. Data comes from the partyab/v1 REST API.
 */
(function () {
	'use strict';

	var P = window.PartYab || {};
	var D = document;
	var FA = '۰۱۲۳۴۵۶۷۸۹';
	var GARAGE_COOKIE = 'partyab_garage';

	/* ---------------------------------------------------------------- utils */

	function fa(v) {
		return String(v == null ? '' : v).replace(/[0-9]/g, function (d) {
			return FA[+d];
		});
	}

	function esc(s) {
		return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
			return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
		});
	}

	function icon(name, cls) {
		var path = (P.icons && P.icons[name]) || '';
		return '<svg class="icon ' + (cls || '') + '" viewBox="0 0 24 24" aria-hidden="true">' + path + '</svg>';
	}

	function $(sel, root) {
		return (root || D).querySelector(sel);
	}

	function $$(sel, root) {
		return Array.prototype.slice.call((root || D).querySelectorAll(sel));
	}

	function normalize(s) {
		return String(s || '')
			.replace(/[\u064A\u0649]/g, 'ی')
			.replace(/\u0643/g, 'ک')
			.replace(/[\u200C\u200F\u200E\u064B-\u0652]/g, '')
			.replace(/\s+/g, ' ')
			.trim()
			.toLowerCase();
	}

	function latinDigits(s) {
		return String(s == null ? '' : s).replace(/[۰-۹]/g, function (d) {
			return String(FA.indexOf(d));
		});
	}

	/* --------------------------------------------------------------- cookie */

	function readCookie(name) {
		var match = D.cookie.match(new RegExp('(?:^|; )' + name.replace(/([.*+?^${}()|[\]\\])/g, '\\$1') + '=([^;]*)'));
		return match ? decodeURIComponent(match[1]) : '';
	}

	function writeCookie(name, value, days) {
		D.cookie = name + '=' + encodeURIComponent(value) + ';path=/;max-age=' + (days || 15552000) + ';SameSite=Lax';
	}

	function readJSON(name, fallback) {
		try {
			var raw = readCookie(name);
			return raw ? JSON.parse(raw) : fallback;
		} catch (e) {
			return fallback;
		}
	}

	var activeVehicle = function () {
		return readJSON(P.cookie || 'partyab_vehicle', null);
	};

	var garageList = function () {
		var list = readJSON(GARAGE_COOKIE, []);
		return Array.isArray(list) ? list : [];
	};

	var saveGarage = function (list) {
		writeCookie(GARAGE_COOKIE, JSON.stringify(list.slice(-3)), P.cookieTtl);
	};

	/* -------------------------------------------------------------- catalog */

	var catalog = null;
	var catalogRequest = null;

	function loadCatalog() {
		if (catalog) {
			return Promise.resolve(catalog);
		}
		if (catalogRequest) {
			return catalogRequest;
		}
		catalogRequest = fetch((P.restUrl || '/wp-json/partyab/v1/') + 'vehicles', { credentials: 'same-origin' })
			.then(function (r) {
				if (!r.ok) {
					throw new Error('catalog');
				}
				return r.json();
			})
			.then(function (data) {
				catalog = data && data.makes ? data : { groups: [], makes: [] };
				catalog.models = [];
				catalog.makes.forEach(function (make) {
					(make.models || []).forEach(function (model) {
						catalog.models.push({
							id: model.id,
							name: model.name,
							years: model.years,
							engines: model.engines || [],
							makeId: make.id,
							makeName: make.name,
							group: make.group || 'iran'
						});
					});
				});
				return catalog;
			})
			.catch(function () {
				catalog = { groups: [], makes: [], models: [] };
				return catalog;
			});

		return catalogRequest;
	}

	function findModel(id) {
		if (!catalog || !id) {
			return null;
		}
		for (var i = 0; i < catalog.models.length; i++) {
			if (catalog.models[i].id === id) {
				return catalog.models[i];
			}
		}
		return null;
	}

	function searchCatalog(query, limit) {
		var q = normalize(query);
		if (!catalog || q.length < 1) {
			return [];
		}
		var hits = [];
		catalog.models.forEach(function (m) {
			var haystack = normalize(m.name + ' ' + m.makeName);
			var pos = haystack.indexOf(q);
			if (pos > -1) {
				hits.push({ model: m, score: pos });
			}
		});
		hits.sort(function (a, b) {
			return a.score - b.score || a.model.name.length - b.model.name.length;
		});
		return hits.slice(0, limit || 8).map(function (h) {
			return h.model;
		});
	}

	/* -------------------------------------------------------------- modal */

	function openModal(sel) {
		var el = typeof sel === 'string' ? $(sel) : sel;
		if (!el) {
			return;
		}
		el.classList.add('is-open');
		el.setAttribute('aria-hidden', 'false');
		D.body.classList.add('modal-open');
	}

	function closeModal(sel) {
		var el = typeof sel === 'string' ? $(sel) : sel;
		if (!el) {
			return;
		}
		el.classList.remove('is-open');
		el.setAttribute('aria-hidden', 'true');
		if (!$$('.modal.is-open').length && !$$('.drawer.is-open').length) {
			D.body.classList.remove('modal-open');
		}
	}

	function closeAll() {
		$$('.modal.is-open, .drawer.is-open').forEach(function (el) {
			el.classList.remove('is-open');
			el.setAttribute('aria-hidden', 'true');
		});
		D.body.classList.remove('modal-open');
	}

	/* -------------------------------------------------------------- toast */

	function toast(message, kind) {
		var host = $('.py-toasts');
		if (!host) {
			host = D.createElement('div');
			host.className = 'py-toasts';
			D.body.appendChild(host);
		}
		var node = D.createElement('div');
		node.className = 'py-toast py-toast--' + (kind || 'info');
		node.innerHTML = icon(kind === 'error' ? 'alert' : 'check') + '<span>' + esc(message) + '</span>';
		host.appendChild(node);
		window.setTimeout(function () {
			node.classList.add('is-out');
			window.setTimeout(function () {
				node.remove();
			}, 260);
		}, 2600);
	}

	/* ------------------------------------------------------ vehicle picker */

	var picker = { view: 'search', modelId: null, year: null, engine: '' };

	function openPicker(modelId) {
		picker.view = 'search';
		picker.modelId = null;
		picker.year = null;
		picker.engine = '';
		if (modelId) {
			picker.modelId = modelId;
			picker.view = 'years';
		}
		renderPicker();
		openModal('#vehicleModal');
	}

	function renderPicker() {
		var body = $('#vehicleModalBody');
		if (!body) {
			return;
		}
		body.innerHTML = picker.view === 'search' ? pickerSearchHTML() : pickerYearsHTML();
		if (picker.view === 'search') {
			bindSearch($('#vmSearch'), $('#vmResults'));
			var input = $('[data-autofocus]');
			if (input) {
				window.setTimeout(function () {
					input.focus({ preventScroll: true });
				}, 60);
			}
		}
	}

	function stepsHTML(active) {
		var items = [['۱', 'مدل'], ['۲', 'سال'], ['۳', 'تیپ']];
		return (
			'<ol class="pp-steps" aria-label="مراحل انتخاب خودرو">' +
			items
				.map(function (it, i) {
					var n = i + 1;
					var cls = n < active ? 'is-done' : n === active ? 'is-current' : '';
					return '<li class="' + cls + '"><span>' + (n < active ? icon('check') : it[0]) + '</span>' + it[1] + '</li>';
				})
				.join('') +
			'</ol>'
		);
	}

	function pickerSearchHTML() {
		var vehicles = garageList();
		var current = activeVehicle();
		var garage = '';

		if (vehicles.length) {
			garage =
				'<div class="picker-garage">' +
				'<div class="picker-label">' +
				icon('garage') +
				' خودروهای من</div>' +
				'<div class="garage-rows">' +
				vehicles
					.map(function (v) {
						var on = current && current.model_id === v.model_id && Number(current.year) === Number(v.year);
						return (
							'<div class="garage-row ' +
							(on ? 'is-active' : '') +
							'">' +
							'<button type="button" class="gr-main" data-pick-vehicle="' +
							esc(v.model_id) +
							'" data-pick-year="' +
							esc(v.year) +
							'">' +
							'<span class="gr-icon">' +
							icon(on ? 'check' : 'car') +
							'</span>' +
							'<span class="gr-text"><b>' +
							esc(v.model_name) +
							'</b><small>' +
							esc(v.make_name) +
							' · ' +
							fa(v.year) +
							'</small></span>' +
							'</button>' +
							'<button type="button" class="gr-del" data-drop-vehicle="' +
							esc(v.model_id) +
							'|' +
							esc(v.year) +
							'" aria-label="' +
							esc(P.strings.removeCar) +
							'">' +
							icon('x') +
							'</button>' +
							'</div>'
						);
					})
					.join('') +
				'</div></div>';
		}

		var popular = [];
		if (catalog && catalog.models) {
			['m-206', 'm-pride', 'm-samand', 'm-corolla', 'm-tara'].forEach(function (id) {
				var model = findModel(id);
				if (model) {
					popular.push(model);
				}
			});
		}

		return (
			stepsHTML(1) +
			garage +
			'<div class="picker-search">' +
			'<label class="picker-label" for="vmSearch">' +
			icon('search') +
			' ' +
			esc(P.strings.searchCar) +
			'</label>' +
			'<div class="combo combo--inline">' +
			icon('search', 'combo-icon') +
			'<input id="vmSearch" type="text" placeholder="مثلاً ۲۰۶، پراید، سمند، کرولا…" autocomplete="off" role="combobox"' +
			' aria-expanded="false" aria-controls="vmResults" aria-autocomplete="list" data-autofocus>' +
			'<div class="combo-list" id="vmResults" role="listbox" aria-label="نتایج خودرو"></div>' +
			'</div></div>' +
			(popular.length
				? '<div class="picker-popular"><span>پرجست‌وجوترین خودروها:</span><div class="row">' +
				  popular
						.map(function (m) {
							return (
								'<button type="button" class="make-pill" data-quick-model="' +
								esc(m.id) +
								'">' +
								esc(m.name) +
								'</button>'
							);
						})
						.join('') +
				  '</div></div>'
				: '') +
			'<p class="picker-note">' +
			icon('info') +
			' اگر خودرویتان در فهرست نیست، مدل را بنویسید تا برای فروشگاه ثبت شود.</p>'
		);
	}

	function pickerYearsHTML() {
		var model = findModel(picker.modelId);
		if (!model) {
			return pickerSearchHTML();
		}

		var years = [];
		for (var y = model.years[1]; y >= model.years[0]; y--) {
			years.push(y);
		}

		return (
			stepsHTML(picker.year ? 3 : 2) +
			'<button type="button" class="picker-back" data-picker-back>' +
			icon('chevron-right') +
			' تغییر خودرو</button>' +
			'<div class="picker-model"><span class="pm-icon">' +
			icon('car') +
			'</span><div><b>' +
			esc(model.name) +
			'</b><small>' +
			esc(model.makeName) +
			'</small></div></div>' +
			'<div class="picker-block"><span class="picker-label">سال ساخت</span><div class="year-grid">' +
			years
				.map(function (year) {
					var on = picker.year === year;
					return (
						'<button type="button" class="year-btn ' +
						(on ? 'is-active' : '') +
						'" data-year="' +
						year +
						'" aria-pressed="' +
						on +
						'">' +
						fa(year) +
						'</button>'
					);
				})
				.join('') +
			'</div></div>' +
			(model.engines.length
				? '<div class="picker-block"><span class="picker-label">موتور / تیپ <em>(اختیاری)</em></span><div class="engine-chips">' +
				  model.engines
						.map(function (engine) {
							var on = picker.engine === engine;
							return (
								'<button type="button" class="engine-chip ' +
								(on ? 'is-active' : '') +
								'" data-engine="' +
								esc(engine) +
								'" aria-pressed="' +
								on +
								'">' +
								esc(engine) +
								'</button>'
							);
						})
						.join('') +
				  '</div></div>'
				: '') +
			'<div class="modal-foot">' +
			'<button type="button" class="btn btn-amber" data-confirm-vehicle ' +
			(picker.year ? '' : 'disabled') +
			'>' +
			icon('check') +
			' ' +
			esc(P.strings.confirm) +
			'</button>' +
			'<button type="button" class="btn btn-ghost" data-close-vehicle-modal>' +
			esc(P.strings.close) +
			'</button>' +
			'</div>'
		);
	}

	function bindSearch(input, list) {
		if (!input || !list) {
			return;
		}

		var render = function () {
			var query = input.value.trim();
			if (!query) {
				list.innerHTML = '';
				list.classList.remove('is-open');
				input.setAttribute('aria-expanded', 'false');
				return;
			}
			var results = searchCatalog(query, 8);
			if (!results.length) {
				list.innerHTML = '<div class="combo-empty">' + esc(P.strings.noResults) + '</div>';
			} else {
				list.innerHTML = results
					.map(function (m) {
						return (
							'<button type="button" class="combo-item" role="option" data-quick-model="' +
							esc(m.id) +
							'"><span class="ci-icon">' +
							icon('car') +
							'</span><span class="ci-text"><b>' +
							esc(m.name) +
							'</b><small>' +
							esc(m.makeName) +
							'</small></span>' +
							icon('chevron-left', 'ci-arrow') +
							'</button>'
						);
					})
					.join('');
			}
			list.classList.add('is-open');
			input.setAttribute('aria-expanded', 'true');
		};

		input.addEventListener('input', render);
		input.addEventListener('focus', function () {
			if (input.value.trim()) {
				render();
			}
		});
		input.addEventListener('keydown', function (e) {
			if (e.key === 'Escape') {
				list.classList.remove('is-open');
			}
		});
	}

	/* ---------------------------------------------------------- save vehicle */

	function persistVehicle(model, year, engine, redirect) {
		var payload = {
			make_id: model.makeId,
			model_id: model.id,
			year: year,
			engine: engine || ''
		};

		writeCookie(P.cookie || 'partyab_vehicle', JSON.stringify(payload), P.cookieTtl);

		var list = garageList().filter(function (v) {
			return !(v.model_id === model.id && Number(v.year) === Number(year));
		});
		list.push({
			make_id: model.makeId,
			make_name: model.makeName,
			model_id: model.id,
			model_name: model.name,
			year: year,
			engine: engine || ''
		});
		saveGarage(list);

		var after = function () {
			window.location.href = redirect || window.location.href;
		};

		fetch((P.restUrl || '/wp-json/partyab/v1/') + 'vehicle', {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': P.nonce || '' },
			body: JSON.stringify(payload)
		})
			.then(function () {
				after();
			})
			.catch(function () {
				// The cookie is already set; the page will read it on reload.
				after();
			});
	}

	/* --------------------------------------------------------------- garage */

	function garageHTML() {
		var vehicles = garageList();
		var current = activeVehicle();

		if (!vehicles.length) {
			return (
				'<div class="empty g-empty" style="padding:40px">' +
				'<span class="empty-icon">' +
				icon('garage') +
				'</span>' +
				'<b>' +
				esc(P.strings.emptyGarage) +
				'</b>' +
				'<p>مدل خودرو را بنویسید و سال ساخت را انتخاب کنید؛ پارت‌یاب فقط قطعات سازگار را نشان می‌دهد.</p>' +
				'<button type="button" class="btn btn-amber" data-open-vehicle>' +
				icon('plus') +
				' ' +
				esc(P.strings.letsGo) +
				'</button></div>'
			);
		}

		return (
			'<div class="g-cards">' +
			vehicles
				.map(function (v) {
					var on = current && current.model_id === v.model_id && Number(current.year) === Number(v.year);
					return (
						'<article class="g-card ' +
						(on ? 'is-active' : '') +
						'">' +
						'<div class="gv-top"><span class="gv-badge">' +
						(on ? icon('check') + ' فعال' : icon('car') + ' در گاراژ') +
						'</span>' +
						'<button type="button" class="gr-del" data-drop-vehicle="' +
						esc(v.model_id) +
						'|' +
						esc(v.year) +
						'" aria-label="' +
						esc(P.strings.removeCar) +
						'">' +
						icon('x') +
						'</button></div>' +
						'<h3 class="gv-name">' +
						esc(v.model_name) +
						'</h3>' +
						'<p class="gv-meta">' +
						esc(v.make_name) +
						' · ' +
						fa(v.year) +
						(v.engine ? ' · ' + esc(v.engine) : '') +
						'</p>' +
						'<div class="gv-actions">' +
						(on
							? '<a class="btn btn-ghost btn-sm" href="' + esc(P.shopUrl + '?compat=1') + '">' + icon('grid') + ' قطعات سازگار</a>'
							: '<button type="button" class="btn btn-amber btn-sm" data-activate-vehicle="' +
							  esc(v.model_id) +
							  '|' +
							  esc(v.year) +
							  '">' +
							  icon('refresh') +
							  ' فعال‌سازی</button>') +
						'</div></article>'
					);
				})
				.join('') +
			(vehicles.length < 3
				? '<button type="button" class="gv-add" data-open-vehicle><span>' +
				  icon('plus') +
				  '</span><b>افزودن خودرو</b><small>' +
				  esc(P.strings.maxCars) +
				  '</small></button>'
				: '') +
			'</div>'
		);
	}

	function renderGarage() {
		var root = $('#garageRoot');
		if (!root) {
			return;
		}
		root.innerHTML = garageHTML();
	}

	/* -------------------------------------------------------------- handlers */

	D.addEventListener('click', function (e) {
		var target = e.target;

		var opener = target.closest('[data-open-vehicle]');
		if (opener) {
			e.preventDefault();
			loadCatalog().then(function () {
				openPicker(opener.getAttribute('data-open-vehicle') || null);
			});
			return;
		}

		if (target.closest('[data-close-vehicle-modal]')) {
			e.preventDefault();
			closeModal('#vehicleModal');
			return;
		}

		var navOpen = target.closest('[data-open-nav]');
		if (navOpen) {
			e.preventDefault();
			openModal('#navDrawer');
			return;
		}

		if (target.closest('[data-close-nav]')) {
			e.preventDefault();
			closeModal('#navDrawer');
			return;
		}

		var quick = target.closest('[data-quick-model]');
		if (quick) {
			e.preventDefault();
			picker.modelId = quick.getAttribute('data-quick-model');
			picker.year = null;
			picker.engine = '';
			picker.view = 'years';
			renderPicker();
			return;
		}

		if (target.closest('[data-picker-back]')) {
			e.preventDefault();
			picker.view = 'search';
			picker.year = null;
			renderPicker();
			return;
		}

		var yearBtn = target.closest('[data-year]');
		if (yearBtn) {
			e.preventDefault();
			picker.year = Number(yearBtn.getAttribute('data-year'));
			renderPicker();
			return;
		}

		var engineBtn = target.closest('[data-engine]');
		if (engineBtn) {
			e.preventDefault();
			var value = engineBtn.getAttribute('data-engine');
			picker.engine = picker.engine === value ? '' : value;
			renderPicker();
			return;
		}

		if (target.closest('[data-confirm-vehicle]')) {
			e.preventDefault();
			var model = findModel(picker.modelId);
			if (!model || !picker.year) {
				return;
			}
			persistVehicle(model, picker.year, picker.engine, $('#vehicleModal').getAttribute('data-redirect') || P.shopUrl + '?compat=1');
			return;
		}

		var pick = target.closest('[data-pick-vehicle]');
		if (pick) {
			e.preventDefault();
			var pickModel = findModel(pick.getAttribute('data-pick-vehicle'));
			if (pickModel) {
				persistVehicle(pickModel, Number(pick.getAttribute('data-pick-year')), '', $('#vehicleModal').getAttribute('data-redirect') || null);
			}
			return;
		}

		var activate = target.closest('[data-activate-vehicle]');
		if (activate) {
			e.preventDefault();
			var parts = activate.getAttribute('data-activate-vehicle').split('|');
			var actModel = findModel(parts[0]);
			if (actModel) {
				persistVehicle(actModel, Number(parts[1]), '', null);
			}
			return;
		}

		var drop = target.closest('[data-drop-vehicle]');
		if (drop) {
			e.preventDefault();
			var bits = drop.getAttribute('data-drop-vehicle').split('|');
			var remaining = garageList().filter(function (v) {
				return !(v.model_id === bits[0] && String(v.year) === String(bits[1]));
			});
			saveGarage(remaining);
			var current = activeVehicle();
			if (current && current.model_id === bits[0] && String(current.year) === String(bits[1])) {
				if (remaining.length) {
					writeCookie(P.cookie, JSON.stringify(remaining[remaining.length - 1]), P.cookieTtl);
				} else {
					writeCookie(P.cookie, '', -1);
				}
			}
			toast(P.strings.removeCar, 'info');
			window.setTimeout(function () {
				window.location.reload();
			}, 400);
			return;
		}

		if (target.closest('[data-remove-vehicle]')) {
			e.preventDefault();
			writeCookie(P.cookie, '', -1);
			window.location.href = window.location.pathname;
			return;
		}

		// Close modals when clicking the backdrop or an empty area.
		if (target.classList && (target.classList.contains('modal-backdrop') || target.classList.contains('drawer-backdrop'))) {
			closeAll();
		}
	});

	D.addEventListener('keydown', function (e) {
		if (e.key === 'Escape') {
			closeAll();
		}
	});

	/* ---------------------------------------------------- hero inline search */

	function initHero() {
		var input = $('#heroCar');
		if (!input) {
			return;
		}
		var list = $('#heroResults');
		var submit = $('[data-hero-go]');

		loadCatalog().then(function () {
			bindSearch(input, list);
		});

		if (submit) {
			submit.addEventListener('click', function (e) {
				var query = input.value.trim();
				if (query && !$('#heroResults .combo-item[aria-selected="true"]')) {
					// Let the form submit to the shop search.
					return;
				}
				if (!query) {
					e.preventDefault();
					openModal('#vehicleModal');
					renderPicker();
				}
			});
		}
	}

	/* ------------------------------------------------------- WooCommerce glue */

	function initWoo() {
		if (window.jQuery) {
			window.jQuery(D.body).on('added_to_cart', function (event, fragments) {
				if (fragments && fragments['.cart-count']) {
					var counter = $('.cart-count');
					if (counter) {
						counter.outerHTML = fragments['.cart-count'];
					}
				}
				toast(P.strings.added, 'success');
				D.body.classList.add('cart-bump');
				window.setTimeout(function () {
					D.body.classList.remove('cart-bump');
				}, 600);
			});
		}

		$$('.shop-compat-toggle').forEach(function (node) {
			node.addEventListener('click', function () {
				$$('.shop-compat-toggle').forEach(function (other) {
					other.classList.toggle('is-active');
				});
			});
		});
	}

	/* ------------------------------------------------------------------ boot */

	function boot() {
		initHero();
		initWoo();
		loadCatalog().then(renderGarage);
	}

	if (D.readyState === 'loading') {
		D.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	window.PartYabUI = { openPicker: openPicker, toast: toast, loadCatalog: loadCatalog };
})();
