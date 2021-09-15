<?php

namespace app\modules\banketvsamare\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\widgets\FilterWidget;
use frontend\widgets\PaginationWidget;
use frontend\components\ParamsFromQuery;
use frontend\components\QueryFromSlice;
use frontend\components\Declension;
use frontend\components\RoomsFilter;
use frontend\modules\banketvsamare\components\Breadcrumbs;
use frontend\modules\banketvsamare\widgets\LoadmoreWidget;
use frontend\modules\banketvsamare\components\FilterOneParamSeoGenerator;
use frontend\modules\banketvsamare\models\ElasticItems;
use common\models\Pages;
use common\models\Filter;
use common\models\Slices;
use common\models\GorkoApi;
use common\models\elastic\ItemsFilterElastic;
use common\models\Seo;
use frontend\modules\pmnbd\models\MediaEnum;


class ListingController extends Controller
{
	protected $per_page = 10;

	public $filter_model,
		$slices_model,
		$paramList;

	public function beforeAction($action)
	{
		Pages::createSiteObjects();
		$this->filter_model = Filter::find()->with('items')->where(['active' => 1])->orderBy(['sort' => SORT_ASC])->all();
		$this->slices_model = Slices::find()->all();
		$this->paramList = [
			'rest_type' => '',
			'chelovek' => '',
			'price' => '',
			'svoy-alko' => '',
			'firework' => ''
		];

		return parent::beforeAction($action);
	}

