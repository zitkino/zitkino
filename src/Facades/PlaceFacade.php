<?php

namespace App\Facades;

use App\Entity\Cinema;
use App\Entity\Place;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;

class PlaceFacade
{
	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var PlaceRepository */
	private $repository;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->repository = $entityManager->getRepository(Place::class);
	}

	/**
	 * @param string $name
	 * @return Place|object|null
	 */
	public function getByName(string $name)
	{
		return $this->repository->findOneBy(["name" => $name]);
	}

	/**
	 * @param int $id
	 * @return Place|object|null
	 */
	public function getById(int $id)
	{
		return $this->repository->find($id);
	}

	/**
	 * @param Cinema $cinema
	 * @return Place[]
	 */
	public function getByCinema(Cinema $cinema): array
	{
		return $this->repository->findBy(["cinema" => $cinema]);
	}

	/**
	 * @return Place[]
	 */
	public function getAll(): array
	{
		return $this->repository->findAll();
	}

	/**
	 * Creates a new place if it doesn't exist
	 */
	public function createIfNotExists(string $name, Cinema $cinema): Place
	{
		$place = $this->getByName($name);
        
		if ($place === null) {
			$place = new Place($name);
			$place->setCinema($cinema);
			$this->entityManager->persist($place);
			$this->entityManager->flush();
		}
        
		return $place;
	}
}
