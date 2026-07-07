<?php

namespace App\Models\Place;

use App\Models\Cinema\Cinema;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Place>
 */
class PlaceRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Place::class);
	}
	
	public function grabByName(string $name): ?Place {
		return $this->findOneBy(["name" => $name]);
	}
	
	/**
	 * @return Place[]
	 */
	public function grabByCinema(Cinema $cinema): array {
		return $this->findBy(["cinema" => $cinema]);
	}
	
	public function save(Place $place): Place {
		$this->getEntityManager()->persist($place);
		$this->getEntityManager()->flush();
		
		return $place;
	}
}
