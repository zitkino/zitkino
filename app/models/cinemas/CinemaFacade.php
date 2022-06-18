<?php
namespace Zitkino\Cinemas;

use Dobine\Facades\DobineFacade;
use Doctrine\ORM\EntityRepository;
use Nettrine\ORM\EntityManagerDecorator;

/**
 * Class CinemaFacade
 * @property CinemaRepository $repository
 */
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
	 * @return Cinema|object|null
	 */
	public function getById($id) {
		if(is_numeric($id)) {
			return $this->repository->findOneBy(["id" => $id]);
		} else {
			return $this->repository->findOneBy(["code" => $id]);
		}
	}
	
	/**
	 * @return CinemaType|object|null
	 */
	public function getType(string $type) {
		return $this->repositoryType->findOneBy(["code" => $type]);
	}
	
	public function save($entity) {
		$this->entityManager->persist($entity);
		$this->entityManager->flush();
		
		return $entity->getId();
	}
}
