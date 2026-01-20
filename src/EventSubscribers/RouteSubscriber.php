<?php

namespace App\EventSubscribers;

use App\Models\Entities\Route;
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
	public function __construct(private readonly array $sluggableEntities, private readonly EntityManagerInterface $entityManager, private readonly AdapterInterface $cacheSystem) { }
	
	public function postPersist(PostPersistEventArgs $args): void {
		$this->updateRoutes($args->getObject(), $args);
	}
	
	public function postUpdate(PostUpdateEventArgs $args): void {
		$this->updateRoutes($args->getObject(), $args);
	}
	
	public function preRemove(PreRemoveEventArgs $args): void {
		$entity = $args->getObject();
		
		if($entity instanceof TranslatableInterface) {
			$class = get_class($entity);
			if(isset($this->sluggableEntities[$class])) {
				$routes = $this->entityManager->getRepository(Route::class)
					->findBy([
						'entityClass' => $class,
						'entityId' => $entity->getId(),
					]);
				
				foreach($routes as $route) {
					$this->entityManager->remove($route);
				}
				// Note: flush will happen in the original transaction
				
				$this->cacheSystem->clear('routing');
			}
		}
	}
	
	private function updateRoutes(object $entity, $args): void {
		if($entity instanceof TranslatableInterface) {
			$class = get_class($entity);
			if(isset($this->sluggableEntities[$class])) {
				$config = $this->sluggableEntities[$class];
				
				foreach($entity->getTranslations() as $translation) {
					/** @var TranslationInterface $translation */
					$slug = method_exists($translation, 'getSlug') ? $translation->getSlug() : null;
					if(!$slug) {
						continue;
					}
					
					$locale = $translation->getLocale();
					
					$route = $this->entityManager->getRepository(Route::class)
						->findOneBy([
							'entityClass' => $class,
							'entityId' => $entity->getId(),
							'locale' => $locale
						]);
					
					if(!$route) {
						$route = new Route();
						$route->setEntityClass($class)
							->setEntityId($entity->getId())
							->setLocale($locale);
						$this->entityManager->persist($route);
					} else {
						$route->setUpdated(new \DateTime());
					}
					
					$route->setSlug($slug);
					$route->setController($config['controller']);
					
					// Assuming entity has getCode() or use ID
					$code = method_exists($entity, 'getCode') ? $entity->getCode() : $entity->getId();
					$route->setCanonicalRoute($config['route_name_prefix'].$code);
				}
				
				$this->entityManager->flush();
				$this->cacheSystem->clear('routing');
			}
		} else if($entity instanceof TranslationInterface) {
			$translatable = $entity->getTranslatable();
			if($translatable) {
				$this->updateRoutes($translatable, $args);
			}
		}
	}
}
