<?php
namespace app\modules\banketvsamare\controllers;

use common\models\blog\BlogPost;
use common\models\blog\BlogTag;
use common\models\Seo;
use frontend\modules\banketvsamare\components\Breadcrumbs;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\widgets\LinkPager;
use yii\web\Controller;
use Yii;

class BlogController extends Controller
{

  	public function actionIndex(){
  		$this->view->params['menu'] = 'blog';
	    $query = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true]);
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 3,
				'forcePageParam' => false,
				'totalCount' => $query->count()
			],
		]);

		$seo = (new Seo('blog', $dataProvider->getPagination()->page + 1))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs('blog');
		$this->setSeo($seo);



		$topPosts = (clone $query)->where(['featured' => true])->limit(5)->all();

		$listConfig = [
			'dataProvider' => $dataProvider,
			'itemView' => '_list-item.twig',
			'layout' => "{items}\n<div class='pagination_wrapper items_pagination' data-pagination-wrapper>{pager}</div>",
			'pager' => [
				'class' => LinkPager::class,
				'disableCurrentPageButton' => true,
				'nextPageLabel' => 'Следующая →',
				'prevPageLabel' => '← Предыдущая',
				'maxButtonCount' => 4,
				'activePageCssClass' => '_active',
				'pageCssClass' => 'items_pagination_item',
			],

		];
		return $this->render('index.twig', compact('listConfig', 'topPosts', 'seo'));
  	}

  	public function actionPost($alias)
	{
		$this->view->params['menu'] = 'blog';
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'alias' => $alias])->one();
		if(empty($post)) {
			throw new \yii\web\NotFoundHttpException();
		}
		$seo = ArrayHelper::toArray($post->seoObject);
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs('post', ['link' => $alias, 'name' => $post->name]);

		$this->setSeo($seo);
		$similarPosts = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true])->andWhere(['!=', 'id', $post->id])->orderBy(['published_at' => SORT_DESC])->limit(2)->all();
		return $this->render('post.twig', compact('post','seo', 'similarPosts'));
	}

	public function actionPreview($id)
	{
		$post = BlogPost::findWithMedia()->with('blogPostTags')->where(['published' => true, 'id' => $id])->one();
		if(empty($post)) {
			throw new NotFoundHttpException();
		}
		$seo = ArrayHelper::toArray($post->seoObject);
		$this->setSeo($seo);
		return $this->render('post.twig', compact('post'));
	}

  	private function setSeo($seo)
	{
		$this->view->title = $seo['title'];
		$this->view->params['desc'] = $seo['description'];
		$this->view->params['kw'] = $seo['keywords'];
	}
}