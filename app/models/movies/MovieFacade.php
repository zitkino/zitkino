<?php
namespace Zitkino;

use Dobine\Facades\DobineFacade;
use Nettrine\ORM\EntityManagerDecorator;
use Zitkino\Movies\Movie;

class MovieFacade extends DobineFacade {
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Movie::class);
	}
}
