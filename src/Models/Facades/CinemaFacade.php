<?php

namespace App\Models\Facades;

use App\Models\Entities\{Cinema, CinemaType};
use App\Models\Repositories\CinemaRepository;
use Dobine\Facades\DobineFacade;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository, NonUniqueResultException};

class CinemaFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected CinemaRepository|EntityRepository $repository;
	
	protected EntityRepository $repositoryType;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Cinema::class);
		$this->repositoryType = $entityManager->getRepository(CinemaType::class);
	}
	
	public function grabById($id): ?Cinema {
		if(is_numeric($id)) {
			return $this->repository->findOneBy(["id" => $id]);
		} else {
			return $this->repository->findOneBy(["ident" => $id]);
		}
	}
	
	public function grabBySlug(string $slug): ?Cinema {
		return $this->repository->findOneBy(["slug" => $slug]);
	}
	
	public function grabVisible(): array {
		return $this->repository->visible()
			->getQuery()->getResult();
	}
	
	public function grabCurrent(): array {
		$qb = $this->repository->visible();
		
		$month = (int)date("m");
		if($month < 6 or $month > 9) {
			$qb->join("c.type", "ct")
				->andWhere("ct.ident != :type")
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
	
	public function grabTypes(): array {
		return $this->repositoryType->findBy(["visible" => true], ["order" => "ASC", "ident" => "ASC"]);
	}
	
	public function grabTypeBySlug(string $slug): ?CinemaType {
		try {
			return $this->repositoryType->createQueryBuilder("t")
				->join("t.translations", "tt")
				->andWhere("tt.slug = :slug")->setParameter("slug", $slug)
				->getQuery()
				->getOneOrNullResult();
		} catch(NonUniqueResultException $e) {
			return null;
		}
	}
	
	public function grabByType(string $type): array {
		return match ($type) {
			"all" => $this->grabVisible(),
			"current" => $this->grabCurrent(),
			default => $this->repository->visible()
				->join("c.type", "ct")
				->andWhere("ct.ident = :type")->setParameter("type", $type)
				->getQuery()
				->getResult()
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
