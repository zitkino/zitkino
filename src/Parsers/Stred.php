<?php
namespace App\Parsers;

use App\Entities\{Movie, Screening, ScreeningType};
use App\Exceptions\ParserException;

/**
 * Stred parser.
 */
class Stred extends Parser {
	/**
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@class='contentContent']//a[@class='programPolozka row']");
		foreach($events as $event) {
			$linkQuery = $xpath->query(".", $event);
			$link = $linkQuery->item(0)->attributes->getNamedItem("href")->nodeValue;
			
			$timeQuery = $xpath->query(".//div[contains(@class, 'dateTime')]//div[@class='dayAndTime']", $event);
			$days = ["po, ", "út, ", "st, ", "čt, ", "pá, ", "so, ", "ne, "];
			$timeString = str_replace($days, "", $timeQuery->item(0)->nodeValue);
			$time = explode(":", $timeString);
			
			$dateQuery = $xpath->query(".//div[contains(@class, 'dateTime')]//div[@class='date']", $event);
			$dateString = $dateQuery->item(0)->nodeValue;
			
			$yearQuery = $xpath->query(".//div[contains(@class, 'dateTime')]//div[@class='year']", $event);
			$year = $yearQuery->item(0)->nodeValue;
			
			$date = $dateString." ".$year;
			
			$datetime = \DateTime::createFromFormat("d. m. Y", $date);
			$datetime->setTime(intval($time[0]), intval($time[1]));
			$datetimes = [$datetime];
			
			$nameQuery = $xpath->query(".//div[contains(@class, 'titleAndBasicInfo')]//h2", $event);
			$name = $nameQuery->item(0)->nodeValue;
			
			$metaQuery = $xpath->query(".//div[contains(@class, 'titleAndBasicInfo')]//div[@class='catalogMetas']", $event);
			$metaString = $metaQuery->item(0)->nodeValue;
			$meta = explode(",", $metaString);
			
			$length = null;
			if(isset($meta[3])) {
				$length = (int)str_replace("min", "", $meta[3]);
				if($length == 0) {
					$length = null;
				}
			}
			
			$dubbing = null;
			$subtitles = null;
			if(isset($meta[4])) {
				$l = explode(" / ", $meta[4]);
				if(count($l) == 2) {
					$meta[4] = $l[0];
					$meta[5] = $l[1];
				}
				
				$language[0] = trim($meta[4]);
				
				$dubbing = match (true) {
					str_contains($language[0], "CZ"), str_contains($language[0], "česky") => "česky",
					str_contains($language[0], "DE"), str_contains($language[0], "německy") => "německy",
					str_contains($language[0], "DA"), str_contains($language[0], "DN"), str_contains($language[0], "dánsky") => "dánsky",
					str_contains($language[0], "EN"), str_contains($language[0], "anglicky") => "anglicky",
					str_contains($language[0], "ES") => "španělsky",
					str_contains($language[0], "FA") => "persky",
					str_contains($language[0], "FR") => "francouzsky",
					str_contains($language[0], "HE") => "hebrejsky",
					str_contains($language[0], "NO") => "norsky",
					str_contains($language[0], "HU") => "maďarsky",
					str_contains($language[0], "IT") => "italsky",
					str_contains($language[0], "SW"), str_contains($language[0], "švédsky") => "švédsky",
					default => $language[0],
				};
			}
			
			if(count($meta) >= 5) {
				switch(true) {
					case (str_contains(end($meta), "CZ tit")):
					case (str_contains(end($meta), "CZE tit")):
					case (str_contains(end($meta), "CT tit")):
						$subtitles = "české";
						break;
				}
			}
			
			$price = 140;
			if(str_contains($name, "Swingový večer")) {
				$price = 50;
			}
			
			$cycleQuery = $xpath->query(".//div[contains(@class, 'icons_and_more')]//div[@class='cycle']", $event);
			$cycleItem = $cycleQuery->item(0);
			$cycle = "";
			if(isset($cycleItem)) {
				$cycle = $cycleItem->nodeValue;
				
				if(str_contains($cycle, "Das Sommerkino")) {
					$price = 50;
				}
				
				if(str_contains($cycle, "Vstup zdarma")) {
					$price = 0;
				}
			}
			
			$movie = $this->parserService->movieFacade->grabByName($name);
			if(!isset($movie)) {
				$movie = new Movie($name);
				$movie->setLength($length);
				$this->parserService->movieFacade->save($movie);
			}
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setLanguages($dubbing, $subtitles)
				->setPrice($price)
				->setLink($link)
				->setShowtimes($datetimes);
			
			$screeningType = $this->parserService->screeningFacade->grabType($cycle);
			if(!isset($screeningType)) {
				$screeningType = new ScreeningType($cycle);
				$this->parserService->screeningFacade->save($screeningType);
			}
			$screening->setType($screeningType);
			
			$this->parserService->screeningFacade->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->cinemaFacade->save($this->cinema);
	}
}
