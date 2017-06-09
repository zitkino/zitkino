<?php
namespace App\Presenters;

use Nette;

/**
 * Homepage presenter.
 */
class HomePresenter extends BasePresenter {
	public function beforeRender() {
		BasePresenter::beforeRender();
		
		$this->template->menuExists=false;
		//$this->template->menuURL='Home/menu.latte';
	}
	public function renderDefault() {
		$cinemas = new \Zitkino\Cinemas();
		$this->template->cinemas = $cinemas->getAllWithMovies();
	}
}
