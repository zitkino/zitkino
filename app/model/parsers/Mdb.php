<?php
namespace Zitkino\parsers;

/**
 * Letní kino na Dvoře Městského divadla parser.
 */
class Mdb extends Parser {
	public function __construct() {
		$this->setUrl("http://www.letnikinobrno.cz/program-kina/");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$events = $xpath->query("//div[@id='right']//div[@class='program-item']");
		$days = 0;
		$movieItems = 0;
		foreach($events as $event) {
			$nameQuery = $xpath->query("//div[@class='program-title']//a", $event);
			$nameString = $nameQuery->item($movieItems)->nodeValue;
			$name = str_replace("(VSTUP ZDARMA)", "", $nameString);
			
			$link = "http://www.letnikinobrno.cz/program-kina/".$nameQuery->item($movieItems)->getAttribute("href");
			
			$infoQuery = $xpath->query("//div[@class='program-info']", $event);
			$infoString = $infoQuery->item($movieItems)->nodeValue;
			$info = explode(",", $infoString);
			
			$dubbing = null;
			if(strpos($infoString, "CZ znění") !== false) {
				$dubbing = "česky";
			}
			
			$subtitles = null;
			if(strpos($infoString, "CZ titulky") !== false) {
				$subtitles = "české";
			} elseif(strpos($infoString, "AN titulky") !== false) {
				$subtitles = "anglické";
			}
			
			$dateQuery = $xpath->query("//span[@class='program-date']", $event);
			$dateString = explode(", ", $dateQuery->item($days)->nodeValue);
			$datetimeString = explode(" ve ", $dateString[1]);
			
			$date = $datetimeString[0];
			$time = explode(":", $datetimeString[1]);
			
			$datetime = \DateTime::createFromFormat("d. m. Y", $date);
			if(isset($time[1])) {
				$datetime->setTime(intval($time[0]), intval($time[1]));
			} else {
				$datetime->setTime(21, 0);
			}
			$datetimes = [$datetime];
			
			$length = null;
			if(strpos($infoString, "min.") !== false) {
				$lengthString = str_replace("...více", "", $info[count($info)-1]);
				$length = str_replace("min.", "", $lengthString);
			}
			
			$movie = new \Zitkino\Movie($name, $datetimes);
			$movie->setLink($link);
			$movie->setDubbing($dubbing);
			$movie->setSubtitles($subtitles);
			$movie->setLength($length);
			$this->movies[] = $movie;
			
			$movieItems++;
			$days++;
		}
		
		$movieItems = 0;
		$prices = $xpath->query("//div[@id='right']//div[@class='price']");
		foreach($prices as $price) {
			$priceQuery = $xpath->query(".", $price); 
			$priceString = $priceQuery->item(0)->nodeValue;
			
			if(strpos($priceString, "ZDARMA") !== false) {
				$price = str_replace("ZDARMA", 0, $priceString);
			} else {
				$price = str_replace("Kč", "", $priceString);
			}
			
			$this->movies[$movieItems]->setPrice($price);
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
