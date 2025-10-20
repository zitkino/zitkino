<?php

namespace App\Models\Facades;

use App\Models\Entities\Movie;
use App\Models\Repositories\MovieRepository;
use Dobine\Facades\DobineFacade;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class MovieFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected MovieRepository|EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Movie::class);
	}
	
	public function grabByName(string $name): ?Movie {
		return $this->repository->findOneBy(["name" => $name]);
	}
}
