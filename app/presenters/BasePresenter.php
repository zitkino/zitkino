<?php
namespace Zitkino\Presenters;

use Nette\Application\UI\Presenter;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {
	public function beforeRender() {
		$this->template->menuExists = false;
//		$this->template->menuURL = __DIR__.'/../templates/menu.latte';
	}
}
