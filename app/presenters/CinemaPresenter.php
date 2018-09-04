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
		$this->template->classicCinemas = $this->cinemaFacade->getByType("classic");
		$this->template->multiplexCinemas = $this->cinemaFacade->getByType("multiplex");
		$this->template->summerCinemas = $this->cinemaFacade->getByType("summer");
	}
	
	public function renderProfile($id) {
		$cinema = $this->cinemaFacade->getById($id);
		$this->template->cinema = $cinema;
		
		$cinema->setScreenings();
		$this->template->screenings = $cinema->getScreenings();
		
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
