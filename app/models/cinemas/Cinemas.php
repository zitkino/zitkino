<?php
namespace Zitkino\Cinemas;

/**
 * Methods for handling multiple cinemas.
 * @property CinemaRepository $repository
 */
trait Cinemas {
	public function getAll() {
		return $this->repository->visible()->getQuery()->getResult();
	}
	
	public function getCurrent() {
		$qb = $this->repository->visible();
		
		$month = (int)date("m");
		if($month < 6 or $month > 9) {
			$qb->join("c.type", "ct")
				->andWhere("ct.code != :type")->setParameter("type", "summer");
		}
		
		return $qb->getQuery()->getResult();
	}
	
	public function getParsable() {
		return $this->repository->parsable()->getQuery()->getResult();
	}
	
	public function getByType($type): array {
		switch($type) {
			case "all":
				return $this->getAll();
			case "current":
				return $this->getCurrent();
			default:
				return $this->repository->visible()->join("c.type", "ct")
					->andWhere("ct.code = :type")->setParameter("type", $type)
					->getQuery()->getResult();
		}
	}
	
	public function getWithMovies($type = "all"): array {
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
