<?php
namespace Zitkino;

use Dobine\Facades\DobineFacade;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityRepository;
use Nettrine\ORM\EntityManagerDecorator;
use Tracy\Debugger;
use Zitkino\Cinemas\Cinema;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\ScreeningType;
use Zitkino\Screenings\Showtime;

/**
 * Class ScreeningFacade
 * @package Zitkino
 * @property EntityRepository $repository
 */
class ScreeningFacade extends DobineFacade {
	/** @var EntityRepository */
	private $repositoryType, $repositoryShowtime;
	
	public function __construct(EntityManagerDecorator $entityManager) {
		$this->entityManager = $entityManager;
		$this->repository = $this->entityManager->getRepository(Screening::class);
		$this->repositoryType = $this->entityManager->getRepository(ScreeningType::class);
		$this->repositoryShowtime = $this->entityManager->getRepository(Showtime::class);
	}
	
	/**
	 * @param string|null $type
	 * @return ScreeningType|object
	 */
	public function getType(?string $type = null) {
		if(!isset($type)) {
			return $this->repositoryType->findOneBy(["code" => "2D"]);
		} else {
			return $this->repositoryType->findOneBy(["code" => $type]);
		}
	}
	
	public function removeScreenings(Cinema $cinema) {
		return $this->repository->createQueryBuilder("s")->delete()
			->where("s.cinema = :cinema")->setParameter("cinema", $cinema)
			->getQuery()->getResult();
	}
	
	/**
	 * Cleanup any needed table abroad TRUNCATE SQL function
	 *
	 * @param string $className
	 * @return bool
	 */
	public function truncateTable(string $className): bool {
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
