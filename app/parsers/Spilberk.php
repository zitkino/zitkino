<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\{OptimisticLockException, ORMException};
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Špilberk parser.
 */
class Spilberk extends Parser {
	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
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
			
			$link = null;
			$linkQuery = $xpath->query(".//a[@class='goout']", $event);
			$linkItem = $linkQuery->item(0);
			if(isset($linkItem)) {
				$link = $linkItem->attributes->getNamedItem("href")->nodeValue;
			}
			
			$itemsQuery = $xpath->query(".//p[@class='popisek']", $event);
			$itemString = $itemsQuery->item(0)->nodeValue;
			
			switch(true) {
				case(strpos($itemString, "Česko") !== false):
				case(strpos($itemString, "český dabing") !== false):
					$dubbing = "český";
					$subtitles = null;
					break;
				case(strpos($itemString, "čes. titulky") !== false):
					$dubbing = null;
					$subtitles = "české";
					break;
				default:
					$dubbing = null;
					$subtitles = null;
					break;
			}
			
			$dateQuery = $xpath->query(".//div[@class='left']//p", $event);
			$dateString = $dateQuery->item(0)->nodeValue;
			$date = rtrim(substr($dateString, 0, 6));
			
			$timeString = substr($dateString, -5);
			$time = explode(":", $timeString);
			
			$datetime = \DateTime::createFromFormat("j. n.", $date);
			$datetime->setTime(intval($time[0]), intval($time[1]));
			$datetimes = [$datetime];
			
			$lengthString = explode("min", $itemString);
			$length = (int)trim($lengthString[0]);
			if($length == 0) {
				$length = null;
			}
			
			$priceQuery = $xpath->query(".//p[@class='cena']", $event);
			$priceString = $priceQuery->item(0)->nodeValue;
			$price = (int)str_replace(["na místě", ",- Kč"], "", $priceString);
			
			$movie = $this->parserService->getMovieFacade()->getByName($name);
			if(!isset($movie)) {
				$movie = new Movie($name);
				$movie->setLength($length)
					->setCsfd($csfd);
				$this->parserService->getMovieFacade()->save($movie);
			}
			
			if(!empty($csfd) and empty($movie->getCsfd())) {
				$movie->setCsfd($csfd);
				$this->parserService->getMovieFacade()->save($movie);
			}
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setLanguages($dubbing, $subtitles)
				->setPrice($price)
				->setLink($link)
				->setShowtimes($datetimes);
			
			$this->parserService->getScreeningFacade()->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
