<?php
namespace Zitkino\Presenters;

use Nette\Application\AbortException;
use Zitkino\Cinemas\{Cinema, CinemaFacade};
use Zitkino\Parsers\ParserService;

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
	public function actionDefault() {
		$this->redirect(":Home:default");
	}
	
	/**
	 * @throws AbortException
	 */
	public function actionParse() {
		$cinemas = $this->cinemaFacade->getParsable();
		
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			$this->parserService->initParser($cinema);
			
			$parser = $this->parserService->getParser();
		}
		
		$this->terminate();
	}
}
