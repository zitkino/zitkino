<?php

namespace App\Models\CinemaType;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CinemaType>
 */
class CinemaTypeRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, CinemaType::class);
	}
	
	/**
	 * @return CinemaType[]
	 */
	public function grabTypes(): array {
		return $this->findBy(["visible" => true], ["order" => "ASC", "ident" => "ASC"]);
	}
	
	public function grabTypeBySlug(string $slug): ?CinemaType {
		try {
			return $this->createQueryBuilder("t")
				->join("t.translations", "tt")
				->andWhere("tt.slug = :slug")->setParameter("slug", $slug)
				->getQuery()
				->getOneOrNullResult();
		} catch(NonUniqueResultException $e) {
			return null;
		}
	}
}
