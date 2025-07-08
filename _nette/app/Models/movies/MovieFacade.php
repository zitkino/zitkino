<?php
namespace _nette\app\Models\movies;

use Dobine\Facades\DobineFacade;
use Nettrine\ORM\EntityManagerDecorator;
use _nette\app\Models\movies\Movie;

class MovieFacade extends DobineFacade {
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Movie::class);
	}
	
	/**
	 * @return Movie|object|null
	 */
	public function getByName(string $name) {
		return $this->repository->findOneBy(["name" => $name]);
	}
}
