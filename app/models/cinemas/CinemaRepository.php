<?php
namespace Zitkino\Cinemas;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class CinemaRepository extends EntityRepository {
	public function active(): QueryBuilder {
		return $this->createQueryBuilder("c")
			->where("c.activeUntil is null or c.activeUntil >= :today")
			->setParameter("today", new \DateTime());
	}
	
	public function visible(): QueryBuilder {
		return $this->active()->orderBy("c.code");
	}
	
	public function parsable(): QueryBuilder {
		$parseDate = new \DateTime();
		$parseDate->modify("-1 hour");
		
		return $this->active()->andWhere("c.parsable = 1")->andWhere("c.parsed is null or c.parsed < :parseDate")
			->setParameter("parseDate", $parseDate)
			->orderBy("c.code");
	}
}
