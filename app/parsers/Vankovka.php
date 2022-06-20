<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\{OptimisticLockException, ORMException};
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Galerie Vaňkovka parser.
 */
class Vankovka extends Parser {
	/**
	 * @throws OptimisticLockException
	 * @throws ORMException
	 */
	public function parse(): void {
		$events = [
			["08.06.2022 10:00", "Croodsovi: Nový věk"],
			["08.06.2022 18:00", "Pulp Fiction: Historky z podsvětí"],
			["11.06.2022 10:00", "Maxinožka"],
			["11.06.2022 18:00", "Teorie tygra"],
			["15.06.2022 10:00", "Velká oříšková loupež 2"],
			["15.06.2022 18:00", "Nikdo"],
			["18.06.2022 10:00", "Jak vycvičit draka 3"],
			["18.06.2022 18:00", "Deníček moderního fotra"],
			["22.06.2022 10:00", "Trollové: Světové turné"],
			["22.06.2022 18:00", "Vlastníci"],
			["25.06.2022 10:00", "Psí veličenstvo"],
			["25.06.2022 18:00", "Prázdniny v Římě"]
		];
		
		foreach($events as $event) {
			$movie = $this->parserService->getMovieFacade()->getByName($event[1]);
			if(!isset($movie)) {
				$movie = new Movie($event[1]);
				$this->parserService->getMovieFacade()->save($movie);
			}
			
			$datetime = \DateTime::createFromFormat("d.m.Y H:i", $event[0]);
			$datetimes = [$datetime];
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setPrice(0.001)
				->setLink("https://www.galerie-vankovka.cz/novinky-a-akce-centra/letni-kino-e34068/")
				->setShowtimes($datetimes);
			
			$this->parserService->getScreeningFacade()->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
