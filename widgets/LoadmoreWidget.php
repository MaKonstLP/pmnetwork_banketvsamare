<?php

namespace frontend\modules\banketvsamare\widgets;

use Yii;
use yii\bootstrap\Widget;

class LoadmoreWidget extends Widget
{

	public $total;
	public $current;
	public $current_page;
	public $per_page;


	public function run()
	{
		$buttons = '<div class="items_pagination">';
		if ($this->total > 1) {

			if ($this->current < $this->total) {
				if(($this->total - $this->current) < $this->per_page) {
					$buttons .= $this->renderPageButton($this->current_page + 1, ($this->total - $this->current), $this->current);
				} else {
					$buttons .= $this->renderPageButton($this->current_page + 1, $this->per_page, $this->current);
				}
			}
			$buttons .= '</div>';
			return $buttons;
		} else {
			return '';
		}
	}

	private function renderPageButton($page, $amount, $test)
	{
		return '<a href="?page='.$page.'" class="button_loadmore" data-page-id="' . $page . '" data-listing-pagitem="' . $test . '">Показать ещё ' . $amount . '</a>';
	}

}
