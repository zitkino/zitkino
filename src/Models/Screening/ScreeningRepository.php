<?php

namespace App\Models\Screening;

use App\Models\Cinema\Cinema;
use App\Models\Movie\Movie;
use App\Models\Place\Place;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Screening>
 */
class ScreeningRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Screening::class);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByCinema(Cinema $cinema): array {
		return $this->findBy(["cinema" => $cinema]);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByMovie(Movie $movie): array {
		return $this->findBy(["movie" => $movie]);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByPlace(Place $place): array {
		return $this->findBy(["place" => $place]);
	}
	
	public function removeScreenings(Cinema $cinema): mixed {
		return $this->createQueryBuilder("s")
			->delete()
			->where("s.cinema = :cinema")
			->setParameter("cinema", $cinema)
			->getQuery()
			->getResult();
	}
	
	public function save(Screening $screening): Screening {
		$this->getEntityManager()->persist($screening);
		$this->getEntityManager()->flush();
		
		return $screening;
	}
}