	public function actionSlice($slice)
	{

		//$pages = Pages::find()->where(['id' => [87,88,89,90,91,92]])->with('seoObject')->all();
		//$pages->seoObject->title = 'test1234';
		//echo '<pre>';
		//print_r($pages->seoObject);
		//$pages->seoObject->save();
		//exit;



		// foreach ($pages as $key => $page) {

		// 	$page->seoObject->title = 'Бар на ' . preg_replace('/[^0-9]/', '', $page->name) . ' человек — Аренда в Самаре';
		// 	// $page->seoObject->title = 'Бар на более 100 человек — Аренда в Самаре';
		// 	$page->seoObject->description = 'Найти бар на ' . preg_replace('/[^0-9]/', '', $page->name) . ' человек для мероприятия в Самаре. Все заведения города, низкие цены. Подбор бесплатно.';
		// 	// $page->seoObject->description = 'Найти бар на более 100 человек для мероприятия в Самаре. Все заведения города, низкие цены. Подбор бесплатно.';
		// 	$page->seoObject->save();

		// 	// echo '<pre>';
		// 	// print_r($page->seoObject->title);
		// 	// print_r($page->seoObject->description);
		// }
		// exit;

		$slice_obj = new QueryFromSlice($slice);

		if (count(array_intersect_key($this->paramList, $_GET)) > 0) {
			return $this->actionSliceWhithParams($slice_obj->params);
		}

		if ($slice_obj->flag) {
			$this->view->params['menu'] = $slice;
			$params = $this->parseGetQuery($slice_obj->params, Filter::find()->with('items')->orderBy(['sort' => SORT_ASC])->all(), $this->slices_model);
			isset($_GET['page']) ? $params['page'] = $_GET['page'] : $params['page'];

			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			//if ($params['page'] > 1) {
			//	$canonical .= $params['canonical'];
			//}

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(3, $slice),
				$canonical 		= 	$canonical,
				$type 			=	$slice
			);
		} else {
			throw new \yii\web\NotFoundHttpException();
		}
	}

	public function actionSliceWhithParams($paramFromAlias)
	{
		$getQuery = $_GET;
		unset($getQuery['q']);
		$params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
		$params['params_filter']['rest_type'] = [];
		array_push($params['params_filter']['rest_type'], $paramFromAlias['rest_type']);
		$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

		if ($params['page'] > 1) {
			$canonical .= $params['canonical'];
		}

		return $this->actionListing(
			$page 			=	$params['page'],
			$per_page		=	$this->per_page,
			$params_filter	= 	$params['params_filter'],
			$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(2),
			$canonical 		= 	$canonical,
			$type = false,
			$sliceWithParams = true,
		);
	}

	public function actionIndex()
	{



		// $slice_new = new Slices();
		// $slice_new->alias = 'test';
		// $slice_new->params = 'test';
		// $slice_new->save();



		function transliterate($textcyr = null, $textlat = null)
		{
			$cyr = array(
				'ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я', 'ы',
				'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я', 'Ы'
			);
			$lat = array(
				'zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q', 'y',
				'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q', 'Y'
			);
			if ($textcyr) return str_replace($cyr, $lat, $textcyr);
			else if ($textlat) return str_replace($lat, $cyr, $textlat);
			else return null;
		}


		// echo "<pre>";
		// //вывод всех типов ресторанов и количества человек
		// foreach ($this->filter_model as $filter) {
		// 	foreach ($filter->items as $filter_item) {
		// 		echo '{"' . $filter->alias . '":' . $filter_item->value . '} - ' . $filter_item->text . '</br>';

		// 		// вывод типов и количества
		// 		echo '{"' . $filter->alias . '":' . $filter_item->value . '}</br>';

		// 		// вывод алиасов (transliterate - переводит транслитом на латиницу, mb_strtolower - выводит все в нижнем регистре, str_ireplace - заменяет "chel." на "chelovek")
		// 		echo str_ireplace(" ", "-", str_ireplace("chel.", "chelovek", mb_strtolower(transliterate(transliterate($filter_item->text), null)))) . '</br>';



		// 		// $slice_new = new Slices();
		// 		// $slice_new->alias = str_ireplace(" ", "-", str_ireplace("chel.", "chelovek", mb_strtolower(transliterate(transliterate($filter_item->text), null))));
		// 		// $slice_new->params = '{"' . $filter->alias . '":' . $filter_item->value . '}';
		// 		// $slice_new->save();
		// 	}
		// }

		// //вывод для каждого типа ресторана с каждым типом количества человек
		// foreach ($this->filter_model[0]->items as $filter_item0) {
		// 	foreach ($this->filter_model[1]->items as $filter_item1) {

		// 		//вывод параметров каждого типа ресторана с каждым количеством человек
		// 		echo '{"' . $this->filter_model[0]->alias . '":' . $filter_item0->value . ', "' . $this->filter_model[1]->alias . '":' . $filter_item1->value . '}</br>';

		// 		//вывод алиасов каждого типа ресторана с каждым количеством человек (transliterate - переводит транслитом на латиницу, mb_strtolower - выводит все в нижнем регистре, str_ireplace - заменяет " " на "-")
		// 		echo str_ireplace(" ", "-", mb_strtolower(transliterate(transliterate($filter_item0->text), null))) . '-na-' . $filter_item1->value . '-' . $this->filter_model[1]->alias . '</br>';


		// 		// $slice_new = new Slices();
		// 		// $slice_new->alias = str_ireplace(" ", "-", mb_strtolower(transliterate(transliterate($filter_item0->text), null))) . '-na-' . $filter_item1->value . '-' . $this->filter_model[1]->alias;
		// 		// $slice_new->params = '{"' . $this->filter_model[0]->alias . '":' . $filter_item0->value . ', "' . $this->filter_model[1]->alias . '":' . $filter_item1->value . '}';
		// 		// $slice_new->save();
		// 	}
		// }
		// exit;


		// Pages::createSiteObjects();
		$getQuery = $_GET;
		unset($getQuery['q']);
		if (count($getQuery) > 0) {
			$params = $this->parseGetQuery($getQuery, $this->filter_model, $this->slices_model);
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
			if ($params['page'] > 1) {
				$canonical .= $params['canonical'];
			}

			return $this->actionListing(
				$page 			=	$params['page'],
				$per_page		=	$this->per_page,
				$params_filter	= 	$params['params_filter'],
				$breadcrumbs 	=	Breadcrumbs::get_breadcrumbs(2),
				$canonical 		= 	$canonical
			);
		} else {
			$canonical = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];

			return $this->actionListing(
				$page 			=	1,
				$per_page		=	$this->per_page,
				$params_filter	= 	[],
				$breadcrumbs 	= 	Breadcrumbs::get_breadcrumbs(2),
				$canonical 		= 	$canonical
			);
		}
	}

	public function actionListing($page, $per_page, $params_filter, $breadcrumbs, $canonical, $type = false, $sliceWithParams = false)
	{
		Yii::$app->params['is_index'] = true;
		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params_filter, $per_page, $page, false, 'restaurants', $elastic_model);

		if(!count($items->items))
			throw new \yii\web\NotFoundHttpException();

		//echo "<pre>";
		//print_r($items);
		//exit;

		// echo "<pre>";
		// print_r($this->slices_model);
		// exit;

		if ($page > 1) {
			$seo['text_top'] = '';
			$seo['text_bottom'] = '';
		}

		$itemsAllPages = new ItemsFilterElastic($params_filter, $this->per_page, $page, false, 'restaurants', $elastic_model);

		$minPrice = 999999;
		foreach ($itemsAllPages->items as $item) {
			if ($item->restaurant_price < $minPrice && $item->restaurant_price !== 250) { // второе условие - костыль под опечатку в инфе из горько
				$minPrice = $item->restaurant_price;
			}
		}

		if ($minPrice === 999999) {
			$minPrice = 2100;
		}

		$filter = FilterWidget::widget([
			'filter_active' => $params_filter,
			'filter_model' => $this->filter_model,
			'minPrice' => $minPrice
		]);

		// $pagination = PaginationWidget::widget([
		// 	'total' => $items->pages,
		// 	'current' => $page,
		// ]);


		$pagination = LoadmoreWidget::widget([
			'total' => $items->total,
			'current_page' => $page,
			'current' => $page * $per_page,
			'per_page' => $per_page,
		]);



		$seo_type = $type ? $type : 'listing';
		$seo = $this->getSeo($seo_type, $page, $items->total);
		$seo['breadcrumbs'] = $breadcrumbs;

		if ($sliceWithParams || count(array_intersect_key($this->paramList, $_GET)) > 0) {
			$seo['robots'] = true;
		}

		$this->setSeo($seo, $page, $canonical);

		if ($seo_type == 'listing' and count($params_filter) > 0) {
			$seo['text_top'] = '';
			$seo['text_bottom'] = '';
		}

		$main_flag = ($seo_type == 'listing' and count($params_filter) == 0);

		$currentRestType = $this->getRestTypeDeclention($params_filter);

		// echo '<pre>';
		// print_r($seo);
		// exit;

		return $this->render('index.twig', array(
			'items' => $items->items,
			'filter' => $filter,
			'pagination' => $pagination,
			'seo' => $seo,
			'currentType' => Declension::get_num_ending($items->total, $currentRestType),
			'count' => $items->total,
			'menu' => $type,
			'main_flag' => $main_flag,
			'filterMinPrice' => isset($params_filter['price']) ? array_shift($params_filter['price']) : 0,
		));
	}

	public function actionAjaxFilter()
	{
		$params = $this->parseGetQuery(json_decode($_GET['filter'], true), $this->filter_model, $this->slices_model);

		$elastic_model = new ElasticItems;
		$items = new ItemsFilterElastic($params['params_filter'], $this->per_page, $params['page'], false, 'restaurants', $elastic_model);

		// $pagination = PaginationWidget::widget([
		// 	'total' => $items->pages,
		// 	'current' => $params['page'],
		// ]);

		$pagination = LoadmoreWidget::widget([
			'total' => $items->total,
			'current_page' => $params['page'],
			'current' => $params['page'] * $this->per_page,
			'per_page' => $this->per_page,
		]);

		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		!$slice_url ? $breadcrumbs = Breadcrumbs::get_breadcrumbs(2) : $breadcrumbs = Breadcrumbs::get_breadcrumbs(3, $slice_url);

		$seo_type = $slice_url ? $slice_url : 'listing';
		$seo = $this->getSeo($seo_type, $params['page'], $items->total);

		$seo['breadcrumbs'] = $breadcrumbs;

		$currentRestType = $this->getRestTypeDeclention($params['params_filter']);

		$title = $this->renderPartial('//components/generic/title.twig', array(
			'seo' => $seo,
			'count' => $items->total,
			'currentType' => Declension::get_num_ending($items->total, $currentRestType),
		));

		if ($params['page'] == 1) {
			$text_top = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_top']));
			$text_bottom = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_bottom']));
			$text_1 = $this->renderPartial('//components/generic/text.twig', array('text' => $seo['text_1']));
		} else {
			$text_top = '';
			$text_bottom = '';
			$text_1 = '';
		}

		if ($seo_type == 'listing' and count($params['params_filter']) > 0) {
			$text_top = '';
			$text_bottom = '';
			$text_1 = '';
		}

		$minPrice = 999999;
		foreach ($items->items as $item) {
			if ($item->restaurant_price < $minPrice) {
				$minPrice = $item->restaurant_price;
			}
		}

		return  json_encode([
			'listing' => $this->renderPartial('//components/generic/listing.twig', array(
				'items' => $items->items,
				'img_alt' => $seo['img_alt'],
				'filterMinPrice' => isset($params['params_filter']['price']) ? $params['params_filter']['price'][0] : 0,
			)),
			'pagination' => $pagination,
			'url' => !$slice_url ? $this->getUrl($params['listing_url'], $params['params_filter']) : $params['listing_url'],
			'title' => $title,
			'text_top' => $text_top,
			'text_bottom' => $text_bottom,
			'seo_title' => $seo['title'],
			'minPrice' => $minPrice,
			'params_filter' => $params['params_filter'],
			'seo' => $seo,
			'total' => $items->total,
			'text_1' => $text_1,
		]);
	}

	public function actionAjaxFilterSlice()
	{
		$slice_url = ParamsFromQuery::isSlice(json_decode($_GET['filter'], true));

		return $slice_url;
	}

	private function parseGetQuery($getQuery, $filter_model, $slices_model)
	{
		$return = [];
		if (isset($getQuery['page'])) {
			$return['page'] = $getQuery['page'];
		} else {
			$return['page'] = 1;
		}

		$temp_params = new ParamsFromQuery($getQuery, $filter_model, $this->slices_model);

		$return['params_api'] = $temp_params->params_api;
		$return['params_filter'] = $temp_params->params_filter;
		$return['listing_url'] = $temp_params->listing_url;
		$return['canonical'] = $temp_params->canonical;
		return $return;
	}

	private function getSeo($type, $page, $count = 0)
	{
		$seo = (new Seo($type, $page, $count))->withMedia([MediaEnum::ADVANTAGES]);

		return $seo->seo;
	}

	private function setSeo($seo, $page, $canonical)
	{

		if ($page != 1) {
			$this->view->params['canonical'] = $canonical;
		}

		if (isset($seo['title'])) {
			$this->view->title = $seo['title'];
		}

		if (isset($seo['description'])) {
			$this->view->params['desc'] = $seo['description'];
		}

		if (isset($seo['keywords'])) {
			$this->view->params['kw'] = $seo['keywords'];
		}

		if (isset($seo['robots'])) {
			$this->view->params['robots'] = $seo['robots'];
		}

		if (isset($seo['h1'])) {
			$this->view->params['h1'] = $seo['h1'];
		} else {
			$this->view->params['h1'] = 'ВСЕ БАНКЕТНЫЕ ПЛОЩАДКИ САМАРЫ';
		}
	}

	private function getRestTypeDeclention($params_filter = [])
	{
		$restTypesList = [
			'1' => ['банкетный зал', 'банкетных зала', 'банкетных залов'],
			'2' => ['ресторан', 'ресторана', 'ресторанов'],
			'3' => ['гостиница', 'гостиницы', 'гостиниц'],
			'4' => ['кафе', 'кафе', 'кафе'],
			'5' => ['лофт', 'лофта', 'лофтов'],
			'6' => ['антикафе', 'антикафе', 'антикафе'],
			'7' => ['база отдыха', 'базы отдыха', 'баз отдыха'],
			'8' => ['бар', 'бара', 'баров'],
			// '4' => ['клуб', 'клуба', 'клубов'],
			// '6' => ['площадка в городе', 'площадки в городе', 'площадок в городе'],
			// '7' => ['площадка на природе', 'площадки на природе', 'площадок на природе'],
		];

		$currentRestType = ['площадка', 'площадки', 'площадок'];

		if (isset($params_filter['rest_type']) && count($params_filter['rest_type']) === 1) {
			$currentRestType = $restTypesList[$params_filter['rest_type'][0]];
		}

		return $currentRestType;
	}

	private function getUrl($listing_url, $params_filter)
	{
		if (isset($params_filter['rest_type']) && count($params_filter['rest_type']) === 1) {
			$currentSlice = Slices::find()->where(['params' => '{"rest_type":' . $params_filter['rest_type'][0] . '}'])->one();
			$newUrl = $currentSlice->alias . '/' . str_replace(('rest_type=' . $params_filter['rest_type'][0] . '&'), '', $listing_url);
			return $newUrl;
		}

		return $listing_url;
	}
}

//class ListingController extends Controller
//{
//	public function actionIndex(){
//		GorkoApi::renewAllData([
//			'city_id=4400&type_id=1&event=15',
//			'city_id=4400&type_id=1&event=17'
//		]);
//		return 1;
//	}	
//}