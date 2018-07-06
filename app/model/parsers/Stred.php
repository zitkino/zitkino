<?php
namespace Zitkino\parsers;
use ICal\ICal;

/**
 * Stred parser.
 */
class Stred extends Parser {
	public function __construct() {
		$this->setUrl("http://kinobude.cz/program/");
		$this->initiateDocument();
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
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
			
			$length = str_replace("min", "", $meta[3]);
			
			$language = explode(" / ", $meta[4]);
			
			$dubbing = null;
			switch (true) {
				case (strpos($language[0], "CZ") !== false): $dubbing = "česky"; break;
				case (strpos($language[0], "DA") !== false): $dubbing = "dánsky"; break;
				case (strpos($language[0], "DE") !== false): $dubbing = "německy"; break;
				case (strpos($language[0], "DN") !== false): $dubbing = "dánsky"; break;
				case (strpos($language[0], "EN") !== false): $dubbing = "anglicky"; break;
				case (strpos($language[0], "ES") !== false): $dubbing = "španělsky"; break;
				case (strpos($language[0], "FA") !== false): $dubbing = "persky"; break;
				case (strpos($language[0], "FR") !== false): $dubbing = "francouzsky"; break;
				case (strpos($language[0], "HE") !== false): $dubbing = "hebrejsky"; break;
				case (strpos($language[0], "NO") !== false): $dubbing = "norsky"; break;
				case (strpos($language[0], "HU") !== false): $dubbing = "maďarsky"; break;
				case (strpos($language[0], "IT") !== false): $dubbing = "italsky"; break;
				case (strpos($language[0], "SW") !== false): $dubbing = "švédsky"; break;
			}
			
			$subtitles = null;
			if(isset($language[1]) and (strpos($language[1], "CZ tit") !== false or strpos($language[1], "CZE tit") !== false)) {
				$subtitles = "české";
			}
			
			$price = 90;
			if(strpos($name, "Swingový večer") !== false) {
				$price = 50;
			}
			
			$movie = new \Zitkino\Movie($name, $datetimes);
			$movie->setLink($link);
			$movie->setDubbing($dubbing);
			$movie->setSubtitles($subtitles);
			$movie->setLength($length);
			$movie->setPrice($price);
			$this->movies[] = $movie;
		}
		
		$this->setMovies($this->movies);
	}
}
