<?php
namespace Zitkino;

use Dobine\Facades\DobineFacade;
use Doctrine\Common\Persistence\ObjectRepository;
use Nettrine\ORM\EntityManagerDecorator;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\ScreeningType;
use Zitkino\Screenings\Showtime;

class ScreeningFacade extends DobineFacade {
	/** @var ObjectRepository */
	private $repositoryType, $repositoryShowtime;
	
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Screening::class);
		$this->repositoryType = $this->entityManager->getRepository(ScreeningType::class);
		$this->repositoryShowtime = $this->entityManager->getRepository(Showtime::class);
	}
}
