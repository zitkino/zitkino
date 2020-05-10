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
	
	public function getParsable() {
		return $this->repository->parsable()->getQuery()->getResult();
	}
	
	public function getByType($type) {
		if($type == "all") {
			return $this->getAll();
		} else {
			return $this->repository->visible()->join("c.type", "ct")
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
