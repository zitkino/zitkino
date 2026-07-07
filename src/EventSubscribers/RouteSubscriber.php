<?php

/**
 * This is a Doctrine event subscriber responsible for managing route synchronization based on entity lifecycle events such as persist, update, and removal.
 *
 * On the `postPersist` and `postUpdate` events, the `updateRoutes` method is triggered to ensure that the routes associated with the given entity are correctly created or updated.
 * On the `preRemove` event, the subscriber removes routes associated with the deleted entity.
 *
 * Attributes:
 * - `RouteAttributeDiscoveryService` is used to fetch routing configurations for entities.
 * - `EntityManagerInterface` facilitates interaction with the database for entity and route updates.
 * - `AdapterInterface` is used for cache invalidation after route changes.
 *
 * Handled Events:
 * - `postPersist`: Triggered after an entity is persisted.
 * - `postUpdate`: Triggered after an entity is updated.
 * - `preRemove`: Triggered before an entity is removed.
 *
 * The subscriber also supports translatable entities, ensuring that translations are properly handled and associated routes are updated accordingly.
 */
namespace App\EventSubscribers;

use App\Models\Route\Route;
use App\Services\RouteAttributeDiscoveryService;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\{PostPersistEventArgs, PostUpdateEventArgs, PreRemoveEventArgs};
use Doctrine\ORM\Events;
use Knp\DoctrineBehaviors\Contract\Entity\{TranslatableInterface, TranslationInterface};
use Symfony\Component\Cache\Adapter\AdapterInterface;

#[AsDoctrineListener(event: Events::postPersist)]
#[AsDoctrineListener(event: Events::postUpdate)]
#[AsDoctrineListener(event: Events::preRemove)]
class RouteSubscriber {
	public function __construct(private readonly RouteAttributeDiscoveryService $discoveryService, private readonly EntityManagerInterface $entityManager, private readonly AdapterInterface $cacheSystem) { }
	
	public function postPersist(PostPersistEventArgs $args): void {
		$this->updateRoutes($args->getObject(), $args);
	}
	
	public function postUpdate(PostUpdateEventArgs $args): void {
		$this->updateRoutes($args->getObject(), $args);
	}
	
	public function preRemove(PreRemoveEventArgs $args): void {
		$entity = $args->getObject();
		
		$discovered = $this->discoveryService->discover();
		$class = get_class($entity);
		
		if(isset($discovered[$class])) {
			$routes = $this->entityManager->getRepository(Route::class)
				->findBy([
					"entityClass" => $class,
					"entityId" => $entity->getId(),
				]);
			
			foreach($routes as $route) {
				$this->entityManager->remove($route);
			}
			// Note: flush will happen in the original transaction
			
			$this->cacheSystem->clear("routing");
		}
	}
	
	private function updateRoutes(object $entity, $args): void {
		$class = get_class($entity);
		
		// If it's a translation, we should use its translatable entity class for discovery
		$discoveryClass = $class;
		if($entity instanceof TranslationInterface) {
			$translatable = $entity->getTranslatable();
			if($translatable) {
				$discoveryClass = get_class($translatable);
			}
		}
		
		$discovered = $this->discoveryService->discover();
		
		if(isset($discovered[$discoveryClass])) {
			$configs = $discovered[$discoveryClass];
			
			// If it's a translation, update the main entity
			if($entity instanceof TranslationInterface) {
				$translatable = $entity->getTranslatable();
				if($translatable) {
					$this->updateRoutes($translatable, $args);
					return;
				}
			}
			
			foreach($configs as $config) {
				if($entity instanceof TranslatableInterface) {
					foreach($entity->getTranslations() as $translation) {
						/** @var TranslationInterface $translation */
						$slug = null;
						if(method_exists($translation, "getSlug")) {
							$slug = $translation->getSlug();
						} else if(method_exists($entity, "getSlug")) {
							$slug = $entity->getSlug();
						}
						
						if(!$slug) {
							continue;
						}
						
						$locale = $translation->getLocale();
						$this->syncRoute($class, $entity, $locale, $slug, $config);
					}
				} else if(method_exists($entity, "getSlug")) {
					$slug = $entity->getSlug();
					$locale = method_exists($entity, "getLocale") ? $entity->getLocale() : "cs";
					$this->syncRoute($class, $entity, $locale, $slug, $config);
				}
			}
			
			$this->entityManager->flush();
			$this->cacheSystem->clear("routing");
		}
	}
	
	private function syncRoute(string $class, object $entity, string $locale, string $slug, array $config): void {
		$route = $this->entityManager->getRepository(Route::class)
			->findOneBy([
				"entityClass" => $class,
				"entityId" => $entity->getId(),
				"locale" => $locale,
				"canonical_route" => $config["route_name_prefix"].(method_exists($entity, "getIdent") ? $entity->getIdent() : $entity->getId())
			]);
		
		if(!$route) {
			$route = new Route();
			$route->setEntityClass($class)
				->setEntityId($entity->getId())
				->setLocale($locale);
			$this->entityManager->persist($route);
		}
		
		$path = "/$slug";
		if(isset($config["path"][$locale])) {
			$path = str_replace("{slug}", $slug, $config["path"][$locale]);
		}
		
		$route->setSlug($path);
		$route->setController($config["controller"]);
		
		// Assuming entity has getIdent() or use ID
		$code = method_exists($entity, "getIdent") ? $entity->getIdent() : $entity->getId();
		$route->setCanonicalRoute($config["route_name_prefix"].$code);
	}
}
