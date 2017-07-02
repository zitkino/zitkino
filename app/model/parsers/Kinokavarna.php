<?php
namespace Zitkino\parsers;

/**
 * Kinokavarna parser.
 */
class Kinokavarna extends Parser {
	public function __construct() {
		$this->setUrl("http://www.kinokavarna.cz/program.html");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$movieQuery = "//div[@class='aktuality']";
		$events = $xpath->query("//div[@id='content-in']".$movieQuery);
		
		$movieItems = 0;
		foreach($events as $event) {
			$movieEvent = $xpath->query(".//p[@class='MsoNormal']", $event);
			//if($movieEvent->length>4) {
				$nameQuery = $xpath->query($movieQuery."//h4", $event);
				$name = $nameQuery->item($movieItems)->nodeValue;
				
				$link = "http://www.kinokavarna.cz/program.html";
				
				$language = null;
				$subtitles = null;
				foreach($movieEvent as $lang) {
					if(strpos($lang->nodeValue, ", ČR,") !== false) {
						$language = "česky";
						break;
					}
					if(strpos($lang->nodeValue, "čes. tit") !== false) {
						$subtitles = "české";
						break;
					}
				}
				
				$dateQuery = $xpath->query($movieQuery."//h4//span", $event);
				$date = $dateQuery->item($movieItems)->nodeValue;
				
				$name = mb_substr($name, strlen($date));
				$badNames = array("", "ZAVŘENO", "STÁTNÍ SVÁTEK- ZAVŘENO");
				if(in_array($name, $badNames)) {
					$movieItems++;
					continue;
				}
				
				$timeQuery = $xpath->query($movieQuery."//p[@class='start']", $event);
				$timeReplacing = array("Začátek: ", "od ");
				$timeString = str_replace($timeReplacing, "", $timeQuery->item($movieItems)->nodeValue);
				$time = str_replace(".", ":", mb_substr($timeString, 0, 5));
				
				$datetimes = [];
				$datetime = \DateTime::createFromFormat("j.n.Y", $date);
				$datetime->setTime(intval(substr($time, 0, 2)), intval(substr($time, 3, 2)));
				$datetimes[] = $datetime;
				
				$this->movies[] = new \Zitkino\Movie($name, $datetimes);
				$this->movies[count($this->movies)-1]->setLink($link);
				$this->movies[count($this->movies)-1]->setLanguage($language);
				$this->movies[count($this->movies)-1]->setSubtitles($subtitles);
			//}
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
