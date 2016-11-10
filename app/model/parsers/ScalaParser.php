<?php
namespace Zitkino\parsers;

/**
 * Scala Parser.
 */
class ScalaParser extends Parser {
	public function __construct() {
		$this->setUrl("http://www.kinoscala.cz/cz/program");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$events = $xpath->query("//div[@id='content']/table//tr");
		$days = 0;
		$movieItems = 0;
		foreach($events as $event) {
			$datetimeArray = [];
			
			if($event->getAttribute("class") === "day") {
				$dateQuery = $xpath->query("//tr[@class='day']//h2", $event);
				$dateFullString = explode(",", $dateQuery->item($days)->nodeValue);
				$dateString = explode(".", $dateFullString[1]);
				
				$day = $dateString[0];
				
				$monthString = $dateString[1];
				$monthArray = array("ledna","února","března","dubna","května","června","července","srpna","září","října","listopadu","prosince");
				$monthNumbers = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12");
				$month = str_replace($monthArray, $monthNumbers, $monthString);
				
				$year = date("Y");
				
				$date = trim($day).".".trim($month).".".$year;
				$days++;
			}
			else {
				$nameQuery = $xpath->query("//td[@class='col_movie_name']//a", $event);
				
				$timeQuery = $xpath->query("//td[@class='col_time_reservation']", $event);
				$time = explode(":", $timeQuery->item($movieItems)->nodeValue);
				
				$datetime = \DateTime::createFromFormat("j.n.Y", $date);
				$datetime->setTime(intval($time[0]), intval($time[1]));
				$datetimeArray[] = $datetime;
				
				$link = "http://www.kinoscala.cz".$nameQuery->item($movieItems)->getAttribute("href");
				
				$this->movies[] = new \Zitkino\Movie($nameQuery->item($movieItems)->nodeValue, $datetimeArray);
				$this->movies[count($this->movies)-1]->setLink($link);
				$movieItems++;
			}
		}
		
		$this->setMovies($this->movies);
	}
}
