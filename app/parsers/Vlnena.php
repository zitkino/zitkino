<?php
namespace Zitkino\Parsers;

use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Vlnena parser.
 */
class Vlnena extends Parser {
	public function parse(): void {
		$items = [
			["name" => "Pulp Fiction: Historky z podsvětí", "length" => 154, "csfd" => "https://www.csfd.cz/film/8852-pulp-fiction-historky-z-podsveti/", "showtime" => "2023-08-10 21:00", "link" => "https://www.facebook.com/events/1235520100491317/823431439143445"],
			["name" => "Vstupenka do ráje", "length" => 104, "csfd" => "https://www.csfd.cz/film/1014051-vstupenka-do-raje/", "showtime" => "2023-08-17 21:00", "link" => "https://www.facebook.com/events/1235520100491317/823431442476778"],
			["name" => "Dungeons & Dragons: Čest zlodějů", "length" => 134, "csfd" => "https://www.csfd.cz/film/579524-dungeons-dragons-cest-zlodeju/", "showtime" => "2023-08-24 21:00", "link" => "https://www.facebook.com/events/1235520100491317/823431452476777"],
			["name" => "Hanebný pancharti", "length" => 153, "csfd" => "https://www.csfd.cz/film/117077-hanebny-pancharti/", "showtime" => "2023-08-31 21:00", "link" => "https://www.facebook.com/events/1235520100491317/823431445810111"]
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
				->setLink($item["link"])
				->setShowtimes([$datetime]);
			
			$this->parserService->getScreeningFacade()->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
