<?php
namespace Zitkino\Cinemas;

use Doctrine\ORM\EntityRepository;

/**
 * Methods for handling multiple cinemas.
 * @property EntityRepository $repository
 */
trait Cinemas {
	public function cinemasSelect() {
		return $this->repository->createQueryBuilder("c")
			->where("c.activeUntil is null")->orderBy("c.code")
			->andWhere("c.disabled = 0");
	}
	
	
	public function getAll() {
		return $this->cinemasSelect()->getQuery()->getResult();
	}
	
	public function getByType($type) {
		if($type == "all") {
			return $this->getAll();
		} else {
			return $this->cinemasSelect()->join("c.type", "ct")
				->andWhere("ct.code = :type")->setParameter("type", $type)
				->getQuery()->getResult();
		}
	}
	
	public function getWithMovies($type = "all") {
		$output = [];
		
		$cinemas = $this->getByType($type);
		/** @var Cinema $cinema */
		foreach($cinemas as $cinema) {
			if($cinema->hasScreenings()) {
				$output[] = $cinema;
			}
		}
		
		return $output;
	}
}
