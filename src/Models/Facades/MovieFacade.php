<?php

namespace App\Models\Facades;

use App\Models\Entities\Movie;
use Dobine\Facades\DobineFacade;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class MovieFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Movie::class);
	}
	
	public function grabByName(string $name): ?Movie {
		return $this->repository->findOneBy(["name" => $name]);
	}
}
