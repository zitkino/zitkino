<?php

namespace App\Models\Facades;

use App\Models\Entities\{Place, Cinema};
use App\Models\Repositories\PlaceRepository;
use Dobine\Facades\DobineFacade;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class PlaceFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected PlaceRepository|EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Place::class);
	}
	
	public function grabByName(string $name): ?Place {
		return $this->repository->findOneBy(["name" => $name]);
	}
	
	/**
	 * @return Place[]
	 */
	public function grabByCinema(Cinema $cinema): array {
		return $this->repository->findBy(["cinema" => $cinema]);
	}
}
