<?php
namespace Zitkino\Presenters;

use Zitkino\Cinemas\CinemaFacade;

/**
 * Homepage presenter.
 */
class HomePresenter extends BasePresenter {
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	
	public function renderDefault() {
		$cinemas = $this->cinemaFacade->getWithMovies("all");
		$this->template->cinemas = $cinemas;
	}
}
