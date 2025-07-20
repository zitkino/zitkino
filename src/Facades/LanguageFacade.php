<?php

namespace App\Facades;

use App\Entity\Language;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;

class LanguageFacade
{
	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var LanguageRepository */
	private $repository;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Language::class);
	}

	/**
	 * @param string $code
	 * @return Language|object|null
	 */
	public function getByCode(string $code)
	{
		return $this->repository->findOneBy(["code" => $code]);
	}

	/**
	 * @return Language[]
	 */
	public function getAll(): array
	{
		return $this->repository->findAll();
	}
}
