<?php
namespace Zitkino\Presenters;

use Tracy\Debugger;
use Zitkino\Cinemas\Cinema;
use Zitkino\Cinemas\CinemaFacade;

/**
 * Cron presenter.
 */
class CronPresenter extends BasePresenter {
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	public function actionDefault() {
		/** @var Cinema $cinema */
		$cinema = $this->cinemaFacade->getById("art");
		$cinema->setScreenings();
		Debugger::barDump($cinema);
		$id = $this->cinemaFacade->update($cinema);
		Debugger::barDump($id);
		
		foreach($cinema->getScreenings() as $screening) {
			$this->cinemaFacade->save($screening);
		}
		Debugger::barDump($cinema);
		
		$this->terminate();
	}
}
