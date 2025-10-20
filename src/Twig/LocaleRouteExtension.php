<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LocaleRouteExtension extends AbstractExtension {
	private RequestStack $requestStack;
	
	private UrlGeneratorInterface $urlGenerator;
	
	private RouterInterface $router;
	
	private ?array $routeMap = null;
	
	private array $enabledLocales;
	
	public function __construct(RequestStack $requestStack, UrlGeneratorInterface $urlGenerator, RouterInterface $router, array $enabledLocales) {
		$this->requestStack = $requestStack;
		$this->urlGenerator = $urlGenerator;
		$this->router = $router;
		$this->enabledLocales = $enabledLocales;
	}
	
	/**
	 * Build route map dynamically from the router
	 */
	private function buildRouteMap(): array {
		if($this->routeMap !== null) {
			return $this->routeMap;
		}
		
		$this->routeMap = [];
		$routeCollection = $this->router->getRouteCollection();
		$routeGroups = [];
		
		// First pass: group routes by their base name and controller
		foreach($routeCollection->all() as $routeName => $route) {
			$defaults = $route->getDefaults();
			$requirements = $route->getRequirements();
			
			// Skip routes without locale
			if(!isset($requirements['_locale']) && !isset($defaults['_locale'])) {
				continue;
			}
			
			$controller = $defaults['_controller'] ?? null;
			if(!$controller) {
				continue;
			}
			
			// Determine the locale for this route
			$routeLocale = $defaults['_locale'] ?? 'cs';
			
			// Create a base name by removing locale suffix
			$baseName = preg_replace('/_[a-z]{2}$/', '', $routeName);
			
			// Group routes by controller and base name
			$groupKey = $controller.'::'.$baseName;
			
			if(!isset($routeGroups[$groupKey])) {
				$routeGroups[$groupKey] = [];
			}
			
			$routeGroups[$groupKey][$routeLocale] = $routeName;
		}
		
		// Second pass: build the locale-based route map
		foreach($this->enabledLocales as $locale) {
			$this->routeMap[$locale] = [];
		}
		
		foreach($routeGroups as $group) {
			foreach($group as $locale => $routeName) {
				// For each route in a group, map all variants
				foreach($group as $targetLocale => $targetRouteName) {
					$this->routeMap[$targetLocale][$routeName] = $targetRouteName;
				}
			}
		}
		
		return $this->routeMap;
	}
	
	public function getFunctions(): array {
		return [
			new TwigFunction('locale_path', [$this, 'getLocaleUrl']),
			new TwigFunction('locale_route', [$this, 'getLocaleRoute']),
		];
	}
	
	/**
	 * Get the appropriate route name for the current locale
	 */
	public function getLocaleRoute(string $baseRoute): string {
		$request = $this->requestStack->getCurrentRequest();
		if(!$request) {
			return $baseRoute;
		}
		
		$currentLocale = $request->getLocale();
		
		// Build route map if not already built
		$routeMap = $this->buildRouteMap();
		
		// If we have a mapping for this locale and route, use it
		if(isset($routeMap[$currentLocale][$baseRoute])) {
			return $routeMap[$currentLocale][$baseRoute];
		}
		
		// Otherwise return the base route
		return $baseRoute;
	}
	
	public function getLocaleUrl(string $targetLocale): string {
		$request = $this->requestStack->getCurrentRequest();
		if(!$request) {
			return '/';
		}
		
		$currentRoute = $request->attributes->get('_route');
		$routeParams = $request->attributes->get('_route_params', []);
		
		// Build route map if not already built
		$routeMap = $this->buildRouteMap();
		
		// Get the mapped route name for target locale
		$targetRoute = $routeMap[$targetLocale][$currentRoute] ?? $currentRoute;
		
		// Update locale in parameters
		$routeParams['_locale'] = $targetLocale;
		
		try {
			return $this->urlGenerator->generate($targetRoute, $routeParams);
		} catch(\Exception $e) {
			// Fallback to homepage if route doesn't exist
			try {
				return $this->urlGenerator->generate('homepage', ['_locale' => $targetLocale]);
			} catch(\Exception $e2) {
				return '/';
			}
		}
	}
}
