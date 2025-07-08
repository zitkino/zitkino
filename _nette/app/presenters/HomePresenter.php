<?php
namespace _nette\app\presenters;

use _nette\app\presenters\BasePresenter;
use _nette\app\Models\cinemas\CinemaFacade;

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
