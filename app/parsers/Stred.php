<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Zitkino\Cinemas\Cinema;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\ScreeningType;

/**
 * Stred parser.
 */
class Stred extends Parser {
	/**
	 * Stred constructor.
	 * @param ParserService $parserService
	 * @param Cinema $cinema
	 */
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->setUrl("http://kinobude.cz/program/");
	}
	
	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@class='contentContent']//a[@class='programPolozka row']");
		foreach($events as $event) {
			$linkQuery = $xpath->query(".", $event);
			$link = $linkQuery->item(0)->getAttribute("href");
			
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
				$length = str_replace("min", "", intval($meta[3]));
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
				
				switch(true) {
					case (strpos($language[0], "CZ") !== false):
					case (strpos($language[0], "česky") !== false):
						$dubbing = "česky";
						break;
					case (strpos($language[0], "DE") !== false):
					case (strpos($language[0], "německy") !== false):
						$dubbing = "německy";
						break;
					case (strpos($language[0], "DA") !== false):
					case (strpos($language[0], "DN") !== false):
					case (strpos($language[0], "dánsky") !== false):
						$dubbing = "dánsky";
						break;
					case (strpos($language[0], "EN") !== false):
					case (strpos($language[0], "anglicky") !== false):
						$dubbing = "anglicky";
						break;
					case (strpos($language[0], "ES") !== false):
						$dubbing = "španělsky";
						break;
					case (strpos($language[0], "FA") !== false):
						$dubbing = "persky";
						break;
					case (strpos($language[0], "FR") !== false):
						$dubbing = "francouzsky";
						break;
					case (strpos($language[0], "HE") !== false):
						$dubbing = "hebrejsky";
						break;
					case (strpos($language[0], "NO") !== false):
						$dubbing = "norsky";
						break;
					case (strpos($language[0], "HU") !== false):
						$dubbing = "maďarsky";
						break;
					case (strpos($language[0], "IT") !== false):
						$dubbing = "italsky";
						break;
					case (strpos($language[0], "SW") !== false):
					case (strpos($language[0], "švédsky") !== false):
						$dubbing = "švédsky";
						break;
					default:
						$dubbing = $language[0];
						break;
				}
			}
			
			if(count($meta) >= 5) {
				switch(true) {
					case (strpos(end($meta), "CZ tit") !== false):
					case (strpos(end($meta), "CZE tit") !== false):
					case (strpos(end($meta), "CT tit") !== false):
						$subtitles = "české";
						break;
				}
			}
			
			$price = 100;
			if(strpos($name, "Swingový večer") !== false) {
				$price = 50;
			}
			
			$cycleQuery = $xpath->query(".//div[contains(@class, 'icons_and_more')]//div[@class='cycle']", $event);
			$cycleItem = $cycleQuery->item(0);
			$cycle = "";
			if(isset($cycleItem)) {
				$cycle = $cycleItem->nodeValue;
				
				if(strpos($cycle, "Das Sommerkino") !== false) {
					$price = 50;
				}
			}
			
			$movie = $this->parserService->getMovieFacade()->getByName($name);
			if(!isset($movie)) {
				$movie = new Movie($name);
				$movie->setLength($length);
				$this->parserService->getMovieFacade()->save($movie);
			}
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setLanguages($dubbing, $subtitles);
			$screening->setPrice($price);
			$screening->setLink($link);
			$screening->setShowtimes($datetimes);
			
			$screeningType = $this->parserService->getScreeningFacade()->getType($cycle);
			if(!isset($screeningType)) {
				$screeningType = new ScreeningType($cycle);
				$this->parserService->getScreeningFacade()->save($screeningType);
			}
			$screening->setType($screeningType);
			
			$this->parserService->getEntityManager()->persist($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getEntityManager()->persist($this->cinema);
		$this->parserService->getEntityManager()->flush();
	}
}
