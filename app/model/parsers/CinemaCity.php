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
	
	public function getOneDay($datetime) {
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
		$movieItems = 0;
		foreach($events as $event) {
			$emptyQuery = $xpath->query(".//td[@class='empty']", $event);
			if($emptyQuery->length !== 0) {
				$this->movies = null;
				break;
			}
			else {
				$nameQuery = $xpath->query("//td[@class='featureName']//a", $event);
				$name = $nameQuery->item($movieItems)->nodeValue;
				
				$link = "http://cinemacity.cz/" . $nameQuery->item($movieItems)->getAttribute("href");
				
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
				
				$timeQuery = $xpath->query(".//td[@class='prsnt']/a", $event);
				$datetimes = [];
				$i = 0;
				foreach($timeQuery as $timeElement) {
					$time = explode(":", trim($timeElement->textContent));
					
					$datetime = \DateTime::createFromFormat("j/m/Y", $this->date);
					$datetime->setTime(intval($time[0]), intval($time[1]));
					
					$datetimes[] = $datetime;
					$i++;
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
				
				$this->movies[] = new \Zitkino\Movie($name, $datetimes);
				$this->movies[count($this->movies)-1]->setLink($link);
				$this->movies[count($this->movies)-1]->setType($type);
				$this->movies[count($this->movies)-1]->setLanguage($language);
				$this->movies[count($this->movies)-1]->setSubtitles($subtitles);
				$this->movies[count($this->movies)-1]->setLength($length);
				$this->movies[count($this->movies)-1]->setPrice($price);
				$movieItems++;
			}
		}
		
		$this->setMovies($this->movies);
	}
}
