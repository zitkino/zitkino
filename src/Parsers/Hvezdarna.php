<?php
namespace App\Parsers;

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
			$movie = $this->parserService->builderService->movie(name: $item["name"], length: $item["length"], csfd: $item["csfd"]);
			
			$datetime = \DateTime::createFromFormat("Y-m-d H:i", trim($item["showtime"]));
			
			$screening = $this->parserService->builderService->screening(cinema: $this->cinema, movie: $movie, subtitles: "české", price: 0, link: "https://www.facebook.com/events/1235520100491317", showtimes: [$datetime]);
			
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->cinemaFacade->save($this->cinema);
	}
}
