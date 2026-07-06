<?php

namespace App\Services;

use App\Attributes\DatabaseRoute;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Discovers route attributes in controller classes within a specified directory.
 *
 * This service scans all PHP files in the controllers directory, recursively checks for  classes, and identifies route attributes associated with methods. It organizes and returns discovered routes along with route metadata, including the controller method, route name prefixes, HTTP methods, class names, and paths.
 */
class RouteAttributeDiscoveryService {
	private string $controllersDir;
	
	public function __construct(string $projectDir) {
		$this->controllersDir = $projectDir.'/src/Controllers';
	}
	
	/**
	 * @return array<string, array{controller: string, route_name_prefix: string, method: string, class: string, path: ?array}>
	 */
	public function discover(): array {
		$discovered = [];
		if(!is_dir($this->controllersDir)) {
			return $discovered;
		}
		
		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->controllersDir));
		foreach($iterator as $file) {
			if($file->isDir() || $file->getExtension() !== 'php') {
				continue;
			}
			
			$relativePath = substr($file->getPathname(), strlen($this->controllersDir) + 1);
			$className = 'App\\Controllers\\'.str_replace([DIRECTORY_SEPARATOR, '.php'], ['\\', ''], $relativePath);
			
			if(!class_exists($className)) {
				continue;
			}
			
			$reflectionClass = new \ReflectionClass($className);
			
			$classPrefix = "";
			$classRouteAttributes = $reflectionClass->getAttributes(Route::class);
			if(!empty($classRouteAttributes)) {
				/** @var Route $classRoute */
				$classRoute = $classRouteAttributes[0]->newInstance();
				$classPrefix = $classRoute->name ?? "";
			}
			
			foreach($reflectionClass->getMethods() as $method) {
				$attributes = $method->getAttributes(DatabaseRoute::class);
				foreach($attributes as $attribute) {
					/** @var DatabaseRoute $dbRouteAttr */
					$dbRouteAttr = $attribute->newInstance();
					if(!$dbRouteAttr->entityClass) {
						continue;
					}
					
					$discovered[$dbRouteAttr->entityClass][] = [
						'controller' => $className.'::'.$method->getName(),
						'route_name_prefix' => $classPrefix.$dbRouteAttr->name,
						'method' => $method->getName(),
						'class' => $className,
						'path' => $dbRouteAttr->path
					];
				}
			}
		}
		
		return $discovered;
	}
}
