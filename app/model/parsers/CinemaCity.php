<?php
namespace Zitkino\parsers;

/**
 * Cinema City parser.
 */
abstract class CinemaCity extends Parser {
	private $cinemaId;
	private $date;
	
	function getCinemaId() {
		return $this->cinemaId;
	}
	
	function setCinemaId($cinemaId) {
		$this->cinemaId = $cinemaId;
	}
	
	public function __construct() {
		$datetime = new \DateTime();
		$this->getOneDay($datetime);
		
		$datetime->modify("+1 days");
		$this->getOneDay($datetime);
	}
	
	public function getOneDay(\DateTime $datetime) {
		$this->date = $datetime->format("j/m/Y");
		
		$dateUrl = str_replace("/", "%2F", $this->date);
		$this->setUrl("http://www.cinemacity.cz/scheduleInfo?locationId=".$this->cinemaId."&date=".$dateUrl."&venueTypeId=1&hideSite=true&openedFromPopup=1&newwin=1");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		/*dateQuery = $xpath->query("//div[@id[starts-with(.,'scheduleInfo_')]]//label[@class='date']");
		$dateString = $dateQuery->item(0)->nodeValue;*/
		
		$events = $xpath->query("//table[@id[starts-with(.,'scheduleTable_')]]/tbody/tr");
		foreach($events as $event) {
			$emptyQuery = $xpath->query(".//td[@class='empty']", $event);
			if($emptyQuery->length !== 0) {
				break;
			} else {
				$nameQuery = $xpath->query(".//td[@class='featureName']//a", $event);
				$name = $nameQuery->item(0)->nodeValue;
				
				$link = "http://cinemacity.cz/" . $nameQuery->item(0)->getAttribute("href");
				
				$type = null;
				if(strpos($name, "3D") !== false) {
					$type = "3D";
					$name = str_replace(" 3D", "", $name);
				}
				
				$languageQuery = $xpath->query(".//td[4]", $event);
				$language = $languageQuery->item(0)->nodeValue;
				switch($language) {
					case "CZ": $language = "česky"; break;
					case "EN": $language = "anglicky"; break;
					case "FR": $language = "francouzsky"; break;
				}
				
				$subtitlesQuery = $xpath->query(".//td[3]", $event);
				$subtitles = $subtitlesQuery->item(0)->nodeValue;
				if(strpos($subtitles, "ČT") !== false) {
					$subtitles = "české";
				}
				if((strpos($subtitles, "DAB") !== false) or (strpos($subtitles, "CZ") !== false)) {
					$subtitles = null;
					$language = "česky";
				}
				if(strpos($subtitles, "---") !== false) {
					$subtitles = null;
				}
				
				$timeQuery = $xpath->query(".//td[@class='prsnt']/a", $event);
				$datetimes = [];
				foreach($timeQuery as $timeElement) {
					$time = explode(":", trim($timeElement->textContent));
					
					$datetime = \DateTime::createFromFormat("j/m/Y", $this->date);
					$datetime->setTime(intval($time[0]), intval($time[1]));
					
					$datetimes[] = $datetime;
				}
				
				$lengthQuery = $xpath->query(".//td[5]", $event);
				$length = $lengthQuery->item(0)->nodeValue;
				
				$dayOfWeek = $datetime->format("w");
				if($dayOfWeek == 1) {
					$price = 154;
					if($type == "3D") { $price = 199; }
				} else {
					$price = 184;
					if($type == "3D") { $price = 229; }
				}
				
				$movie = new \Zitkino\Movie($name, $datetimes);
				$movie->setLink($link);
				$movie->setType($type);
				$movie->setLanguage($language);
				$movie->setSubtitles($subtitles);
				$movie->setLength($length);
				$movie->setPrice($price);
				$this->movies[] = $movie;
			}
		}
		
		$this->setMovies($this->movies);
	}
}
