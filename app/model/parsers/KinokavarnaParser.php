<?php
namespace Zitkino\parsers;

/**
 * Kinokavarna Parser.
 */
class KinokavarnaParser extends Parser {
	public function __construct() {
		$this->setUrl("http://www.kinokavarna.cz/program.html");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$movieQuery = "//div[@class='aktuality'][count(p)>4]";
		$events = $xpath->query("//div[@id='content-in']".$movieQuery);
		
		$movieItems = 0;
		foreach($events as $event) {
			$movieEvent = $xpath->query("//p[@class='MsoNormal']", $event);
			if($movieEvent->length>4) {
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
					if(strpos($lang->nodeValue, "čes. tit.") !== false) {
						$subtitles = "české";
						break;
					}
				}
				
				$dateQuery = $xpath->query($movieQuery."//h4//span", $event);
				$date = $dateQuery->item($movieItems)->nodeValue;
				
				$timeQuery = $xpath->query($movieQuery."//p[@class='start']", $event);
				$timeString = mb_substr($timeQuery->item($movieItems)->nodeValue, 9, 5);
				$time = str_replace(".", ":", $timeString);
				
				$datetimes = [];
				$datetime = \DateTime::createFromFormat("j.n.Y", $date);
				$datetime->setTime(intval(substr($time, 0, 2)), intval(substr($time, 3, 2)));
				$datetimes[] = $datetime;
				
				$name = mb_substr($name, strlen($date));
				
				$this->movies[] = new \Zitkino\Movie($name, $datetimes);
				$this->movies[count($this->movies)-1]->setLink($link);
				$this->movies[count($this->movies)-1]->setLanguage($language);
				$this->movies[count($this->movies)-1]->setSubtitles($subtitles);
			}
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
