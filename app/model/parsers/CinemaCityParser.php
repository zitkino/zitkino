<?php
namespace Zitkino\parsers;

/**
 * Cinema City Parser.
 */
abstract class CinemaCityParser extends Parser {
	private $cinemaId;
	
	function getCinemaId() {
		return $this->cinemaId;
	}

	function setCinemaId($cinemaId) {
		$this->cinemaId = $cinemaId;
	}
		
	public function __construct() {
		$datetime = new \DateTime();
		$date = $datetime->format("j/m/Y");
		
		$this->setUrl("http://www.cinemacity.cz/scheduleInfo?locationId=".$this->cinemaId."&date=".$date."&venueTypeId=1&hideSite=true&openedFromPopup=1&newwin=1");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$dateQuery = $xpath->query("//div[@id[starts-with(.,'scheduleInfo_')]]//label[@class='date']");
		$dateString = $dateQuery->item(0)->nodeValue;
		
		$events = $xpath->query("//table[@id[starts-with(.,'scheduleTable_')]]/tbody/tr");
		$movieItems = 0;
		foreach($events as $event) {
			$nameQuery = $xpath->query("//td[@class='featureName']//a", $event);
			$badEncoding = ["Ã¡", "Ä", "Ã©", "Ä", "Ã­", "Å", "Ãº", "Å¾"];
			$goodEncoding = ["á", "č", "é", "ě", "í", "ř", "ú", "ž"];
			$name = str_replace($badEncoding, $goodEncoding, $nameQuery->item($movieItems)->nodeValue);
			
			$link = "http://cinemacity.cz/".$nameQuery->item($movieItems)->getAttribute("href");
			
			$timeQuery = $xpath->query(".//td[@class='prsnt']/a", $event);			
			$datetimeArray = [];
			$i = 0;
			foreach($timeQuery as $timeElement) {
				$time = explode(":", trim($timeElement->textContent));
				
				$datetime = \DateTime::createFromFormat("j/m/Y", $dateString);
				$datetime->setTime(intval($time[0]), intval($time[1]));
				
				$datetimeArray[] = $datetime;
				$i++;
			}	
			
			$this->movies[] = new \Zitkino\Movie($name, $datetimeArray);
			$this->movies[count($this->movies)-1]->setLink($link);
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
