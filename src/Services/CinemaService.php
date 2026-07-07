<?php

namespace App\Services;

use App\Models\Cinema\Cinema;
use App\Models\Screening\Screening;
use App\Models\Showtime\Showtime;
use App\Models\Cinema\CinemaRepository;
use Doctrine\ORM\EntityManagerInterface;

class CinemaService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly CinemaRepository $cinemaRepository,
	) {}

	/**
	 * Finds soonest screenings of movies from all visible cinemas in the next 4 hours.
	 * @return Screening[]
	 */
	public function getSoonestScreenings(): array {
		$now = new \DateTime();
		$nextFourHours = (new \DateTime())->modify("+4 hours");

		$cinemas = $this->cinemaRepository->grabVisible();
		if (empty($cinemas)) {
			return [];
		}

		$cinemaIds = array_map(fn(Cinema $cinema) => $cinema->getId(), $cinemas);

		$showtimes = $this->entityManager->createQueryBuilder()
			->select("s")
			->from(Showtime::class, "s")
			->join("s.screening", "sc")
			->join("sc.cinema", "c")
			->where("c.id IN (:cinemaIds)")
			->andWhere("s.datetime >= :now")
			->andWhere("s.datetime <= :nextFourHours")
			->setParameter("cinemaIds", $cinemaIds)
			->setParameter("now", $now)
			->setParameter("nextFourHours", $nextFourHours)
			->orderBy("s.datetime", "ASC")
			->getQuery()
			->getResult();

		if (empty($showtimes)) {
			$showtimes = [];
			foreach ($cinemas as $cinema) {
				$cinemaShowtimes = $this->entityManager->createQueryBuilder()
					->select("s")
					->from(Showtime::class, "s")
					->join("s.screening", "sc")
					->where("sc.cinema = :cinema")
					->andWhere("s.datetime >= :now")
					->setParameter("cinema", $cinema)
					->setParameter("now", $now)
					->orderBy("s.datetime", "ASC")
					->setMaxResults(2)
					->getQuery()
					->getResult();

				$showtimes = array_merge($showtimes, $cinemaShowtimes);
			}

			// Sort merged showtimes by datetime
			usort($showtimes, fn(Showtime $a, Showtime $b) => $a->datetime <=> $b->datetime);
		}

		$screenings = [];
		/** @var Showtime $showtime */
		foreach ($showtimes as $showtime) {
			$screening = $showtime->screening;
			if (!in_array($screening, $screenings, true)) {
				$screenings[] = $screening;
			}
		}

		return $screenings;
	}
}
