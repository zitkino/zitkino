<?php
namespace Zitkino\parsers;

/**
 * Letní kino na Dvoře Městského divadla parser.
 */
class Mdb extends Parser {
	public function __construct() {
		$this->setUrl("https://www.letnikinobrno.cz/program-kina/");
		$this->initiateDocument();
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$events = $xpath->query("//div[@class='wpb_wrapper']//div[@class='table-events-content']");
		foreach($events as $event) {
			$nameQuery = $xpath->query(".//div[@class='events-text1 events-text']//p[1]", $event);
			$name = $nameQuery->item(0)->nodeValue;
			
			$linkQuery = $xpath->query(".//div[@class='events-text1 events-text']//p[2]/a", $event);
			$linkItem = $linkQuery->item(0);
			$link = null;
			if($linkItem != null) {
				$link = $linkQuery->item(0)->getAttribute("href");
			}
			
			$dateQuery = $xpath->query(".//div[@class='events-text2 events-text']", $event);
			$days = ["PONDĚLÍ", "ÚTERÝ", "STŘEDA", "ČTVRTEK", "PÁTEK", "SOBOTA", "NEDĚLE"];
			$dateString = str_replace($days, "", $dateQuery->item(0)->nodeValue);
			
			$months = ["července", "červen", "srpna", "září"];
			$monthsNumbers = [7, 6, 8, 9];
			$date = trim(str_replace($months, $monthsNumbers, $dateString));
			
			$datetime = \DateTime::createFromFormat("d. m Y", $date);
			if($datetime != false) {
				$month = $datetime->format("m");
				switch ($month) {
					case "6": case "7": $datetime->setTime(21, 30); break;
					case "8": case "9": $datetime->setTime(21, 0); break;
				}
			}
			$datetimes = [$datetime];
			
			$price = 99;
			
			$movie = new \Zitkino\Movie($name, $datetimes);
			$movie->setLink($link);
			$movie->setPrice($price);
			$this->movies[] = $movie;
		}
		
		$this->setMovies($this->movies);
	}
}
