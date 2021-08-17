<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use frontend\modules\banketvsamare\assets\AppAsset;
use frontend\modules\banketvsamare\models\ElasticItems;
use common\models\Subdomen;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<link rel="icon" type="image/png" href="/img/logo_icon.svg">
	<title><?php echo $this->title ?></title>
	<?php $this->head() ?>
	<?php if (!empty($this->params['desc'])) echo "<meta name='description' content='" . $this->params['desc'] . "'>"; ?>
	<?php if (!empty($this->params['kw'])) echo "<meta name='keywords' content='" . $this->params['kw'] . "'>"; ?>
	<?php if (isset($this->params['canonical']) and !empty($this->params['canonical'])) echo "<link rel='canonical' href='" . $this->params['canonical'] . "'>"; ?>
	<?php if (isset($this->params['robots']) && $this->params['robots'] === true) echo "<meta name='robots' content='noindex, nofollow'>"; ?>
	<?= Html::csrfMetaTags() ?>

	<?php if (isset(Yii::$app->params['item'])) : ?>
		<?php $item = Yii::$app->params['item']; ?>
		<script type="application/ld+json">
			{
				"@context": "http://schema.org",
				"@type": "Restaurant",
				"image": "<?= $item->restaurant_cover_url ?>",
				"@id": "<?= $item->restaurant_id ?>",
				"name": "<?= $item->restaurant_name ?>",
				"address": {
					"@type": "PostalAddress",
					"streetAddress": "<?= $item->restaurant_address ?>"
				},
				"price": "<?= $item->restaurant_price ?>",
				"servesCuisine": ["<?= $item->restaurant_cuisine ?>"],
				"geo": {
					"@type": "GeoCoordinates",
					"latitude": <?= $item->restaurant_latitude ?>,
					"longitude": <?= $item->restaurant_longitude ?>
				},
				"url": "http://<?php $url = $_SERVER['HTTP_HOST'] . '/' . explode('/',$_SERVER['REQUEST_URI'])[1]; echo $url; ?>",
				"telephone": "<?= $item->restaurant_phone ?>",
			}
		</script>
	<?php endif; ?>

</head>

<body>
	<!-- Google Tag Manager (noscript) 
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTTPDSK"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
 End Google Tag Manager (noscript) -->
	<?php $this->beginBody() ?>

	<div class="main_wrap">

		<header class="<?= isset(Yii::$app->params['is_index']) ? '_index' : '' ?><?= isset(Yii::$app->params['is_item']) ? '_item' : '' ?>" data-city-id="<?= Yii::$app->params['subdomen_id'] ?>">
			<div class="header_wrap">

				<div class="header_top_container">

					<div class="header_top">

						<a href="/" class="header_logo _logo"></a>

						<div class="header_phone _phone">
							<a href="tel:<?= Yii::$app->params['subdomen_phone'] ?>"><?= Yii::$app->params['subdomen_phone_pretty'] ?></a>
						</div>

						<div class="header_buttons">
							<div class="header_button header_button_choose">
								<span class="_link">Подобрать зал</span>
							</div>
							<div class="header_button header_button_favorite">
								<a href="/blog/" class="_link">Подборки</a>
							</div>
						</div>

					</div>

				</div>

				<div class="header_offer">
					<div class="header__title">
						<?php if(isset($this->params['h1'])):?>
						<h1><?=$this->params['h1']?></h1>
						<?php endif;?>
					</div>
					<p class="header__text">Бесплатная помощь банкетного менеджера</p>
				</div>
			</div>


			<div class="header_form_popup _hide">
				<div class="header_form_popup_content">

					<div class="form_wrapper callback">

						<div class="form_title">
							<p class="form_title_main">Подобрать зал</p>
						</div>

						<form class="form_block" action="/ajax/form/" data-type="<?= isset(Yii::$app->params['is_index']) ? 'main_popup' : '' ?><?= isset(Yii::$app->params['is_item']) ? 'item_popup' : '' ?>">

							<div class="form_inputs">

								<div class="input_wrapper">
									<input type="text" name="name" placeholder="Ваше имя" data-required />
								</div>
								<div class="input_wrapper">
									<input type="text" name="phone" placeholder="+7 (999) 999 - 99 - 99" data-required autocomplete="off" />
								</div>
								<div class="input_wrapper">
									<input type="text" name="amount" placeholder="Количество человек" data-required autocomplete="off" />
								</div>
								<div class="input_wrapper _textarea">
									<textarea name="question" placeholder="Напишите ваши пожелания"></textarea>
								</div>

							</div>

							<div class="form_submit_callback">
								<div class="form_policy checkbox_item _active" data-action="form_checkbox" data-form-privacy>
									<input type="checkbox" name="policy" class="personalData" checked="" data-required>
									<p class="checkbox_pseudo">Согласен с <a class="_link" href="/politika-konfidentsialnosti/" target="_blank">политикой конфиденциальности</a></p>
								</div>
								<button class="form_submit_button _button" type="submit">Отправить</button>
							</div>

						</form>
						<div class="form_success">
							<div class="header_form_popup_message_sent _hide" data-success>

								<h2>Мы получили вашу заявку!</h2>
								<p class="header_form_popup_message">В ближайшее время наш сотрудник свяжется с вами по телефону <span data-success-phone></span> для уточнения деталей.</p>
								<!-- <p class="header_form_popup_message_close _link" data-success-close>Понятно, закрыть</p> -->
								<div class="close_button"></div>

							</div>
						</div>
					</div>

				</div>
			</div>
		</header>

		<div class="content_wrap">
			<?= $content ?>
		</div>

		<footer>
			<div class="footer_container">
				<div class="footer_wrap">
					<div class="footer_row">

						<a href="/" class="footer_logo _logo"></a>
						<div class="footer_info">
							<p class="footer_copy">Банкет в Самаре © 2021</p>
							<a href="/politika-konfidentsialnosti/" class="footer_pc _link">Политика конфиденциальности</a>
						</div>

						<div class="footer_phone _phone">
							<a href="tel:<?= Yii::$app->params['subdomen_phone'] ?>"><?= Yii::$app->params['subdomen_phone_pretty'] ?></a>
						</div>

					</div>
				</div>
			</div>
		</footer>

	</div>

	<?php $this->endBody() ?>
	<!-- <link href="https://fonts.googleapis.com/css?family=Montserrat:400,600&amp;display=swap&amp;subset=cyrillic" rel="stylesheet"> -->

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;900&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

	<!-- <noscript><div><img src="https://mc.yandex.ru/watch/84074572" style="; left:-9999px;" alt="" /></div></noscript>
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-205074779-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-205074779-1');
		gtag('config', 'G-HEPKG2TRK0');
	</script> -->
</body>

</html>
<?php $this->endPage() ?>