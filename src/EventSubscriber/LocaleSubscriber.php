<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocaleSubscriber implements EventSubscriberInterface {
	private TranslatorInterface $translator;
	
	private string $defaultLocale;
	
	public function __construct(TranslatorInterface $translator, string $defaultLocale) {
		$this->translator = $translator;
		$this->defaultLocale = $defaultLocale;
	}
	
	public function onKernelRequest(RequestEvent $event): void {
		$request = $event->getRequest();
		
		// Try to get locale from route parameters
		$locale = $request->attributes->get('_locale');
		
		// If no locale in route, try session
		if(!$locale) {
			$locale = $request->getSession()
				->get('_locale', $this->defaultLocale);
		}
		
		// Set locale on request
		$request->setLocale($locale);
		
		// Set locale on translator
		$this->translator->setLocale($locale);
		
		// Store in session for future requests
		$request->getSession()
			->set('_locale', $locale);
	}
	
	public static function getSubscribedEvents(): array {
		return [
			KernelEvents::REQUEST => [['onKernelRequest', 120]],
		];
	}
}
