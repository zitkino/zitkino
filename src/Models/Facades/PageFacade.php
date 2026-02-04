<?php
namespace App\Models\Facades;

use App\Models\Entities\Page;
use Dobine\Facades\DobineFacade;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class PageFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected EntityRepository $repository;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Page::class);
	}
	
	public function grabAll(): array {
		return $this->repository->findBy(["visible" => true], ["order" => "ASC"]);
	}
	
	public function grabByIdent(string $ident): ?Page {
		return $this->repository->findOneBy(["ident" => $ident]);
	}
	
	public function save($entity): int {
		$this->entityManager->persist($entity);
		$entity->mergeNewTranslations();
		$this->entityManager->flush();
		
		return $entity->getId();
	}
}
