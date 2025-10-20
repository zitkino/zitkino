<?php

namespace App\Models\Facades;

use App\Models\Entities\Language;
use App\Models\Repositories\LanguageRepository;
use Dobine\Facades\DobineFacade;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class LanguageFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected LanguageRepository|EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Language::class);
	}
	
	public function grabByCode(string $code): ?Language {
		return $this->repository->findOneBy(["code" => $code]);
	}
}
