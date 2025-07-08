<?php

namespace App\Service;

use App\Entity\Cinema;
use App\Entity\Movie;
use App\Entity\Place;
use App\Entity\Screening;
use App\Entity\ScreeningType;
use App\Repository\ScreeningRepository;
use App\Repository\ScreeningTypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class ScreeningFacade
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ScreeningRepository */
    private $repository;

    /** @var ScreeningTypeRepository */
    private $repositoryType;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Screening::class);
        $this->repositoryType = $entityManager->getRepository(ScreeningType::class);
    }

    /**
     * @param int $id
     * @return Screening|object|null
     */
    public function getById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * @param Cinema $cinema
     * @return Screening[]
     */
    public function getByCinema(Cinema $cinema): array
    {
        return $this->repository->findBy(["cinema" => $cinema]);
    }

    /**
     * @param Movie $movie
     * @return Screening[]
     */
    public function getByMovie(Movie $movie): array
    {
        return $this->repository->findBy(["movie" => $movie]);
    }

    /**
     * @param Place $place
     * @return Screening[]
     */
    public function getByPlace(Place $place): array
    {
        return $this->repository->findBy(["place" => $place]);
    }

    /**
     * @return Screening[]
     */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param string $code
     * @return ScreeningType|object|null
     */
    public function getTypeByCode(string $code)
    {
        return $this->repositoryType->findOneBy(["code" => $code]);
    }

    /**
     * Creates a new screening
     */
    public function create(Movie $movie, Cinema $cinema): Screening
    {
        $screening = new Screening($movie, $cinema);
        $this->entityManager->persist($screening);
        $this->entityManager->flush();
        
        return $screening;
    }

    /**
     * Removes all screenings for a cinema
     */
    public function removeScreenings(Cinema $cinema): void
    {
        $screenings = $this->getByCinema($cinema);
        
        foreach ($screenings as $screening) {
            $this->entityManager->remove($screening);
        }
        
        $this->entityManager->flush();
    }
}
