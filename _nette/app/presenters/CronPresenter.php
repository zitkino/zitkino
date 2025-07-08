<?php
namespace _nette\app\presenters;

use _nette\app\Models\cinemas\Cinema;
use _nette\app\presenters\BasePresenter;
use Nette\Application\AbortException;
use _nette\app\Models\cinemas\{CinemaFacade};
use _nette\app\parsers\ParserService;

/**
 * Cron presenter.
 */
class CronPresenter extends BasePresenter {
	/** @var CinemaFacade @inject */
	public $cinemaFacade;
	
	/** @var ParserService @inject */
	public $parserService;
	
	/**
	 * @throws AbortException
	 */
	public function actionDefault(): void {
		$this->redirect(":Home:default");
	}
	
	/**
	 * @throws AbortException
	 */
	public function actionParse(): void {
		$cinemas = $this->cinemaFacade->getParsable();
		
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			$this->parserService->initParser($cinema);
			
			$parser = $this->parserService->getParser();
		}
		
		$this->terminate();
	}
}
