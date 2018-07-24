<?php
namespace App\presenters;

use Zitkino\Facades\CinemaFacade;

/**
 * Homepage presenter.
 */
class Home extends Base {
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	
	public function beforeRender() {
		Base::beforeRender();
		
		$this->template->menuExists=false;
		//$this->template->menuURL='Home/menu.latte';
	}
	
	public function renderDefault() {
		$cinemas = $this->cinemaFacade->getWithMovies("all");
		$this->template->cinemas = $cinemas;
	}
}
