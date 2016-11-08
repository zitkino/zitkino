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
		
		$i = 0;
		foreach($events as $event) {
			$movieEvent = $xpath->query("//p[@class='MsoNormal']", $event);
			if($movieEvent->length>4) {
				$nameQuery = $xpath->query($movieQuery."//h4", $event);
				if($i == 0) {
					$startCharacter = 9;
				}
				else {
					$startCharacter = 10;
				}
				$name = mb_substr($nameQuery->item($i)->nodeValue, $startCharacter);
				
				$link = "http://www.kinokavarna.cz/program.html";
				
				$dateQuery = $xpath->query($movieQuery."//h4//span", $event);
				$timeQuery = $xpath->query($movieQuery."//p[@class='start']", $event);
				$timeString = mb_substr($timeQuery->item($i)->nodeValue, 9, 5);
				$time = str_replace(".", ":", $timeString);
				
				$datetime = \DateTime::createFromFormat("j.n.Y", $dateQuery->item($i)->nodeValue);
				$datetime->setTime(intval(substr($time, 0, 2)), intval(substr($time, 3, 2)));
				
				$this->movies[] = new \Zitkino\Movie($name, $datetime);
				$this->movies[count($this->movies)-1]->setLink($link);
			}
			$i++;
		}
		
		$this->setMovies($this->movies);
	}
}
