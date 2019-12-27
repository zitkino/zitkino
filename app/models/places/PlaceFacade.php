<?php
namespace Zitkino;

use Dobine\Facades\DobineFacade;
use Doctrine\ORM\EntityManager;

class PlaceFacade extends DobineFacade {
	public function __construct(EntityManager $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Place::class);
	}
	
	/**
	 * @param int|string $id
	 * @return object|Language
	 */
	public function getById($id) {
		if(is_numeric($id)) {
			return $this->repository->findOneBy(["id" => $id]);
		} else {
			return $this->repository->findOneBy(["code" => $id]);
		}
	}
}
