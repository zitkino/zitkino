<?php

namespace App\Models\Cinema;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cinema>
 */
class CinemaRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Cinema::class);
	}
	
	public function active(): QueryBuilder {
		return $this->createQueryBuilder("c")
			->where("c.activeUntil is null or c.activeUntil >= :today")
			->setParameter("today", new \DateTime());
	}
	
	public function visible(): QueryBuilder {
		return $this->active()
			->andWhere("c.visible = 1")
			->orderBy("c.order")
			->addOrderBy("c.ident");
	}
	
	public function parsable(): QueryBuilder {
		$parseDate = new \DateTime();
		$parseDate->modify("-1 hour");
		
		return $this->active()
			->andWhere("c.parsable = 1")
			->andWhere("c.parsed is null or c.parsed < :parseDate")
			->setParameter("parseDate", $parseDate)
			->orderBy("c.ident");
	}
	
	public function save(Cinema $cinema): Cinema {
		$this->getEntityManager()->persist($cinema);
		$this->getEntityManager()->flush();
		
		return $cinema;
	}
	
	public function grabById($id): ?Cinema {
		if(is_numeric($id)) {
			return $this->findOneBy(["id" => $id]);
		} else {
			return $this->findOneBy(["ident" => $id]);
		}
	}
	
	public function grabBySlug(string $slug): ?Cinema {
		return $this->findOneBy(["slug" => $slug]);
	}
	
	/**
	 * @return Cinema[]
	 */
	public function grabVisible(): array {
		return $this->visible()
			->getQuery()->getResult();
	}
	
	/**
	 * @return Cinema[]
	 */
	public function grabCurrent(): array {
		$qb = $this->visible();
		
		$month = (int)date("m");
		if($month < 6 or $month > 9) {
			$qb->join("c.type", "ct")
				->andWhere("ct.ident != :type")
				->setParameter("type", "summer");
		}
		
		return $qb->getQuery()
			->getResult();
	}
	
	/**
	 * @return Cinema[]
	 */
	public function grabParsable(): array {
		return $this->parsable()
			->getQuery()
			->getResult();
	}
	
	/**
	 * @return Cinema[]
	 */
	public function grabByType(string $type): array {
		return match ($type) {
			"all" => $this->grabVisible(),
			"current" => $this->grabCurrent(),
			default => $this->visible()
				->join("c.type", "ct")
				->andWhere("ct.ident = :type")->setParameter("type", $type)
				->getQuery()
				->getResult()
		};
	}
	
	/**
	 * @return Cinema[]
	 */
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
