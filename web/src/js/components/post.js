'use strict';
// import Swiper from 'swiper';
import Swiper from 'swiper/bundle';

import YaMapAll from './map';

export default class Post {
	constructor($block) {
		self = this;
		this.block = $block;
		this.swipers_gal = [];
		this.swipers_rest = [];

		this.yaMap = new YaMapAll();

		$('[data-action="show_phone"]').on("click", function () {
			let $object_book = $(this).closest(".object_book");
			$object_book.addClass("_active");
			$object_book.find(".object_book_hidden").addClass("_active");
			$object_book.find(".object_book_interactive_part").removeClass("_hide");
			$object_book.find(".object_book_send_mail").removeClass("_hide");
			//ym(66603799,'reachGoal','showphone');
			//dataLayer.push({'event': 'event-to-ga', 'eventCategory' : 'Search', 'eventAction' : 'ShowPhone'});
		});

		$('[data-action="show_form"]').on("click", function () {
			$(".object_book_send_mail").addClass("_hide");
			$(".send_restaurant_info").removeClass("_hide");
		});

		$('[data-action="show_mail_sent"]').on("click", function () {
			let $object_book = $(this).closest(".object_book");
			$object_book.find(".send_restaurant_info").addClass("_hide");
			$object_book.find(".object_book_mail_sent").removeClass("_hide");
		});

		$('[data-action="show_form_again"]').on("click", function () {
			let $object_book = $(this).closest(".object_book");
			$object_book.find(".object_book_mail_sent").addClass("_hide");
			$object_book.find(".send_restaurant_info").removeClass("_hide");
		});

		$('[data-book-open]').on('click', function () {
			$(this).closest('.object_book_email').addClass('_form');
		})

		$('[data-book-email-reload]').on('click', function () {
			$(this).closest('.object_book_email').removeClass('_success');
			$(this).closest('.object_book_email').addClass('_form');
		})

		// $('.post_gallery_wrap').each(function (iter, object) {
		// 	// console.log(new Swiper('.post-gallery-top'));
		// 	let postGalleryThumbs = new Swiper($(this).find('.post-gallery-thumbs'), {
		// 		spaceBetween: 10,
		// 		slidesPerView: 'auto',
		// 		// slidesPerColumn: 1,
		// 		freeMode: true,
		// 		watchSlidesVisibility: true,
		// 		watchSlidesProgress: true,

		// 		// breakpoints: {
		// 		// // 	1440: {
		// 		// // 		slidesPerView: 5,
		// 		// // 	},

		// 		// 	767: {
		// 		// 		spaceBetween: 3,
		// 		// 	}
		// 		// }
		// 	});
		// 	let postGalleryTop = new Swiper($(this).find('.post-gallery-top'), {
		// 		spaceBetween: 0,
		// 		thumbs: {
		// 			swiper: postGalleryThumbs
		// 		}
		// 	});

		// 	self.swipers_gal.push({
		// 		postGalleryThumbs,
		// 		postGalleryTop
		// 	});
		// });

		let postGalleryThumbs = new Swiper('.post-gallery-thumbs', {
			spaceBetween: 3,
			slidesPerView: 'auto',
			// slidesPerColumn: 1,
			freeMode: true,
			watchSlidesVisibility: true,
			watchSlidesProgress: true,

			breakpoints: {
				// when window width is >= 768px
				768: {
					spaceBetween: 10
				}
			}
		});
		
		let postGalleryTop = new Swiper('.post-gallery-top', {
			spaceBetween: 0,
			thumbs: {
				swiper: postGalleryThumbs
			}
		});


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




		// $('.post_item_gallery_wrap').each(function (iter, object) {
		// 	let postGalleryThumbs = new Swiper($(this).find('.post-item-gallery-thumbs'), {
		// 		spaceBetween: 5,
		// 		slidesPerView: 4,
		// 		slidesPerColumn: 1,
		// 		freeMode: true,
		// 		watchSlidesVisibility: true,
		// 		watchSlidesProgress: true,

		// 		breakpoints: {
		// 			1440: {
		// 				slidesPerView: 3,
		// 			},

		// 			767: {
		// 				slidesPerView: 2,
		// 			}
		// 		}
		// 	});
		// 	let postGalleryTop = new Swiper($(this).find('.post-item-gallery-top'), {
		// 		spaceBetween: 0,
		// 		thumbs: {
		// 			swiper: postGalleryThumbs
		// 		}
		// 	});

		// 	self.swipers_rest.push({
		// 		postGalleryThumbs,
		// 		postGalleryTop
		// 	});
		// });



		//mapPopup START
		$('[data-title-address]').on('click', function (e) {

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
}