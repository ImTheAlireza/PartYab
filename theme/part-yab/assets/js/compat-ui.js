/**
 * PartYab — رابط کاربری سازگاری خودرو
 * وظیفه: پر کردن مدل‌ها بر اساس برند انتخاب‌شده، بدون درخواست شبکه.
 */
(function () {
	'use strict';

	function initCarSelector() {
		var form = document.querySelector('.pb-car');
		if (!form || !window.partyabData || !window.partyabData.cars) {
			return;
		}

		var brand = form.querySelector('[data-pb-brand]');
		var model = form.querySelector('[data-pb-model]');
		var cars = window.partyabData.cars;
		var initialModel = window.partyabData.currentModel || '';

		if (!brand || !model) {
			return;
		}

		function fillModels(preselect) {
			var models = cars[brand.value] || [];

			model.innerHTML = '';

			if (!models.length) {
				var opt = document.createElement('option');
				opt.value = '';
				opt.textContent = brand.value ? 'مدلی ثبت نشده' : 'اول برند را انتخاب کن';
				model.appendChild(opt);
				return;
			}

			models.forEach(function (name) {
				var option = document.createElement('option');
				option.value = name;
				option.textContent = name;
				if (preselect && name === preselect) {
					option.selected = true;
				}
				model.appendChild(option);
			});
		}

		brand.addEventListener('change', function () {
			fillModels('');
		});

		if (brand.value) {
			fillModels(initialModel);
		}
	}

	if (document.readyState !== 'loading') {
		initCarSelector();
	} else {
		document.addEventListener('DOMContentLoaded', initCarSelector);
	}
})();
