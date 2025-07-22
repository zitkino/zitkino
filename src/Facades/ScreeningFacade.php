<?php

namespace App\Facades;

use App\Entities\{Cinema, Movie, Place, Screening, ScreeningType};
use App\Repositories\{ScreeningRepository, ScreeningTypeRepository};
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ScreeningFacade {
	private EntityManagerInterface $entityManager;
	
	private ScreeningRepository|EntityRepository $repository;
	
	private ScreeningTypeRepository|EntityRepository $repositoryType;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Screening::class);
		$this->repositoryType = $entityManager->getRepository(ScreeningType::class);
	}
	
	public function grabById(int $id): ?Screening {
		return $this->repository->find($id);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByCinema(Cinema $cinema): array {
		return $this->repository->findBy(["cinema" => $cinema]);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByMovie(Movie $movie): array {
		return $this->repository->findBy(["movie" => $movie]);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByPlace(Place $place): array {
		return $this->repository->findBy(["place" => $place]);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabAll(): array {
		return $this->repository->findAll();
	}
	
	public function grabTypeByCode(string $code): ?ScreeningType {
		return $this->repositoryType->findOneBy(["code" => $code]);
	}
	
	/**
	 * Creates a new screening
	 */
	public function create(Movie $movie, Cinema $cinema): Screening {
		$screening = new Screening($movie, $cinema);
		$this->entityManager->persist($screening);
		$this->entityManager->flush();
		
		return $screening;
	}
	
	/**
	 * Removes all screenings for a cinema
	 */
	public function removeScreenings(Cinema $cinema): void {
		$screenings = $this->grabByCinema($cinema);
		
		foreach($screenings as $screening) {
			$this->entityManager->remove($screening);
		}
		
		$this->entityManager->flush();
	}
}
