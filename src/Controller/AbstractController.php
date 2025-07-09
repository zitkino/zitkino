<?php

namespace App\Controller;

use App\Service\MetaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Base controller for all application controllers.
 */
abstract class AbstractController extends SymfonyAbstractController {
	/** @var TranslatorInterface */
	protected $translator;
	
	/** @var RequestStack */
	protected $requestStack;
	
	/** @var MetaService */
	protected $metaService;
	
	/** @var ParameterBagInterface */
	protected $parameterBag;
	
	public function __construct(
		TranslatorInterface $translator,
		RequestStack $requestStack,
		MetaService $metaService,
		ParameterBagInterface $parameterBag
	) {
		$this->translator = $translator;
		$this->requestStack = $requestStack;
		$this->metaService = $metaService;
		$this->parameterBag = $parameterBag;
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
