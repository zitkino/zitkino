<?php
namespace Zitkino\Presenters;

use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {
	public function startup() {
		parent::startup();
		
		$this->template->meta = $this->presenter->context->getParameters()["meta"];
	}
}
