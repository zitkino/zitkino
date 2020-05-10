<?php
namespace Zitkino\Presenters;

use Nette\Application\{AbortException, BadRequestException};
use Tracy\Debugger;

/**
 * Error presenter.
 */
class ErrorPresenter extends BasePresenter {
	/**
	 * @param \Exception
	 * @return void
	 * @throws AbortException
	 */
	public function renderDefault($exception) {
		if($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = true;
			$this->terminate();
		} else if($exception instanceof BadRequestException) {
			$code = $exception->getCode();
			
			// load template 403.latte or 404.latte or ... 4xx.latte
			$this->setView(in_array($code, [403, 404, 405, 410, 500]) ? $code : '4xx');
			
			// log to access.log
			Debugger::log("HTTP code $code: {$exception->getMessage()} in {$exception->getFile()}:{$exception->getLine()}", 'access');
		} else {
			$this->setView('500'); // load template 500.latte
			Debugger::log($exception, Debugger::ERROR); // and log exception
		}
	}
}
