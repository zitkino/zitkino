<?php
namespace Zitkino\Presenters;

use Zitkino\Cinemas\CinemaFacade;

/**
 * Cinema presenter.
 */
class CinemaPresenter extends BasePresenter {
	private $cinemas;
	
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	
	public function renderDefault() {
		$this->template->classicCinemas = $this->cinemaFacade->getByType("1");
		$this->template->multiplexCinemas = $this->cinemaFacade->getByType("2");
		$this->template->summerCinemas = $this->cinemaFacade->getByType("3");
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
		$this->template->cinemas = $this->cinemaFacade->getByType("1");
	}
	public function renderClassic_programme($id) {
		$this->template->cinemas = $this->cinemaFacade->getWithMovies("1");
	}
	
	public function renderMultiplex($id) {
		$this->template->cinemas = $this->cinemaFacade->getByType("2");
	}
	public function renderMultiplex_programme($id) {
		$this->template->cinemas = $this->cinemaFacade->getWithMovies("2");
	}
	
	public function renderSummer($id) {
		$this->template->cinemas = $this->cinemaFacade->getByType("3");
	}
	public function renderSummer_programme($id) {
		$this->template->cinemas = $this->cinemaFacade->getWithMovies("3");
	}
}
