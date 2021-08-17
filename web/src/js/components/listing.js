'use strict';
import Swiper from 'swiper';

import Filter from './filter';
import YaMapAll from './map';
import Breadcrumbs from './breadcrumbs';

export default class Listing {
	constructor($block) {
		self = this;
		this.block = $block;
		this.filter = new Filter($('[data-filter-wrapper]'));
		this.yaMap = new YaMapAll(this.filter);
		this.breadcrumbs = new Breadcrumbs();

		//КЛИК ПО КНОПКЕ "ПРИМЕНИТЬ"
		$('[data-filter-button]').on('click', function () {
			self.reloadListing();

			setTimeout(catalogSliderInit, 500);
			$(this).closest('[data-filter-select-block]').removeClass('_active');
			if ($(window).width() < '768') {
				$('body').toggleClass('_overflow')
			}
		});

		//КЛИК ПО КНОПКЕ "СБРОСИТЬ"
		$('[data-filter-reset]').on('click', function () {
			self.reloadListing();

			setTimeout(catalogSliderInit, 500);
		});

		//КЛИК ПО ПАГИНАЦИИ
		// $('body').on('click', '[data-pagination-wrapper] [data-listing-pagitem]', function () {
		// 	self.reloadListing($(this).data('page-id'));
		// 	setTimeout(catalogSliderInit, 300);
		// });

		//КЛИК ПО КНОПКЕ "ПОКАЗАТЬ ЕЩЕ"
		$('body').on('click', '[data-pagination-wrapper] [data-listing-pagitem]', function () {
			self.loadMoreListing($(this).data('page-id'));
			setTimeout(catalogSliderInit, 500);
		});
		// console.log(this);

		function catalogSliderInit() {
			let galleryRestaurantRoom = new Swiper('.item_listing_slider', {
				slidesPerView: "auto",
				spaceBetween: 10,

				navigation: {
					nextEl: '.item_slider_next',
					prevEl: '.item_slider_prev',
				},

				breakpoints: {
					// when window width is >= 768px
					768: {
						spaceBetween: 20,
					}
				}
			});
		}

		catalogSliderInit();

		//mapPopup START
		$('[data-listing-list]').on('click', '[data-title-address]', function (e) {

			let restaurantCoordinates = [$(this).closest('.item').attr("data-restaurant-mapDotX"), $(this).closest('.item').attr("data-restaurant-mapDotY")];
			let restaurantMyBalloonHeader = $(this).closest('.item').attr("data-restaurant-name");
			let restaurantMyBalloonBody = $(this).closest('.item').attr("data-restaurant-address");

			self.yaMap.restaurantMapShow(restaurantCoordinates, restaurantMyBalloonHeader, restaurantMyBalloonBody);

			let currentItem = $(e.target).closest('.item');

			$('.map_popup_title').text(currentItem.attr('data-restaurant-name'));
			$('.map_popup_address span').text(currentItem.attr('data-restaurant-address'));
			$('.map_popup_phone').text(currentItem.attr('data-restaurant-phone')).attr('href', 'tel:' + currentItem.attr('data-restaurant-phone').replace(/-|\s/g, ""));
		});
		//mapPopup END
	}

	reloadListing(page = 1) {
		let self = this;

		function declOfNum(n, text_forms) {
			n = Math.abs(n) % 100;
			var n1 = n % 10;
			if (n > 10 && n < 20) { return text_forms[2]; }
			if (n1 > 1 && n1 < 5) { return text_forms[1]; }
			if (n1 == 1) { return text_forms[0]; }
			return text_forms[2];
		}

		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				// console.log(response.params_filter);
				// console.log(self.filter.state);
				//ym(66603799,'reachGoal','filter');
				//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Search', 'eventAction' : 'Filter'});

				$('[data-listing-list]').html(response.listing);
				$('[data-listing-total]').html(declOfNum(response.total, ['Найдена','Найдено','Найдено']) +' '+ response.total +' '+ declOfNum(response.total, ['площадка','площадки','площадок']));
				// $('[data-listing-list]').append(response.listing);
				// $('[data-listing-title]').html(response.title);
				$('[data-listing-text-top]').html(response.text_top);
				// $('[data-listing-text-bottom]').html(response.text_bottom);
				$('[data-listing-text-bottom]').html(response.text_1);
				$('[data-pagination-wrapper]').html(response.pagination);

				$('.header__title h1').html(response.seo['h1']);
				if (response.seo["media"]["advantages"] != 0) {
					$('[data-seo-img]').html('<img src="' + response.seo["media"]["advantages"][0]["src"] + '" alt="' + response.seo["media"]["advantages"][0]["alt"] + '">');
				}
				

				self.block.removeClass('_loading');
				// $('html,body').animate({ scrollTop: $('.items_list').offset().top - 160 }, 400);

				// history.pushState({}, '', '/ploshhadki/' + response.url);
				history.pushState({}, '', '/' + response.url);

				self.breadcrumbs = new Breadcrumbs();

				console.log(response);
			}
		);
	}

	loadMoreListing(page = 1) {
		let self = this;

		self.block.addClass('_loading');
		self.filter.filterListingSubmit(page);
		self.filter.promise.then(
			response => {
				$('[data-listing-list]').append(response.listing);
				$('[data-pagination-wrapper]').html(response.pagination);
				self.block.removeClass('_loading');

				// history.pushState({}, '', '/ploshhadki/' + response.url);
				history.pushState({}, '', '/' + response.url);

				self.breadcrumbs = new Breadcrumbs();

				// $('[data-page-id]').attr('data-page-id', +$('[data-page-id]').attr('data-page-id') + 1);
				// console.log($('[data-page-id]').attr('data-page-id'));
			}
		);
	}
}