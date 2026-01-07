<?php
namespace App\Parsers;

use App\Models\Entities\{Screening};
use App\Models\Entities\Movie;
use Doctrine\ORM\{OptimisticLockException, ORMException};

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
			$movie = $this->parserService->movieFacade->grabByName($event[1]);
			if(!isset($movie)) {
				$movie = new Movie($event[1]);
				$this->parserService->movieFacade->save($movie);
			}
			
			$datetime = \DateTime::createFromFormat("d.m.Y H:i", $event[0]);
			$datetimes = [$datetime];
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setPrice(0)
				->setLink("https://www.galerie-vankovka.cz/novinky-a-akce-centra/letni-kino-e34068/")
				->setShowtimes($datetimes);
			
			$this->parserService->screeningFacade->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->cinemaFacade->save($this->cinema);
	}
}
