<?php
namespace Zitkino;

use Dobine\Facades\DobineFacade;
use Doctrine\DBAL\{ConnectionException, Exception as DBALException};
use Doctrine\ORM\{EntityRepository, Mapping\ClassMetadata};
use Nette\Utils\Strings;
use Nettrine\ORM\EntityManagerDecorator;
use Tracy\Debugger;
use Zitkino\Cinemas\Cinema;
use Zitkino\Screenings\{Screening, ScreeningType};

/**
 * Class ScreeningFacade
 * @package Zitkino
 * @property EntityRepository $repository
 */
class ScreeningFacade extends DobineFacade {
	/** @var EntityRepository */
	private $repositoryType;
	
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Screening::class);
		$this->repositoryType = $this->entityManager->getRepository(ScreeningType::class);
		//$this->repositoryShowtime = $this->entityManager->getRepository(Showtime::class);
	}
	
	/**
	 * @return ScreeningType|object|null
	 */
	public function getType(?string $type = null) {
		if(empty($type)) {
			return $this->repositoryType->findOneBy(["code" => "2D"]);
		} else {
			return $this->repositoryType->findOneBy(["code" => Strings::webalize($type)]);
		}
	}
	
	public function removeScreenings(Cinema $cinema): int {
		return $this->repository->createQueryBuilder("s")->delete()
			->where("s.cinema = :cinema")->setParameter("cinema", $cinema)
			->getQuery()->getResult();
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
			Debugger::barDump($e);
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
