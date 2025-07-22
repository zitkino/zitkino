<?php

namespace App\Controllers;

use App\Services\MetaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{RequestStack, Response};
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Base controller for all application controllers.
 */
abstract class BaseController extends AbstractController {
	protected TranslatorInterface $translator;
	
	protected RequestStack $requestStack;
	
	protected MetaService $metaService;
	
	public function __construct(TranslatorInterface $translator, RequestStack $requestStack, MetaService $metaService) {
		$this->translator = $translator;
		$this->requestStack = $requestStack;
		$this->metaService = $metaService;
	}
	
	protected function render(string $view, array $parameters = [], Response $response = null): Response {
		// Add global variables for all templates rendered via this controller
		$parameters['app_locale'] = $this->getLocale();
		$parameters['meta'] = $this->metaService;
		
		// You can also add parameters from your config/services.yaml like this:
		// $parameters['app_name'] = $this->getParameter('app.name');
		
		return parent::render($view, $parameters, $response);
	}
	
	/**
	 * Get the current locale
	 */
	protected function getLocale(): string {
		return $this->translator->getLocale();
	}
	
	/**
	 * Change the current locale
	 */
	protected function changeLocale(string $locale): void {
		$session = $this->requestStack->getSession();
		$session->set('_locale', $locale);
	}
}
