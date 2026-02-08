<?php

namespace App\Models\Facades;

use App\Models\Entities\{Cinema, Place};
use Dobine\Facades\DobineFacade;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class PlaceFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected EntityRepository $repository;
	
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
