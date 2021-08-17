'use strict';
import Swiper from 'swiper';
import 'slick-carousel';
import * as Lightbox from '../../../node_modules/lightbox2/dist/js/lightbox.js';
import Breadcrumbs from './breadcrumbs';

export default class Item {
	constructor($item) {
		var self = this;
		this.sliders = new Array();
		this.breadcrumbs = new Breadcrumbs();

		$('[data-action="show_phone"]').on("click", function () {
			$(".object_book").addClass("_active");
			$(".object_book_hidden").addClass("_active");
			$(".object_book_interactive_part").removeClass("_hide");
			$(".object_book_send_mail").removeClass("_hide");
			//ym(66603799,'reachGoal','showphone');
			//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Search', 'eventAction' : 'ShowPhone'});
		});

		$('[data-action="show_form"]').on("click", function () {
			$(".object_book_send_mail").addClass("_hide");
			$(".send_restaurant_info").removeClass("_hide");
		});

		$('[data-action="show_mail_sent"]').on("click", function () {
			$(".send_restaurant_info").addClass("_hide");
			$(".object_book_mail_sent").removeClass("_hide");
		});

		$('[data-action="show_form_again"]').on("click", function () {
			$(".object_book_mail_sent").addClass("_hide");
			$(".send_restaurant_info").removeClass("_hide");
		});

		// $('[data-title-address]').on('click', function () {
		// 	let map_offset_top = $('.map').offset().top;
		// 	let map_height = $('.map').height();
		// 	let header_height = $('header').height();
		// 	let window_height = $(window).height();
		// 	let scroll_length = map_offset_top - header_height - ((window_height - header_height) / 2) + map_height / 2;
		// 	$('html,body').animate({ scrollTop: scroll_length }, 400);
		// });

		
		//map popup START 
		// const body = $('body');
		// const mapPopupBlock = $('._popup');

		// function openPopup() {
		// 	body.addClass('_overflow');
		// 	mapPopupBlock.addClass('_active');
		// }

		// function closePopup() {
		// 	body.removeClass('_overflow');
		// 	mapPopupBlock.removeClass('_active');
		// }

		// $('[data-title-address]').on('click', function () {
		// 	openPopup();
		// });

		// $('.map_popup_close').on('click', function () {
		// 	closePopup();
		// });

		// $(document).mouseup(function (e) { // событие клика по веб-документу
		// 	let mapPopupContainer = $('.map_popup_container'); // тут указываем ID элемента

		// 	if (mapPopupContainer.has(e.target).length === 0) { //если клик был не по дочерним элементам блока
		// 		closePopup();
		// 	}
		// });
		//map popup END

		$('[data-book-open]').on('click', function () {
			$(this).closest('.object_book_email').addClass('_form');
		})

		$('[data-book-email-reload]').on('click', function () {
			$(this).closest('.object_book_email').removeClass('_success');
			$(this).closest('.object_book_email').addClass('_form');
		})


		// var galleryThumbs = new Swiper('.gallery-thumbs', {
		// 	spaceBetween: 10,
		// 	slidesPerView: 5,
		// 	slidesPerColumn: 2,
		// 	freeMode: true,
		// 	watchSlidesVisibility: true,
		// 	watchSlidesProgress: true,

		// 	breakpoints: {
		// 		767: {
		// 			slidesPerView: 3,
		// 			slidesPerColumn: 1
		// 		}
		// 	}
		// });

		var galleryTop = new Swiper('.gallery-top', {
			slidesPerView: "auto",
			spaceBetween: 5,
			// loop: true,

			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			// thumbs: {
			// 	swiper: galleryThumbs
			// }
		});

		// $('.object_gallery._room').each((t, e) => {
		// 	let galleryRoomThumbs = new Swiper($(e).find('.gallery-thumbs-room'), {
		// 		//el: ".gallery-thumbs-room",
		// 		spaceBetween: 10,
		// 		slidesPerView: 5,
		// 		slidesPerColumn: 1,
		// 		freeMode: true,
		// 		watchSlidesVisibility: true,
		// 		watchSlidesProgress: true,

		// 		breakpoints: {
		// 			767: {
		// 				slidesPerView: 3,
		// 				slidesPerColumn: 1
		// 			}
		// 		}
		// 	});
		// 	let galleryRoomTop = new Swiper($(e).find('.gallery-top-room'), {
		// 		spaceBetween: 10,
		// 		thumbs: {
		// 			swiper: galleryRoomThumbs
		// 		}
		// 	});

		// 	this.sliders.push(galleryRoomThumbs);
		// 	this.sliders.push(galleryRoomTop)
		// });

		// console.log(this.sliders);

	}

}