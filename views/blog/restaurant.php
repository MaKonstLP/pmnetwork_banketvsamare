<?php
use frontend\modules\banketvsamare\models\ElasticItems;

$elastic_model = new ElasticItems;
$item = $elastic_model::get($text_id);

if($item)
	echo $this->render('//components/generic/restaurant_adv_test.twig', ['item' => $item]);
?>