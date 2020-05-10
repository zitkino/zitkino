<?php
namespace Zitkino\Cinemas;

use Doctrine\ORM\EntityRepository;

class CinemaRepository extends EntityRepository {
	public function visible() {
		return $this->createQueryBuilder("c")
			->where("c.activeUntil is null")
			->orderBy("c.code");
	}
	
	public function parsable() {
		$parseDate = new \DateTime();
		$parseDate->modify("-1 hour");
		
		return $this->createQueryBuilder("c")
			->where("c.activeUntil is null")->andWhere("c.parsable = 1")->andWhere("c.parsed is null or c.parsed < :parseDate")
			->setParameter("parseDate", $parseDate)
			->orderBy("c.code");
	}
}
