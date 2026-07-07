<?php

namespace App\Routing;

use App\Models\Route\Route;
use App\Services\RouteAttributeDiscoveryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\{Route as SymfonyRoute, RouteCollection};

class DatabaseSlugLoader extends Loader {
	private bool $isLoaded = false;
	
	public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RouteAttributeDiscoveryService $discoveryService, protected ?string $env = null) {
		parent::__construct($env);
	}
	
	public function load(mixed $resource, ?string $type = null): RouteCollection {
		if(true === $this->isLoaded) {
			throw new \RuntimeException('Do not add the "database_slug" loader twice');
		}
		
		$routes = new RouteCollection();
		
		$discovered = $this->discoveryService->discover();
		foreach($discovered as $entityClass => $configs) {
			$entities = $this->entityManager->getRepository($entityClass)
				->findAll();
			
			foreach($configs as $config) {
				$className = $config["class"];
				$methodName = $config["method"];
				$routeNamePrefix = $config["route_name_prefix"];
				
				$discoveredPath = $config["path"] ?? null;
				
				foreach($entities as $entity) {
					if(method_exists($entity, "getTranslations") && !empty($entity->getTranslations())) {
						foreach($entity->getTranslations() as $translation) {
							$slug = null;
							if(method_exists($translation, "getSlug")) {
								$slug = $translation->getSlug();
							} else if(method_exists($entity, "getSlug")) {
								$slug = $entity->getSlug();
							}
							
							if($slug) {
								$locale = $translation->getLocale();
								$canonicalRouteName = $routeNamePrefix.(method_exists($entity, "getIdent") ? $entity->getIdent() : $entity->getId());
								
								$path = "/$slug";
								if($discoveredPath !== null && isset($discoveredPath[$locale])) {
									$path = str_replace("{slug}", $slug, $discoveredPath[$locale]);
								}
								
								$this->syncRoute($entityClass, $entity, $locale, $path, $config, $canonicalRouteName);
								
								$route = new SymfonyRoute($path, [
									"_controller" => $className."::".$methodName,
									"slug" => $slug,
									"_locale" => $locale,
									"_canonical_route" => $canonicalRouteName
								], [
									"slug" => preg_quote($slug, "#")
								]);
								
								$routes->add($canonicalRouteName.".".$locale, $route);
							}
						}
					} else if(method_exists($entity, "getSlug")) {
						$slug = $entity->getSlug();
						$locale = method_exists($entity, "getLocale") ? $entity->getLocale() : "cs"; // default locale if not localable
						$canonicalRouteName = $routeNamePrefix.(method_exists($entity, "getIdent") ? $entity->getIdent() : $entity->getId());
						
						$path = "/$slug";
						if($discoveredPath !== null && isset($discoveredPath[$locale])) {
							$path = str_replace("{slug}", $slug, $discoveredPath[$locale]);
						}
						
						$this->syncRoute($entityClass, $entity, $locale, $path, $config, $canonicalRouteName);
						
						$route = new SymfonyRoute($path, [
							"_controller" => $className."::".$methodName,
							"slug" => $slug,
							"_locale" => $locale,
							"_canonical_route" => $canonicalRouteName
						], [
							"slug" => preg_quote($slug, "#")
						]);
						
						$routes->add($canonicalRouteName.".".$locale, $route);
					}
				}
			}
		}
		
		$this->entityManager->flush();
		$this->isLoaded = true;
		
		return $routes;
	}
	
	private function syncRoute(string $class, object $entity, string $locale, string $path, array $config, string $canonicalRouteName): void {
		$route = $this->entityManager->getRepository(Route::class)
			->findOneBy([
				"entityClass" => $class,
				"entityId" => $entity->getId(),
				"locale" => $locale,
				"canonical_route" => $canonicalRouteName
			]);
		
		if(!$route) {
			$route = new Route();
			$route->setEntityClass($class)
				->setEntityId($entity->getId())
				->setLocale($locale)
				->setSlug($path)
				->setController($config["controller"])
				->setCanonicalRoute($canonicalRouteName);
			
			$this->entityManager->persist($route);
		}
	}
	
	public function supports(mixed $resource, ?string $type = null): bool {
		return "database_slug" === $type;
	}
}
