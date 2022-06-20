<?php
namespace Zitkino\Presenters;

use Zitkino\Cinemas\CinemaFacade;

/**
 * Cinema presenter.
 */
class CinemaPresenter extends BasePresenter {
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	public function renderDefault(): void {
		$this->template->classicCinemas = $this->cinemaFacade->getByType("classic");
		$this->template->multiplexCinemas = $this->cinemaFacade->getByType("multiplex");
		$this->template->summerCinemas = $this->cinemaFacade->getByType("summer");
	}
	
	/**
	 * @param int|string $id
	 */
	public function renderProfile($id): void {
		$cinema = $this->cinemaFacade->getById($id);
		$this->template->cinema = $cinema;
		
		$this->template->screenings = $cinema->getNewScreenings();
		
		$gmaps = $cinema->getGmaps();
		if(is_null($gmaps)) {
			$address = $cinema->getAddress().", ".$cinema->getCity();
			$param = urlencode($address);
		} else {
			$param = "place_id:".$gmaps;
		}
		$this->template->gmap = $param;
		
		$this->template->gmapKey = $this->getContainer()->getParameters()["google-maps-key"];
	}
	
	public function renderType(string $type): void {
		$this->template->cinemas = $this->cinemaFacade->getByType($type);
		$this->template->type = $type;
	}
	
	public function renderProgramme(string $type): void {
		$this->template->cinemas = $this->cinemaFacade->getWithMovies($type);
		$this->template->type = $type;
	}
}
