<?php

namespace App\Models\Facades;

use App\Models\Entities\{Cinema, Movie, Place, Screening, ScreeningType};
use App\Models\Repositories\{ScreeningRepository};
use App\Models\Repositories\ScreeningTypeRepository;
use Dobine\Facades\DobineFacade;
use Doctrine\DBAL\{ConnectionException, Exception as DBALException};
use Doctrine\ORM\{EntityManagerInterface, EntityRepository, Mapping\ClassMetadata};
use Nette\Utils\Strings;

class ScreeningFacade extends DobineFacade {
	protected EntityManagerInterface $entityManager;
	
	protected ScreeningRepository|EntityRepository $repository;
	
	protected ScreeningTypeRepository|EntityRepository $repositoryType;
	
	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Screening::class);
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
	
	public function grabType(?string $type = null) {
		if(empty($type)) {
			return $this->repositoryType->findOneBy(["ident" => "2D"]);
		} else {
			return $this->repositoryType->findOneBy(["ident" => Strings::webalize($type)]);
		}
	}
	
	public function removeScreenings(Cinema $cinema): int {
		return $this->repository->createQueryBuilder("s")
			->delete()
			->where("s.cinema = :cinema")
			->setParameter("cinema", $cinema)
			->getQuery()
			->getResult();
	}
	
	/**
	 * Cleanup any needed table abroad TRUNCATE SQL function
	 * @throws DBALException
	 */
	public function truncateTable(string $className): bool {
		/** @var ClassMetadata $cmd */
		$cmd = $this->entityManager->getClassMetadata($className);
		$connection = $this->entityManager->getConnection();
		$connection->beginTransaction();
		
		try {
			$connection->query("SET FOREIGN_KEY_CHECKS=0");
			$connection->query("TRUNCATE TABLE ".$cmd->getTableName());
			$connection->query("SET FOREIGN_KEY_CHECKS=1");
			$connection->commit();
			$this->entityManager->flush();
		} catch(\Exception $e) {
			try {
				fwrite(STDERR, print_r("Can't truncate table ".$cmd->getTableName().". Reason: ".$e->getMessage(), true));
				$connection->rollback();
				return false;
			} catch(ConnectionException $connectionException) {
				fwrite(STDERR, print_r("Can't rollback truncating table ".$cmd->getTableName().". Reason: ".$connectionException->getMessage(), true));
				return false;
			}
		}
		return true;
	}
}
