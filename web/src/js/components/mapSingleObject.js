"use strict";

export default class YaMapSingleObject {
	constructor() {
		let self = this;
		var fired = false;

		window.addEventListener('click', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('scroll', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('mousemove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		window.addEventListener('touchmove', () => {
			if (fired === false) {
				fired = true;
				load_other();
			}
		}, { passive: true });

		function load_other() {
			setTimeout(function () {
				self.init();
			}, 100);

		}

		//map popup START
		// const body = $('body');
		const mapPopupBlock = $('._popup');

		function openPopup() {
			// body.addClass('_overflow');
			mapPopupBlock.addClass('_active');
		}

		function closePopup() {
			// body.removeClass('_overflow');
			mapPopupBlock.removeClass('_active');
		}

		$('[data-title-address]').on('click', function () {
			// ym(84074572,'reachGoal','show_lead');
			let attrCommission = $(this).attr('data-commission');
			if (typeof attrCommission !== typeof undefined && attrCommission !== false) {
				ym(84074572, 'reachGoal', 'show_lead');
			}
			openPopup();
		});

		// $('.content_block_map._popup').on('click', $('.map_popup_close'), function () {
			// closePopup();
		// });
		$('.map_popup_close').on('click', function () {
			closePopup();
		});

		$(document).mouseup(function (e) { // событие клика по веб-документу
			let mapPopupContainer = $('.map_popup_container'); // тут указываем ID элемента

			if (mapPopupContainer.has(e.target).length === 0) { //если клик был не по дочерним элементам блока
				closePopup();
			}
		});
		//map popup END
	}

	script(url) {
		if (Array.isArray(url)) {
			let self = this;
			let prom = [];
			url.forEach(function (item) {
				prom.push(self.script(item));
			});
			return Promise.all(prom);
		}

		return new Promise(function (resolve, reject) {
			let r = false;
			let t = document.getElementsByTagName('script')[0];
			let s = document.createElement('script');

			s.type = 'text/javascript';
			s.src = url;
			s.async = true;
			s.onload = s.onreadystatechange = function () {
				if (!r && (!this.readyState || this.readyState === 'complete')) {
					r = true;
					resolve(this);
				}
			};
			s.onerror = s.onabort = reject;
			t.parentNode.insertBefore(s, t);
		});
	}

	init() {
		this.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').then(() => {
			const ymaps = global.ymaps;
			ymaps.ready(function () {
				let map = document.querySelector(".map");
				let myMap = new ymaps.Map(map, { center: [55.76, 37.64], zoom: 15, controls: [] },
					{ suppressMapOpenBlock: true });

				myMap.behaviors.disable('scrollZoom');

				let zoomControl = new ymaps.control.ZoomControl({
					options: {
						size: "small",
						position: {
							top: 10,
							right: 10
						}

					}
				});

				let geolocationControl = new ymaps.control.GeolocationControl({
					options: {
						noPlacemark: true,
						position: {
							top: 10,
							left: 10
						}
					}
				});

				myMap.controls.add(zoomControl);
				myMap.controls.add(geolocationControl);

				let objectCoordinates = [$("#map").attr("data-mapDotX"), $("#map").attr("data-mapDotY")];
				let myBalloonHeader = $("#map").attr("data-name");
				let myBalloonBody = $("#map").attr("data-address");
				let myBalloonLayout = ymaps.templateLayoutFactory.createClass(
					`<div class="balloon_layout _single_object">
    					<div class="arrow"></div>
              <div class="balloon_inner">
                <div class="balloon_inner_header">
                  {{properties.balloonContentHeader}}
                </div>
                <div class="balloon_inner_body">
                  {{properties.balloonContentBody}}
                </div>
    					</div>
    				</div>`
				);

				let object = new ymaps.Placemark(objectCoordinates, {
					balloonContentHeader: myBalloonHeader,
					balloonContentBody: myBalloonBody
				}, {
					// iconColor: "red",

					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/map_geo_icon.svg',
					// Размеры метки.
					iconImageSize: [42, 60],
					// Смещение левого верхнего угла иконки относительно
					// её "ножки" (точки привязки).
					iconImageOffset: [-21, -65],

					balloonLayout: myBalloonLayout,
					hideIconOnBalloonOpen: false,
					balloonOffset: [-150, 17],
				});

				myMap.geoObjects.add(object);
				myMap.setCenter(objectCoordinates);
				object.balloon.open("", "", { closeButton: false });


				//popupMap on Item Page START
				map = document.querySelector(".map_popup");
				myMap = new ymaps.Map(map, { center: [55.76, 37.64], zoom: 15, controls: [] },
					{ suppressMapOpenBlock: true });

				myMap.behaviors.disable('scrollZoom');

				zoomControl = new ymaps.control.ZoomControl({
					options: {
						size: "small",
						position: {
							top: 10,
							right: 10
						}

					}
				});

				geolocationControl = new ymaps.control.GeolocationControl({
					options: {
						noPlacemark: true,
						position: {
							top: 10,
							left: 10
						}
					}
				});

				myMap.controls.add(zoomControl);
				myMap.controls.add(geolocationControl);

				objectCoordinates = [$("#map_popup").attr("data-mapDotX"), $("#map_popup").attr("data-mapDotY")];
				myBalloonHeader = $("#map_popup").attr("data-name");
				myBalloonBody = $("#map_popup").attr("data-address");
				myBalloonLayout = ymaps.templateLayoutFactory.createClass(
					`<div class="balloon_layout _single_object">
    					<div class="arrow"></div>
              <div class="balloon_inner">
                <div class="balloon_inner_header">
                  {{properties.balloonContentHeader}}
                </div>
                <div class="balloon_inner_body">
                  {{properties.balloonContentBody}}
                </div>
    					</div>
    				</div>`
				);

				object = new ymaps.Placemark(objectCoordinates, {
					balloonContentHeader: myBalloonHeader,
					balloonContentBody: myBalloonBody
				}, {
					// iconColor: "green",

					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/map_geo_icon.svg',
					// Размеры метки.
					iconImageSize: [42, 60],
					// Смещение левого верхнего угла иконки относительно
					// её "ножки" (точки привязки).
					iconImageOffset: [-21, -65],

					balloonLayout: myBalloonLayout,
					hideIconOnBalloonOpen: false,
					balloonOffset: [-150, 17],
				});

				myMap.geoObjects.add(object);
				myMap.setCenter(objectCoordinates);
				object.balloon.open("", "", { closeButton: false });
				//popupMap on Item Page  END

			});
		});



	}
}