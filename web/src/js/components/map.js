"use strict";
import Filter from './filter';

export default class YaMapAll {
	constructor(filter) {
		let self = this;
		var fired = false;
		this.filter = filter;
		console.log(this.filter);

		this.myMap = false;
		this.object = false;
		this.myBalloonLayout = false;
		this.objectCoordinates = false;

		this.myBalloonHeader = false;
		this.myBalloonBody = false;


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
		const mapPopupBlock = $('._popup');

		function closePopup() {
			mapPopupBlock.removeClass('_active');
		}

		$(document).on('click touchstart', '.map_popup_close', function () {
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


	restaurantMapShow(restaurantCoordinates, restaurantMyBalloonHeader, restaurantMyBalloonBody) {
		let self = this;

		const mapPopupBlock = $('._popup');

		mapPopupBlock.addClass('_active');

		self.objectCoordinates = restaurantCoordinates;
		self.myBalloonHeader = restaurantMyBalloonHeader;
		self.myBalloonBody = restaurantMyBalloonBody;

		self.myBalloonLayout = ymaps.templateLayoutFactory.createClass(
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

		self.object = new ymaps.Placemark(self.objectCoordinates, {
			balloonContentHeader: self.myBalloonHeader,
			balloonContentBody: self.myBalloonBody
		}, {

			// Необходимо указать данный тип макета.
			iconLayout: 'default#image',
			// Своё изображение иконки метки.
			iconImageHref: '/img/map_geo_icon.svg',
			// Размеры метки.
			iconImageSize: [42, 60],
			// Смещение левого верхнего угла иконки относительно
			// её "ножки" (точки привязки).
			iconImageOffset: [-21, -65],

			balloonLayout: self.myBalloonLayout,
			hideIconOnBalloonOpen: false,
			balloonOffset: [-150, 17],
		});

		self.myMap.geoObjects.removeAll();
		self.myMap.geoObjects.add(self.object);
		self.myMap.setCenter(self.objectCoordinates);
		self.object.balloon.open("", "", { closeButton: false });
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
		let self = this;
		this.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').then(() => {
			const ymaps = global.ymaps;

			ymaps.ready(function () {
				let map = document.querySelector(".map_popup");
				self.myMap = new ymaps.Map(map, { center: [55.76, 37.64], zoom: 15, controls: [] },
					{ suppressMapOpenBlock: true });

				self.myMap.behaviors.disable('scrollZoom');

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

				self.myMap.controls.add(zoomControl);
				self.myMap.controls.add(geolocationControl);

				self.objectCoordinates = [$("#map_popup").attr("data-mapDotX"), $("#map_popup").attr("data-mapDotY")];
				self.myBalloonHeader = $("#map_popup").attr("data-name");
				self.myBalloonBody = $("#map_popup").attr("data-address");

				self.myBalloonLayout = ymaps.templateLayoutFactory.createClass(
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

				self.object = new ymaps.Placemark(self.objectCoordinates, {
					balloonContentHeader: self.myBalloonHeader,
					balloonContentBody: self.myBalloonBody
				}, {
					// Необходимо указать данный тип макета.
					iconLayout: 'default#image',
					// Своё изображение иконки метки.
					iconImageHref: '/img/map_geo_icon.svg',
					// Размеры метки.
					iconImageSize: [42, 60],
					// Смещение левого верхнего угла иконки относительно
					// её "ножки" (точки привязки).
					iconImageOffset: [-21, -65],

					balloonLayout: self.myBalloonLayout,
					hideIconOnBalloonOpen: false,
					balloonOffset: [-150, 17],
				});

				self.myMap.geoObjects.add(self.object);
				self.myMap.setCenter(self.objectCoordinates);
				self.object.balloon.open("", "", { closeButton: false });
			});
		});
	}



	// init() {
	// 	let self = this;
	// 	this.script('//api-maps.yandex.ru/2.1/?lang=ru_RU').then(() => {
	// 		const ymaps = global.ymaps;

	// 		ymaps.ready(function () {
	// 			let map = document.querySelector('.map');
	// 			let myMap = new ymaps.Map(map, { center: [55.76, 37.64], zoom: 15 });
	// 			myMap.behaviors.disable('scrollZoom');

	// 			let myBalloonLayout = ymaps.templateLayoutFactory.createClass(
	// 				`<div class="balloon_layout">
	// 					<a class="close" href="#"></a>
	// 					<div class="arrow"></div>
	// 					<div class="balloon_inner">
	// 						$[[options.contentLayout]]
	// 					</div>
	// 				</div>`, {
	// 				build: function () {
	// 					this.constructor.superclass.build.call(this);

	// 					this._$element = $('.balloon_layout', this.getParentElement());

	// 					this._$element.find('.close')
	// 						.on('click', $.proxy(this.onCloseClick, this));

	// 				},

	// 				clear: function () {
	// 					this._$element.find('.close')
	// 						.off('click');

	// 					this.constructor.superclass.clear.call(this);
	// 				},

	// 				onCloseClick: function (e) {
	// 					e.preventDefault();

	// 					this.events.fire('userclose');
	// 				},

	// 				getShape: function () {
	// 					if (!this._isElement(this._$element)) {
	// 						return myBalloonLayout.superclass.getShape.call(this);
	// 					}

	// 					var position = this._$element.position();

	// 					return new ymaps.shape.Rectangle(new ymaps.geometry.pixel.Rectangle([
	// 						[position.left, position.top], [
	// 							position.left + this._$element[0].offsetWidth,
	// 							position.top + this._$element[0].offsetHeight + this._$element.find('.arrow')[0].offsetHeight
	// 						]
	// 					]));
	// 				},

	// 				_isElement: function (element) {
	// 					return element && element[0] && element.find('.arrow')[0];
	// 				}
	// 			}
	// 			);

	// 			let myBalloonContentLayout = ymaps.templateLayoutFactory.createClass(
	// 				`<div class="balloon_wrapper">

	// 					<div class="balloon_content">

	// 						<img src={{properties.img}}>

	// 						<div class="balloon_text">

	// 							<div class="balloon_header">
	// 								{{properties.organization}}
	// 							</div>

	// 							<div class="balloon_address">
	// 								{{properties.address}}
	// 							</div>

	// 						</div>

	// 					</div>

	// 					<div class="balloon_link">
	// 						<button class="balloon_link_button _button"><a href="{{properties.link}}">Посмотреть зал</a></button>
	// 					</div>

	// 				</div>`
	// 			);

	// 			let objectManager = new ymaps.ObjectManager(
	// 				{
	// 					geoObjectBalloonLayout: myBalloonLayout,
	// 					geoObjectBalloonContentLayout: myBalloonContentLayout,
	// 					geoObjectHideIconOnBalloonOpen: false,
	// 					geoObjectBalloonOffset: [-360, 17],
	// 					clusterize: true,
	// 					clusterDisableClickZoom: false,
	// 					clusterBalloonItemContentLayout: myBalloonContentLayout,
	// 					clusterIconColor: "green",
	// 					geoObjectIconColor: "green"
	// 				}
	// 			);

	// 			let serverData = null;
	// 			let data = {
	// 				subdomain_id: $('[data-map-api-subid]').data('map-api-subid'),
	// 				filter: JSON.stringify(self.filter.state)
	// 			};

	// 			$.ajax({
	// 				type: "POST",
	// 				url: "/api/map_all/",
	// 				data: data,
	// 				success: function (response) {
	// 					serverData = response;

	// 					objectManager.add(serverData);
	// 					//console.log(`objectManager length: ${objectManager.objects.getLength()}`);
	// 					myMap.geoObjects.add(objectManager);
	// 					//console.log(`objectManager: ${objectManager.getBounds()}`);
	// 					myMap.setBounds(objectManager.getBounds());
	// 				},
	// 				error: function (response) {

	// 				}
	// 			});



	/*let serverResponse = fetch("/api/map_all/", {
			 method: 'post',
			 mode:    'cors',
			 headers: {
				'Content-Type': 'application/json',  // sent request
				'Accept':       'application/json'   // expected data sent back
			 },
			 body: JSON.stringify(data),
		})
		.then(function(response) {
			if (response.ok) { 
				let json = response.json();
				return json;
			} else {
				alert("Ошибка HTTP: " + response.status);
			}
		})
		.then(function(json) {
			serverData = json;
			
			objectManager.add(serverData);  
			//console.log(`objectManager length: ${objectManager.objects.getLength()}`);
			myMap.geoObjects.add(objectManager);
			//console.log(`objectManager: ${objectManager.getBounds()}`);
			myMap.setBounds(objectManager.getBounds());
		});*/



	// });
	// });
	// }
}