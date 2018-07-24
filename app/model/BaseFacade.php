<?php
namespace App\Facades;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kdyby\Doctrine\Entities\BaseEntity;

/**
 * Class BaseFacade
 * @property EntityManager $entityManager
 * @property EntityRepository $repository
 */
abstract class BaseFacade {
	public function getById($id) {
		return $this->repository->findOneBy(["id" => $id]);
	}
	
	public function getAll() {
		return new ArrayCollection($this->repository->findAll());
	}
	
	public function getForForm() {
	    $output = [];
	    
	    $items = $this->repository->findBy([], ["name" => "ASC"]);
	    foreach($items as $item) {
	        $output[$item->id] = $item->name;
        }
        return $output;
    }
    
    public function search($needle, $limit = 50) {
        $items = $this->repository->createQueryBuilder("i")->select("i.id, i.name")
            ->where("i.name LIKE :needle")->setParameters(["needle" => "%$needle%"])->setMaxResults($limit)
            ->getQuery()->getResult();
        
        return $items;
    }

	/**
	 * @param BaseEntity $entity
	 * @return int
	 * @throws \Doctrine\ORM\ORMException
	 */
	public function save($entity) {
		$this->entityManager->persist($entity);
		$this->entityManager->flush();
		return $entity->getId();
	}

	/**
	 * @param BaseEntity $entity
	 * @return int
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function update($entity) {
		$this->entityManager->merge($entity);
		$this->entityManager->flush();
		return $entity->getId();
	}

	/**
	 * @param BaseEntity $entity
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function remove($entity) {
		$this->entityManager->remove($entity);
		$this->entityManager->flush();
	}
}
