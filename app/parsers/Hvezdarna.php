<?php
namespace Zitkino\Parsers;

use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Hvezdarna parser.
 */
class Hvezdarna extends Parser {
	public function parse(): void {
		$items = [
			["name" => "Vetřelci", "length" => 137, "csfd" => "https://www.csfd.cz/film/1245-vetrelci/", "showtime" => "2023-08-06 21:00"],
			["name" => "Terminátor 2: Den zúčtování", "length" => 137, "csfd" => "https://www.csfd.cz/film/1248-terminator-2-den-zuctovani/", "showtime" => "2023-08-07 21:00"],
			["name" => "Pán prstenů: Dvě věže", "length" => 172, "csfd" => "https://www.csfd.cz/film/4713-pan-prstenu-dve-veze/", "showtime" => "2023-08-08 21:00"]
		];
		
		foreach($items as $item) {
			$movie = $this->parserService->getMovieFacade()->getByName($item["name"]);
			if(!isset($movie)) {
				$movie = new Movie($item["name"]);
			}
			$movie->setLength($item["length"])->setCsfd($item["csfd"]);
			$this->parserService->getMovieFacade()->save($movie);
			
			$datetime = \DateTime::createFromFormat("Y-m-d H:i", trim($item["showtime"]));
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setLanguages(null, "české")
				->setPrice(0)
				->setLink("https://www.facebook.com/events/1235520100491317")
				->setShowtimes([$datetime]);
			
			$this->parserService->getScreeningFacade()->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
