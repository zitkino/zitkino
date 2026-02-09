<?php
namespace App\Parsers;

use App\Exceptions\ParserException;

/**
 * Špilberk parser.
 */
class Spilberk extends Parser {
	/**
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@id='page-program']/div[@class='film typ-0']");
		foreach($events as $event) {
			$nameQuery = $xpath->query(".//h2", $event);
			$nameString = $nameQuery->item(0)->nodeValue;
			$name = str_replace([" - repríza"], "", $nameString);
			
			$csfd = null;
			$csfdQuery = $xpath->query(".//p[@class='csfd']//a", $event);
			$csfdItem = $csfdQuery->item(0);
			if(isset($csfdItem)) {
				$csfdString = $csfdItem->attributes->getNamedItem("href")->nodeValue;
				$csfd = str_replace(["https://www.csfd.cz/film/", "/prehled/"], "", $csfdString);
			}
			
			$linkQuery = $xpath->query(".//a[@class='goout']", $event);
			$linkItem = $linkQuery->item(0);
			$link = $linkItem?->attributes->getNamedItem("href")->nodeValue;
			
			$descriptionQuery = $xpath->query(".//p[@class='popisek']", $event);
			$descriptionItem = $descriptionQuery->item(0);
			if(isset($descriptionItem)) {
				$descriptionString = $descriptionItem->nodeValue;
				switch(true) {
					case(str_contains($descriptionString, "Česko")):
					case(str_contains($descriptionString, "český dabing")):
						$dubbing = "český";
						$subtitles = null;
						break;
					case(str_contains($descriptionString, "čes. titulky")):
						$dubbing = null;
						$subtitles = "české";
						break;
					default:
						$dubbing = null;
						$subtitles = null;
						break;
				}
				
				$lengthString = explode("min", $descriptionString);
				$length = (int)trim($lengthString[0]);
				if($length == 0) {
					$length = null;
				}
			} else {
				$dubbing = null;
				$subtitles = null;
				$length = null;
			}
			
			$dateQuery = $xpath->query(".//div[@class='left']//p", $event);
			$dateString = $dateQuery->item(0)->nodeValue;
			$date = rtrim(substr($dateString, 0, 6));
			
			$timeString = substr($dateString, -5);
			$time = explode(":", $timeString);
			
			$datetime = \DateTime::createFromFormat("j. n.", $date);
			$datetime->setTime(intval($time[0]), intval($time[1]));
			$datetimes = [$datetime];
			
			$priceQuery = $xpath->query(".//p[@class='cena']", $event);
			$priceString = $priceQuery->item(0)->nodeValue;
			$price = (int)str_replace(["na místě", ",- Kč"], "", $priceString);
			
			$movie = $this->parserService->builderService->movie(name: $name, length: $length, csfd: $csfd);
			$screening = $this->parserService->builderService->screening(cinema: $this->cinema, movie: $movie, dubbing: $dubbing, subtitles: $subtitles, price: $price, link: $link, showtimes: $datetimes);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->parsed = new \DateTime();
		$this->parserService->cinemaFacade->save($this->cinema);
	}
}
