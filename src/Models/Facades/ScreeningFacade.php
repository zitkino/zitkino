<?php

namespace App\Models\Facades;

use App\Models\Entities\{Cinema, Movie, Place, Screening, ScreeningFormat, ScreeningType};
use Dobine\Facades\DobineFacade;
use Dobine\Utils\Strings;
use Doctrine\ORM\{EntityManagerInterface, EntityRepository};

class ScreeningFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected EntityRepository $repository;
	
	protected EntityRepository $repositoryFormat;
	
	protected EntityRepository $repositoryType;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Screening::class);
		$this->repositoryFormat = $entityManager->getRepository(ScreeningFormat::class);
		$this->repositoryType = $entityManager->getRepository(ScreeningType::class);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByCinema(Cinema $cinema): array {
		return $this->repository->findBy(["cinema" => $cinema]);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByMovie(Movie $movie): array {
		return $this->repository->findBy(["movie" => $movie]);
	}
	
	/**
	 * @return Screening[]
	 */
	public function grabByPlace(Place $place): array {
		return $this->repository->findBy(["place" => $place]);
	}
	
	public function grabFormat(?string $format = null): ?ScreeningFormat {
		if(empty($format)) {
			return null;
		}
		return $this->repositoryFormat->findOneBy(["ident" => Strings::identify($format)]);
	}
	
	public function grabType(?string $type = null): ?ScreeningType {
		if(empty($type)) {
			return null;
		}
		return $this->repositoryType->findOneBy(["ident" => Strings::identify($type)]);
	}
	
	public function removeScreenings(Cinema $cinema): int {
		return $this->repository->createQueryBuilder("s")
			->delete()
			->where("s.cinema = :cinema")
			->setParameter("cinema", $cinema)
			->getQuery()
			->getResult();
	}
}
