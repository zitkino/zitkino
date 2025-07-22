<?php

namespace App\Facades;

use App\Entities\Language;
use App\Repositories\LanguageRepository;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class LanguageFacade {
	private EntityManagerInterface $entityManager;
	
	private LanguageRepository|EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Language::class);
	}
	
	public function grabByCode(string $code): ?Language {
		return $this->repository->findOneBy(["code" => $code]);
	}
	
	/**
	 * @return Language[]
	 */
	public function grabAll(): array {
		return $this->repository->findAll();
	}
}
