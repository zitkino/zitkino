<?php

namespace App\Twig;

use Symfony\Component\Intl\Locales;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class LocalesExtension extends AbstractExtension {
	/** @var string[] $enabledLocales */
	public function __construct(private readonly array $enabledLocales = []) {
	}
	
	/**
	 * Twig callable that returns an array of locales data.
	 *
	 * @return array<int, array{code:string,name:string,flag:string}>
	 */
	public function locales(): array {
		$result = [];
		$flagIconPrefix = "fi fi-";
		
		foreach($this->enabledLocales as $locale) {
			$result[] = [
				"code" => $locale,
				"name" => Locales::getName($locale, $locale),
				"flag" => match ($locale) {
					"en" => $flagIconPrefix."gb",
					"cs" => $flagIconPrefix."cz",
					default => $flagIconPrefix.$locale,
				},
			];
		}
		
		return $result;
	}
	
	public function getFunctions(): array {
		return [new TwigFunction("locales", $this->locales(...))];
	}
}
