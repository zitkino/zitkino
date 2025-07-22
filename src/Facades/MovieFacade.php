<?php

namespace App\Facades;

use App\Entities\Movie;
use App\Repositories\MovieRepository;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class MovieFacade {
	private EntityManagerInterface $entityManager;
	
	private MovieRepository|EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Movie::class);
	}
	
	public function grabByName(string $name): ?Movie {
		return $this->repository->findOneBy(["name" => $name]);
	}
	
	public function grabById(int $id): ?Movie {
		return $this->repository->find($id);
	}
	
	/**
	 * @return Movie[]
	 */
	public function grabAll(): array {
		return $this->repository->findAll();
	}
	
	/**
	 * Creates a new movie if it doesn't exist
	 */
	public function createIfNotExists(string $name): Movie {
		$movie = $this->grabByName($name);
		
		if($movie === null) {
			$movie = new Movie($name);
			$this->entityManager->persist($movie);
			$this->entityManager->flush();
		}
		
		return $movie;
	}
}
