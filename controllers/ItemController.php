<?php
namespace app\modules\banketvsamare\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\elastic\RestaurantElastic;
use common\models\elastic\ItemsWidgetElastic;
use common\models\Seo;
use frontend\components\QueryFromSlice;
use frontend\modules\banketvsamare\models\ElasticItems;
use frontend\modules\banketvsamare\components\Breadcrumbs;
use frontend\modules\banketvsamare\components\RoomMicrodata;
use common\models\Slices;
use yii\helpers\ArrayHelper;
use common\models\Pages;

class ItemController extends Controller
{

	public  $rest_type_comparison = [
				2 => 1,
				1 => 2,
				25 => 3,
				3 => 4,
				36 => 5,
				28 => 6,
				13 => 7,
				4 => 8,
		    ];

	public function actionIndex($slug)
	{
		$elastic_model = new ElasticItems;
		$item = $elastic_model::find()->query([
			'bool' => [
				'must' => [
					['match' => ['restaurant_slug' => $slug]],
					['match' => ['restaurant_city_id' => \Yii::$app->params['subdomen_id']]],
				],
			]
		])->one();

		if(!$item)
			throw new \yii\web\NotFoundHttpException();

		Yii::$app->params['is_item'] = true;
		Yii::$app->params['item'] = $item;


		$seo = new Seo('item', 1, 0, $item, 'rest');
		$seo = $seo->seo;
        $this->setSeo($seo);

		if(isset($_SERVER['HTTP_REFERER'])){
			$slice_obj = new QueryFromSlice(basename($_SERVER['HTTP_REFERER']));
		}
		else{
			$slice_obj = (object)['flag' => false];
		}
        
		if($slice_obj->flag){
			$slice_alias = basename($_SERVER['HTTP_REFERER']);
		}
		else{
			$type = $item->restaurant_types[0]['id'];
			$types = [
				1 => 'restorany',
				2 => 'banketnye-zaly',
				3 => 'kafe',
				4 => 'bary',
				16 => 'kluby',
			];
			if(isset($types[$item->restaurant_types[0]['id']])){
				$slice_alias = $types[$item->restaurant_types[0]['id']];
			}
			else{
				$slice_alias = $types[1];
			}			
		}

		$seo['h1'] = $item->restaurant_name;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs(4, $slice_alias, ['id' => $item->id,'name' => $item->restaurant_name]);
		$seo['desc'] = $item->restaurant_name;
		$seo['address'] = $item->restaurant_address;

		$other_rooms = $item->rooms;

		$microdata = RoomMicrodata::getRoomMicrodata($item);

		$restaurantSpec = '';
		$restaurantMainSpec = '';


		foreach ($item->restaurant_types as $type){
			$restaurantSpec .= $type['name'] . ', ';
			if ($restaurantMainSpec === ''){
				$restaurantMainSpec = $type['name'];
			}
		}

		$restaurantSpec = substr($restaurantSpec, 0, -2);

		$construct_slices = $this->constructSlices($item);
		$rest_type_row = $this->constructTypes($construct_slices['types']);

		$urlPreviousPage = $_SERVER['HTTP_REFERER'] ?? '/';

		return $this->render('index.twig', compact('item', 'seo', 'other_rooms', 'microdata', 'restaurantSpec', 'restaurantMainSpec', 'construct_slices', 'rest_type_row', 'urlPreviousPage'));
	}

	private function setSeo($seo){
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}

	private function constructTypes($types){
		$rest_type_row = '';

		foreach ($types as $key => $type) {
			if(isset($type['link'])){
				$rest_type_row .= '<a href="/'.$type['link'].'/">'.$type['name'].'</a><span>,</span> ';
			}
			else{
				$rest_type_row .= $type['name'].'<span>,</span> ';
			}			
		}
		return $rest_type_row;
	}

	private function constructSlices($item){
		$rest_type_arr = [];
		foreach($item->restaurant_types as $restaurant_type){
			if(isset($this->rest_type_comparison[intval($restaurant_type['id'])]))
				$rest_type_arr[$restaurant_type['id']] = $this->rest_type_comparison[intval($restaurant_type['id'])];
		}

		$chelovek_arr = [];
		foreach($item->rooms as $room){
			switch(true) {
				case in_array($room['capacity'], range(0, 10)):
					$chelovek_arr[1] = 1;
				break;
				case in_array($room['capacity'], range(10,30)):
					$chelovek_arr[10] = 10;
				break;
				case in_array($room['capacity'], range(30,50)):
					$chelovek_arr[30] = 30;
				break;
				case in_array($room['capacity'], range(50,70)):
					$chelovek_arr[50] = 50;
				break;
				case in_array($room['capacity'], range(70,100)):
					$chelovek_arr[70] = 70;
				break;
				case $room['capacity'] > 100:
					$chelovek_arr[100] = 100;
				break;
			}
		}

		$types = [];
		foreach($item->restaurant_types as $key => $restaurant_type){
			$types[$restaurant_type['id']] = [];
			$types[$restaurant_type['id']]['name'] = $restaurant_type['name'];
			if(isset($this->rest_type_comparison[intval($restaurant_type['id'])])){
				$slice = Slices::find()
					->where(['params' => '{"rest_type":'.$this->rest_type_comparison[intval($restaurant_type['id'])].'}'])
					->one();
				if($slice)
					$types[$restaurant_type['id']]['link'] = $slice->alias;
			}
		}

		$slices_d_params = [];
		foreach ($rest_type_arr as $rest_type) {
			foreach ($chelovek_arr as $chelovek) {
				$slices_d_params[] = '{"rest_type":'.$rest_type.', "chelovek":'.$chelovek.'}';
			}
		}
		$slices = Slices::find()
			->select('alias')
			->where(['params' => $slices_d_params])
			->asArray()
			->all();
		$slices = ArrayHelper::getColumn($slices, 'alias');
		$pages = Pages::find()->where(['type' => $slices])->with('seoObject')->all();

		return ['pages' => $pages, 'types' => $types];
	}

}