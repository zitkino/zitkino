<?php

namespace App\Service;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;

class MovieFacade
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MovieRepository */
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Movie::class);
    }

    /**
     * @param string $name
     * @return Movie|object|null
     */
    public function getByName(string $name)
    {
        return $this->repository->findOneBy(["name" => $name]);
    }

    /**
     * @param int $id
     * @return Movie|object|null
     */
    public function getById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * @return Movie[]
     */
    public function getAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * Creates a new movie if it doesn't exist
     */
    public function createIfNotExists(string $name): Movie
    {
        $movie = $this->getByName($name);
        
        if ($movie === null) {
            $movie = new Movie($name);
            $this->entityManager->persist($movie);
            $this->entityManager->flush();
        }
        
        return $movie;
    }
}
