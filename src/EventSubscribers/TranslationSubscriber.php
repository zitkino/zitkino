<?php

namespace App\EventSubscribers;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\{PostLoadEventArgs, PostPersistEventArgs};
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;

#[AsDoctrineListener(event: Events::postLoad)]
#[AsDoctrineListener(event: Events::postPersist)]
class TranslationSubscriber {
	public function __construct(private readonly array $enabledLocales, private readonly EntityManagerInterface $entityManager) {
	}
	
	public function postLoad(PostLoadEventArgs $args): void {
		$this->checkTranslations($args->getObject());
	}
	
	public function postPersist(PostPersistEventArgs $args): void {
		if($this->checkTranslations($args->getObject())) {
			$this->entityManager->flush();
		}
	}
	
	private function checkTranslations(object $entity): bool {
		$created = false;
		if($entity instanceof TranslatableInterface) {
			foreach($this->enabledLocales as $locale) {
				// This will create translation if it doesn't exist because of Knp\DoctrineBehaviors
				$translation = $entity->translate($locale);
				if($this->entityManager->getUnitOfWork()
						->getEntityState($translation) === UnitOfWork::STATE_NEW) {
					// Use reflection to initialize the first available non-skipped field with a dummy value.
					// This prevents isEmpty() from returning true and Knp\DoctrineBehaviors from discarding the translation.
					$reflectionClass = new \ReflectionClass($translation);
					foreach($reflectionClass->getProperties() as $property) {
						$name = $property->getName();
						if(in_array($name, ["id", "translatable", "locale"], true)) {
							continue;
						}
						
						// Check if the property is a string
						$type = $property->getType();
						if($type instanceof \ReflectionNamedType && $type->getName() !== "string") {
							continue;
						}
						
						// Try to set value via setter or property if accessible
						$setter = "set".ucfirst($name);
						if($reflectionClass->hasMethod($setter)) {
							$translation->$setter("-");
							$created = true;
							break;
						} else if($property->isPublic()) {
							$translation->$name = "-";
							$created = true;
							break;
						}
					}
					
					if(!$created) {
						// Fallback if no suitable property was found (should not happen with standard translation entities)
						$created = true;
					}
				}
			}
			
			$entity->mergeNewTranslations();
		}
		return $created;
	}
}
