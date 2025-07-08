<?php

namespace App\Service;

use App\Entity\Cinema;
use App\Entity\CinemaType;
use App\Repository\CinemaRepository;
use Doctrine\ORM\EntityManagerInterface;

class CinemaFacade
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var CinemaRepository */
    private $repository;

    /** @var \Doctrine\ORM\EntityRepository */
    private $repositoryType;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Cinema::class);
        $this->repositoryType = $entityManager->getRepository(CinemaType::class);
    }

    /**
     * @param int|string $id
     * @return Cinema|object|null
     */
    public function getById($id)
    {
        if (is_numeric($id)) {
            return $this->repository->findOneBy(["id" => $id]);
        } else {
            return $this->repository->findOneBy(["code" => $id]);
        }
    }

    /**
     * @return CinemaType|object|null
     */
    public function getType(string $type)
    {
        return $this->repositoryType->findOneBy(["code" => $type]);
    }

    public function getAll(): array
    {
        return $this->repository->visible()->getQuery()->getResult();
    }

    public function getCurrent(): array
    {
        $qb = $this->repository->visible();

        $month = (int)date("m");
        if ($month < 6 or $month > 9) {
            $qb->join("c.type", "ct")
                ->andWhere("ct.code != :type")->setParameter("type", "summer");
        }

        return $qb->getQuery()->getResult();
    }

    public function getParsable(): array
    {
        return $this->repository->parsable()->getQuery()->getResult();
    }

    public function getByType(string $type): array
    {
        switch ($type) {
            case "all":
                return $this->getAll();
            case "current":
                return $this->getCurrent();
            default:
                return $this->repository->visible()->join("c.type", "ct")
                    ->andWhere("ct.code = :type")->setParameter("type", $type)
                    ->getQuery()->getResult();
        }
    }

    public function getWithMovies(string $type = "all"): array
    {
        $output = [];

        $cinemas = $this->getByType($type);
        /** @var Cinema $cinema */
        foreach ($cinemas as $cinema) {
            if ($cinema->hasScreenings()) {
                $output[] = $cinema;
            }
        }

        return $output;
    }
}
