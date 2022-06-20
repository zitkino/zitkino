<?php
namespace Zitkino\Presenters;

use Zitkino\Cinemas\CinemaFacade;

/**
 * Homepage presenter.
 */
class HomePresenter extends BasePresenter {
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	public function renderDefault(): void {
		$cinemas = $this->cinemaFacade->getWithMovies("current");
		$this->template->cinemas = $cinemas;
	}
}
