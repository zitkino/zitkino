<?php

namespace App\Models\Page;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class PageRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Page::class);
	}
	
	/**
	 * @return Page[]
	 */
	public function grabAll(): array {
		return $this->findBy(["visible" => true], ["order" => "ASC"]);
	}
	
	public function grabByIdent(string $ident): ?Page {
		return $this->findOneBy(["ident" => $ident]);
	}
	
	public function save(Page $page): int {
		$this->getEntityManager()->persist($page);
		$page->mergeNewTranslations();
		$this->getEntityManager()->flush();
		
		return $page->getId();
	}
}
