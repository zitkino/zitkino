<?php
namespace _nette\app\Models\places;

use _nette\app\Models\places\Place;
use Dobine\Facades\DobineFacade;
use Nettrine\ORM\EntityManagerDecorator;

class PlaceFacade extends DobineFacade {
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Place::class);
	}
	
	/**
	 * @return Place|object|null
	 */
	public function getByName(string $name) {
		return $this->repository->findOneBy(["name" => $name]);
	}
}
