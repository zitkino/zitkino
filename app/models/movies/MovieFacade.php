<?php
namespace Zitkino;

use Dobine\Facades\DobineFacade;
use Doctrine\ORM\EntityManager;
use Zitkino\Movies\Movie;

class MovieFacade extends DobineFacade {
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Movie::class);
	}
}
