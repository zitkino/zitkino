<?php
namespace App\presenters;

/**
 * Homepage presenter.
 */
class Home extends Base {
	public function beforeRender() {
		Base::beforeRender();
		
		$this->template->menuExists=false;
		//$this->template->menuURL='Home/menu.latte';
	}
	
	public function renderDefault() {
		$cinemas = new \Zitkino\Cinemas();
		$this->template->cinemas = $cinemas->getWithMovies();
	}
}
