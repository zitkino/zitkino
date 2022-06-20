<?php
namespace Zitkino\Presenters;

use App\Services\MetaService;
use App\Templates\BaseTemplate;
use Contributte\Translation\LocalesResolvers\Session;
use Contributte\Translation\Translator;
use Nette\Application\AbortException;
use Nette\Application\UI\Presenter;
use Nette\DI\Container;

/**
 * Base presenter for all application presenters.
 * @property-read BaseTemplate $template
 */
abstract class BasePresenter extends Presenter {
	/** @var Translator @inject */
	public $translator;
	
	/** @var Session @inject */
	public $translatorSessionResolver;
	
	/** @var string|null */
	public $locale = null;
	
	/** @var MetaService @inject */
	public $metaService;
	
	/** @var Container @inject */
	public $container;
	
	public function getContainer(): Container {
		return $this->container;
	}
	
	public function startup() {
		parent::startup();
		
		$this->template->meta = $this->metaService;
		
		$this->template->locale = $this->translator->getLocale();
	}
	
	/**
	 * @throws AbortException
	 */
	public function handleChangeLocale(string $locale): void {
		$this->translatorSessionResolver->setLocale($locale);
		$this->translator->setLocale($locale);
		$this->redirect("this");
	}
}
