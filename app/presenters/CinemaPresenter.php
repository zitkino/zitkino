<?php
namespace App\Presenters;
use Nette;

/**
 * Cinema presenter.
 */
class CinemaPresenter extends BasePresenter {
	public function beforeRender() {
		BasePresenter::beforeRender();
		
		$this->template->menuExists = false;
		//$this->template->menuURL='Home/menu.latte';
	}
	
	public function renderProfile($id)	{
		$cinema = new \Zitkino\Cinema($id);
		$data = $cinema->getData();
		$this->template->cinema = $data;
		
		$cinema->setMovies();
		$this->template->movies = $cinema->getMovies();
		
		$gmaps = $data["gmaps"];
		if(is_null($gmaps)) {
			$address = $data["address"].", ".$data["city"];
			$param = "/v1/place?q=".urlencode($address);
		} else { $param = "?pb=".$gmaps; }
		$this->template->gmap = $param;
	}
}
