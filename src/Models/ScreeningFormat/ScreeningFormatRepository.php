<?php

namespace App\Models\ScreeningFormat;

use Dobine\Utils\Strings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ScreeningFormat>
 */
class ScreeningFormatRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, ScreeningFormat::class);
	}
	
	public function grabFormat(?string $format = null): ?ScreeningFormat {
		if(empty($format)) {
			return null;
		}
		return $this->findOneBy(["ident" => Strings::identify($format)]);
	}
	
	public function save(ScreeningFormat $format): ScreeningFormat {
		$this->getEntityManager()->persist($format);
		$this->getEntityManager()->flush();
		
		return $format;
	}
}
