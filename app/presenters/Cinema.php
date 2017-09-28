<?php
namespace App\presenters;

/**
 * Cinema presenter.
 */
class Cinema extends Base {
	private $cinemas;
	
	public function beforeRender() {
		Base::beforeRender();
		$this->cinemas = new \Zitkino\Cinemas();
		
		$this->template->menuExists = false;
		//$this->template->menuURL='Home/menu.latte';
	}
	
	public function renderProfile($id) {
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
	
	public function renderClassic_programme($id) {
		$this->template->cinemas = $this->cinemas->getWithMovies("classic");
	}
	public function renderMultiplex_programme($id) {
		$this->template->cinemas = $this->cinemas->getWithMovies("multiplex");
	}
	public function renderSummer_programme($id) {
		$this->template->cinemas = $this->cinemas->getWithMovies("summer");
	}
}
