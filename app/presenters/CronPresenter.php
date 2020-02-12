<?php
namespace Zitkino\Presenters;

use Nette\Application\AbortException;
use Tracy\Debugger;
use Zitkino\Cinemas\Cinema;
use Zitkino\Cinemas\CinemaFacade;
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
	public function actionParse() {
		$cinemas = $this->cinemaFacade->getAll();
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			$this->parserService->initParser($cinema);
			
			$parser = $this->parserService->getParser();
			Debugger::barDump($parser);
		}
		
		$this->terminate();
	}
}
