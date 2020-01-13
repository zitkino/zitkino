<?php
namespace Zitkino\Cinemas;

use Dobine\Facades\DobineFacade;
use Nettrine\ORM\EntityManagerDecorator;
use Doctrine\ORM\EntityRepository;

class CinemaFacade extends DobineFacade {
	use Cinemas;
	
	/** @var EntityRepository */
	private $repositoryType;
	
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Cinema::class);
		$this->repositoryType = $this->entityManager->getRepository(CinemaType::class);
	}
	
	/**
	 * @param int|string $id
	 * @return object|Cinema
	 */
	public function getById($id) {
		if(is_numeric($id)) {
			return $this->repository->findOneBy(["id" => $id]);
		} else {
			return $this->repository->findOneBy(["code" => $id]);
		}
	}
	
	public function getType($type) {
		return $this->repositoryType->findOneBy(["code" => $type]);
	}
}
