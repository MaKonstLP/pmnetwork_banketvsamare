'use strict';

export default class Main {
	constructor() {
		let self = this;
		$('body').on('click', '[data-seo-control]', function () {
			$(this).closest('[data-seo-text]').addClass('_active');
		});
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
		//console.log("конструктор");


		//фиксация меню при прокрутке до низа хедера
		let header = $('header');
		let heightHeader = header.outerHeight(true);
		let headerTopContainer = $('.header_top_container');
		let heightheaderTopContainer = headerTopContainer.outerHeight(true);
		// let heightHeaderTop = $('.header_top').outerHeight(true);
		let titleBlock = $('.content_block[data-listing-title]');
		let heightTitleBlock = titleBlock.outerHeight(true);

		let stickyHeader = $('.header_top').clone().addClass('_sticky_header').css('display', 'none');
		headerTopContainer.append(stickyHeader);

		let stickyHeaderItem = $('[data-listing-title] .content_block_wrap').clone().addClass('_sticky_header').css('display', 'none');
		titleBlock.append(stickyHeaderItem);

		function checkScrollHeader(item) {
			if ($(window).scrollTop() > item) {
				if (header.hasClass("_item")) {
					// titleBlock.addClass('_active');
					// titleBlock.css('height', heightTitleBlock);

					stickyHeader.css('display', 'none');
					stickyHeaderItem.css('display', 'flex').fadeIn(300);
				} else {
					// header.addClass('_active');
					// headerTopContainer.css('height', heightheaderTopContainer);

					stickyHeader.css('display', 'flex').fadeIn(300);
					stickyHeaderItem.css('display', 'none');
				}

			} else {
				if (header.hasClass("_item")) {
					// titleBlock.removeClass('_active');
					// titleBlock.css('height', 'auto');

					stickyHeader.css('display', 'none');
					stickyHeaderItem.fadeOut(300);
				} else {
					// header.removeClass('_active');
					// headerTopContainer.css('height', 'auto');

					stickyHeader.fadeOut(300);
					stickyHeaderItem.css('display', 'none');
				}
			}
		}

		//проверка, что открыта главная страница
		if (header.hasClass("_index")) {
			checkScrollHeader(heightHeader);

			$(window).scroll(function () {
				checkScrollHeader(heightHeader);
			});
		} else {
			let heightTitleBlock = titleBlock.outerHeight(true);
			let offsetTopTitleBlock = titleBlock.offset().top;
			let offsetBottomTitleBlock = offsetTopTitleBlock + heightTitleBlock;

			checkScrollHeader(offsetBottomTitleBlock);
			// checkScrollHeader(heightHeader);

			$(window).scroll(function () {
				checkScrollHeader(offsetBottomTitleBlock);
				// checkScrollHeader(heightHeader);
			});
		}
	}

	init() {
		setTimeout(function() {
			(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
				m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
				(window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");
				ym(84074572, "init", {
				    clickmap:true,
				    trackLinks:true,
				    accurateTrackBounce:true,
				    webvisor:true
			});
		}, 100);

		$(".header_phone_button").on("click", this.helpWhithBookingButtonHandler);
		$(".footer_phone_button").on("click", this.helpWhithBookingButtonHandler);
		$(".header_form_popup").on("click", this.closePopUpHandler);
		$('.header_burger').on('click', this.burgerHandler);
		$(".header_city_select").on("click", this.citySelectHandler);
		$(document).mouseup(this.closeCitySelectHandler);
		//$(document).mouseup(this.closeBurgerHandler);

		$('.header_button_choose').on('click', this.helpWhithBookingButtonHandler);

		/* Настройка формы в окне popup */
		var $inputs = $(".header_form_popup .input_wrapper");

		// for (var input of $inputs) {
		// 	if ($(input).find("[name='email']").length !== 0
		// 		|| $(input).find("[name='question']").length !== 0) {
		// 		$(input).addClass("_hide");
		// 	}
		// }

		// $(".header_form_popup .form_title_main").text("Помочь с выбором зала?");
		// $(".header_form_popup .form_title_desc").addClass("_hide");

	}

	helpWhithBookingButtonHandler() {
		var $popup = $(".header_form_popup");
		var body = document.querySelector("body");
		if ($popup.hasClass("_hide")) {

			body.dataset.scrollY = self.pageYOffset;
			body.style.top = `-${body.dataset.scrollY}px`;

			$popup.removeClass("_hide");
			$(body).addClass("_modal_active");
			//ym(66603799,'reachGoal','headerlink')
		}
	}

	closePopUpHandler(e) {
		var $popupWrap = $(".header_form_popup");
		var $target = $(e.target);
		var $inputs = $(".header_form_popup input");
		var body = document.querySelector("body");

		if ($target.hasClass("close_button")
			|| $target.hasClass("header_form_popup")
			|| $target.hasClass("header_form_popup_message_close")) {
			$inputs.prop("value", "");
			$inputs.attr("value", "");
			$('.fc-day-number.fc-selected-date').removeClass('fc-selected-date')
			$popupWrap.addClass("_hide");
			$("body").removeClass("_modal_active");
			window.scrollTo(0, body.dataset.scrollY);
		}
	}

	burgerHandler(e) {
		if ($('header').hasClass('_active')) {
			$('header').removeClass('_active');
		}
		else {
			$('header').addClass('_active');
		}
	}

	closeBurgerHandler(e) {
		var $target = $(e.target);
		var $menu = $(".header_menu");

		if (!$menu.is($target)
			&& $menu.has($target).length === 0) {

			if ($('header').hasClass('_active')) {
				$('header').removeClass('_active');
			}
		}

	}

	citySelectHandler(e) {
		var $target = $(e.target);
		var $button = $(".header_city_select");
		var $cityList = $(".city_select_search_wrapper");

		if ($button.is($target)
			|| $button.has($target).length !== 0) {
			$cityList.toggleClass("_hide");
			$button.toggleClass("_active");
		}

	}

	closeCitySelectHandler(e) {
		var $target = $(e.target);
		var $button = $(".header_city_select");
		var $cityList = $(".city_select_search_wrapper");
		var $backButton = $(".back_to_header_menu");

		if (!$button.is($target)
			&& $button.has($target).length === 0
			&& !$cityList.is($target)
			&& $cityList.has($target).length === 0) {
			if (!$cityList.hasClass("_hide")) {
				$cityList.addClass("_hide");
				$button.removeClass("_active");
			}
		}

		if ($backButton.is($target)) {
			$cityList.addClass("_hide");
			$button.removeClass("_active");
		}
	}
}