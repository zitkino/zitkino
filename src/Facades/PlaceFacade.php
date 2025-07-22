<?php

namespace App\Facades;

use App\Entities\{Cinema, Place};
use App\Repositories\PlaceRepository;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class PlaceFacade {
	private EntityManagerInterface $entityManager;
	
	private PlaceRepository|EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Place::class);
	}
	
	public function grabByName(string $name): ?Place {
		return $this->repository->findOneBy(["name" => $name]);
	}
	
	public function grabById(int $id): ?Place {
		return $this->repository->find($id);
	}
	
	/**
	 * @return Place[]
	 */
	public function grabByCinema(Cinema $cinema): array {
		return $this->repository->findBy(["cinema" => $cinema]);
	}
	
	/**
	 * @return Place[]
	 */
	public function grabAll(): array {
		return $this->repository->findAll();
	}
	
	/**
	 * Creates a new place if it doesn't exist
	 */
	public function createIfNotExists(string $name, Cinema $cinema): Place {
		$place = $this->grabByName($name);
		
		if($place === null) {
			$place = new Place($name);
			$place->setCinema($cinema);
			$this->entityManager->persist($place);
			$this->entityManager->flush();
		}
		
		return $place;
	}
}
