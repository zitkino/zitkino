<?php
namespace App\presenters;

use Zitkino\Facades\CinemaFacade;

/**
 * Cinema presenter.
 */
class Cinema extends Base {
	private $cinemas;
	
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	public function beforeRender() {
		Base::beforeRender();
		
		$this->template->menuExists = false;
		//$this->template->menuURL='Home/menu.latte';
	}
	
	public function renderDefault() {
		$this->template->classicCinemas = $this->cinemaFacade->getByType("classic");
		$this->template->multiplexCinemas = $this->cinemaFacade->getByType("multiplex");
		$this->template->summerCinemas = $this->cinemaFacade->getByType("summer");
	}
	
	public function renderProfile($id) {
		$cinema = $this->cinemaFacade->getById($id);
		$this->template->cinema = $cinema;
		
		$cinema->setMovies();
		$this->template->movies = $cinema->getMovies();
		
		$gmaps = $cinema->getGmaps();
		if(is_null($gmaps)) {
			$address = $cinema->getAddress().", ".$cinema->getCity();
			$param = urlencode($address);
		} else { $param = "place_id:".$gmaps; }
		$this->template->gmap = $param;
	}
	
	public function renderClassic($id) {
		$this->template->cinemas = $this->cinemaFacade->getByType("classic");
	}
	public function renderClassic_programme($id) {
		$this->template->cinemas = $this->cinemaFacade->getWithMovies("classic");
	}
	
	public function renderMultiplex($id) {
		$this->template->cinemas = $this->cinemaFacade->getByType("multiplex");
	}
	public function renderMultiplex_programme($id) {
		$this->template->cinemas = $this->cinemaFacade->getWithMovies("multiplex");
	}
	
	public function renderSummer($id) {
		$this->template->cinemas = $this->cinemaFacade->getByType("summer");
	}
	public function renderSummer_programme($id) {
		$this->template->cinemas = $this->cinemaFacade->getWithMovies("summer");
	}
}
