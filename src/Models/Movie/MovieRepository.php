<?php

namespace App\Models\Movie;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Movie::class);
	}
	
	public function grabByName(string $name): ?Movie {
		return $this->findOneBy(["name" => $name]);
	}
	
	public function save(Movie $movie): Movie {
		$this->getEntityManager()->persist($movie);
		$this->getEntityManager()->flush();
		
		return $movie;
	}
}
