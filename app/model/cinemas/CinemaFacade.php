<?php
namespace Zitkino\Cinemas;

use Dobine\Facades\DobineFacade;
use Doctrine\ORM\EntityManager;

class CinemaFacade extends DobineFacade {
	use Cinemas;
	
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Cinema::class);
	}
	
	/**
	 * @param int|string $id
	 * @return object|Cinema
	 */
	public function getById($id) {
		if(is_numeric($id)) {
			return $this->repository->findOneBy(["id" => $id]);
		} else {
			return $this->repository->findOneBy(["shortName" => $id]);
		}
	}
}
