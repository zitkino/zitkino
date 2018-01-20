<?php
namespace Zitkino\parsers;

/**
 * Spilberk parser.
 */
class Spilberk extends Parser {
	public function __construct() {
		$this->setUrl("http://www.letnikinospilberk.cz");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$events = $xpath->query("//div[@id='page-program']/div[@class='film']");
		foreach($events as $event) {
			$nameQuery = $xpath->query(".//div[@class='right']//h2", $event);
			$nameString = $nameQuery->item(0)->nodeValue;
			$name = str_replace([" - repríza"], "", $nameString);
			
			$csfdQuery = $xpath->query(".//a[@class='vice']", $event);
			$csfdItem = $csfdQuery->item(0);
			if(isset($csfdItem)) {
				$csfdString = $csfdItem->getAttribute("href");
				$csfd = str_replace(["https://www.csfd.cz/film/", "/prehled/"], "", $csfdString);
			} else { $csfd = null; }
			
			$itemsQuery = $xpath->query(".//div[@class='right']//p[@class='popisek']", $event);
			$itemString = $itemsQuery->item(0)->nodeValue;
			
			$dubbing = null;
			if(strpos($itemString, "Česko") !== false) {
				$dubbing = "český";
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
			$length = $lengthString[0];
			
			$priceQuery = $xpath->query(".//div[@class='right']//p[@class='cena']", $event);
			$priceString = $priceQuery->item(0)->nodeValue;
			$price = str_replace([",- Kč"], "", $priceString);
			
			$movie = new \Zitkino\Movie($name, $datetimes);
			$movie->setDubbing($dubbing);
			$movie->setLength($length);
			$movie->setPrice($price);
			$movie->setCsfd($csfd);
			$this->movies[] = $movie;
		}
		
		$this->setMovies($this->movies);
	}
}
