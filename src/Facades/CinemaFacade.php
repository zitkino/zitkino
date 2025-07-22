<?php

namespace App\Facades;

use App\Repositories\CinemaRepository;
use App\Entities\{Cinema, CinemaType};
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class CinemaFacade {
	private EntityManagerInterface $entityManager;
	
	private CinemaRepository|EntityRepository $repository;
	
	private EntityRepository $repositoryType;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Cinema::class);
		$this->repositoryType = $entityManager->getRepository(CinemaType::class);
	}
	
	public function grabById(int|string $id): Cinema|null {
		if(is_numeric($id)) {
			return $this->repository->findOneBy(["id" => $id]);
		} else {
			return $this->repository->findOneBy(["code" => $id]);
		}
	}
	
	public function grabType(string $type): CinemaType|null {
		return $this->repositoryType->findOneBy(["code" => $type]);
	}
	
	public function grabAll(): array {
		return $this->repository->visible()
			->getQuery()
			->getResult();
	}
	
	public function grabCurrent(): array {
		$qb = $this->repository->visible();
		
		$month = (int)date("m");
		if($month < 6 or $month > 9) {
			$qb->join("c.type", "ct")
				->andWhere("ct.code != :type")
				->setParameter("type", "summer");
		}
		
		return $qb->getQuery()
			->getResult();
	}
	
	public function grabParsable(): array {
		return $this->repository->parsable()
			->getQuery()
			->getResult();
	}
	
	public function grabByType(string $type): array {
		return match ($type) {
			"all" => $this->grabAll(),
			"current" => $this->grabCurrent(),
			default => $this->repository->visible()
				->join("c.type", "ct")
				->andWhere("ct.code = :type")
				->setParameter("type", $type)
				->getQuery()
				->getResult(),
		};
	}
	
	public function gatherWithMovies(string $type = "all"): array {
		$output = [];
		
		$cinemas = $this->grabByType($type);
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			if($cinema->hasScreenings()) {
				$output[] = $cinema;
			}
		}
		
		return $output;
	}
}
