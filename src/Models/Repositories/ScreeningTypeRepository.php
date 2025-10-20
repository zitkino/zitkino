<?php

namespace App\Models\Repositories;

use App\Models\Entities\ScreeningType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ScreeningTypeRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, ScreeningType::class);
	}
}
