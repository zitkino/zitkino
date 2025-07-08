<?php
namespace _nette\app\Models\languages;

use _nette\app\Models\languages\Language;
use Dobine\Facades\DobineFacade;
use Nettrine\ORM\EntityManagerDecorator;

class LanguageFacade extends DobineFacade {
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Language::class);
	}
	
	/**
	 * @param int|string $id
	 * @return object|Language
	 */
	public function getById($id) {
		if(is_numeric($id)) {
			return $this->repository->findOneBy(["id" => $id]);
		} else {
			return $this->repository->findOneBy(["code" => $id]);
		}
	}
}
