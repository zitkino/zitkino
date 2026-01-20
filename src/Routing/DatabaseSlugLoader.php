<?php

namespace App\Routing;

use App\Models\Repositories\RouteRepository;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

class DatabaseSlugLoader extends Loader {
	private bool $isLoaded = false;
	
	public function __construct(
		private readonly RouteRepository $routeRepository,
		private readonly array $sluggableEntities,
		protected ?string $env = null
	) {
		parent::__construct($env);
	}
	
	public function load(mixed $resource, ?string $type = null): RouteCollection {
		if(true === $this->isLoaded) {
			throw new \RuntimeException('Do not add the "database_slug" loader twice');
		}
		
		$routes = new RouteCollection();
		$dbRoutes = $this->routeRepository->findAll();
		
		foreach($dbRoutes as $dbRoute) {
			$slug = $dbRoute->getSlug();
			
			// Create route for this slug
			$route = new SymfonyRoute("/$slug", [
				'_controller' => $dbRoute->getController(),
				'slug' => $slug,
				'_locale' => $dbRoute->getLocale(),
				'_canonical_route' => $dbRoute->getCanonicalRoute()
			], [
				'slug' => preg_quote($slug, '#')
			]);

			$routes->add($dbRoute->getCanonicalRoute().'.'.$dbRoute->getLocale(), $route);
		}
		
		$this->isLoaded = true;
		
		return $routes;
	}
	
	public function supports(mixed $resource, ?string $type = null): bool {
		return 'database_slug' === $type;
	}
}
