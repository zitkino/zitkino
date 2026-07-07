<?php

namespace App\Models\ScreeningType;

use Dobine\Utils\Strings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScreeningType>
 */
class ScreeningTypeRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, ScreeningType::class);
	}
	
	public function grabType(?string $type = null): ?ScreeningType {
		if(empty($type)) {
			return null;
		}
		return $this->findOneBy(["ident" => Strings::identify($type)]);
	}
	
	public function save(ScreeningType $type): ScreeningType {
		$this->getEntityManager()->persist($type);
		$this->getEntityManager()->flush();
		
		return $type;
	}
}
