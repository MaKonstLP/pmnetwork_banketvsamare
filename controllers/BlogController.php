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
use frontend\modules\banketvsamare\widgets\PaginationWidget;

class BlogController extends Controller
{
	protected $per_page = 3;

  	public function actionIndex(){
  		$current_page = $_GET['page'] ?? 1;
  		$this->view->params['menu'] = 'blog';
	    $posts = BlogPost::findWithMedia()
	    	->with('blogPostTags')
	    	->where(['published' => true])
	    	->limit($this->per_page)
	    	->offset(($current_page - 1) * $this->per_page)
	    	->all();

		$seo = (new Seo('blog', $current_page))->seo;
		$seo['breadcrumbs'] = Breadcrumbs::get_breadcrumbs('blog');
		$this->setSeo($seo);

		$pagination = PaginationWidget::widget([
			'total' => ceil(BlogPost::find()->where(['published' => true])->count() / $this->per_page),
			'current' => $current_page,
		]);

		return $this->render('index.twig', compact('posts', 'seo', 'pagination'));
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