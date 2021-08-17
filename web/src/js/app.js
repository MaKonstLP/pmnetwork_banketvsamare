import $ from 'jquery';

import Listing from './components/listing';
import Item from './components/item';
import Main from './components/main';
import Form from './components/form';
import YaMapSingleObject from './components/mapSingleObject';
import WidgetMain from './components/widgetMain';
import Breadcrumbs from './components/breadcrumbs';
import Post from './components/post';

window.$ = $;

(function ($) {
	$(function () {

		if ($('[data-page-type="listing"]').length > 0) {
			var listing = new Listing($('[data-page-type="listing"]'));
		}

		if ($('[data-page-type="item"]').length > 0) {
			var item = new Item($('[data-page-type="item"]'));
		}

		if ($('.map').length > 0) {
			if ($('[data-page-type="item"]').length > 0) {
				var yaMap = new YaMapSingleObject();
			}
		}

		if ($('[data-page-type="post"]').length > 0) {
			var post = new Post($('[data-page-type="post"]'));
		}

		var main = new Main();
		var form = [];

		$('form').each(function () {
			form.push(new Form($(this)))
		});

	});
})($);

function mapInit() {
	console.log(1);
}