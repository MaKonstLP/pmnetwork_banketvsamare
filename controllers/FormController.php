<?php
namespace app\modules\banketvsamare\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\helpers\Html;
use frontend\modules\banketvsamare\models\ElasticItems;
use common\components\GorkoLeadApi;

class FormController extends Controller
{
    public function beforeAction($action){
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSend()
    {
		//\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //return [
		//	'response' => 1,
		//	'info' => 1,
		//	'payload' => ['phone' => $_POST['phone']],
	  	//];
		$payload = [];

        if(isset($_POST['name']))
            $payload['name'] = $_POST['name'];
        if(isset($_POST['phone']))
            $payload['phone'] = $_POST['phone'];
        if(isset($_POST['amount']))
            $payload['guests'] = intval($_POST['amount']);
        
        if(isset($_POST['cityID'])){
            $payload['city_id'] = intval($_POST['cityID']);
        }
        else{
            return 1;
        }

        
        $payload['details'] = '';
        if(isset($_POST['question']))
            $payload['details'] .= $_POST['question'].' ';
        if(isset($_POST['url']))
            $payload['details'] .= 'Заявка отправлена с '.$_POST['url'];


        $resp = GorkoLeadApi::send_lead('v.gorko.ru', 'banketvsamare', $payload);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $resp;
    }
}
