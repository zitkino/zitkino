<?php
namespace Zitkino\parsers;

/**
 * Art Parser.
 */
class ArtParser extends Parser {
	public function __construct() {
		$this->setUrl("http://kinoart.cz/program/");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$this->getHall($xpath, "leftcol distillery");
		//$this->getHall($xpath, "rightcol");
	}
	
	/**
	 * Parses data for selected hall.
	 * @param DOMXPath $xpath XPath document for parsing.
	 * @param string $which ID which hall to parse.
	 */
	public function getHall($xpath, $which) {
		$events = $xpath->query("//div[@id='content']//div[@class='".$which."']//tr");
		$movieItems = 0;
		foreach($events as $event) {
			$datetimes = [];
			
			$nameQuery = $xpath->query(".//td[@class='movie']//a", $event);
			$name = $nameQuery->item(0)->nodeValue;
			
			$link = $nameQuery->item(0)->getAttribute("href");
			
			$dateQuery = $xpath->query("//td[@class='date']", $event);
			$date = mb_substr($dateQuery->item($movieItems)->nodeValue, 3);
			$datetime = \DateTime::createFromFormat("j.m.H:i", $date);
			$datetimes[] = $datetime;
			
			/*$programmeUrl = $this->getUrl();
			$this->setUrl($link);
			$xpathCurrent = $this->downloadData();
			$screenings = $xpathCurrent->query("//div[@id='content']//div[@class='rightcol']//tr");
			$screeningItems = 0;
			foreach($screenings as $screening) {
				$dateQuery = $xpathCurrent->query("//td[@class='date']", $screening);
				$date = mb_substr($dateQuery->item($screeningItems)->nodeValue, 3);
				$datetimeCurrent = \DateTime::createFromFormat("j.m.H:i", $date);

				$language = null;
				$subtitles = null;
				if($datetimeCurrent == $datetime) {
					$languageQuery = $xpathCurrent->query(".//td[@class='sub']", $screening);
					var_dump($languageQuery->length);
					$languageString = $languageQuery->item(0)->nodeValue;
					var_dump($languageString);
					var_dump($screeningItems);
					var_dump($datetimeCurrent);
					echo "<br>";
					
					if((strpos($languageString, "CZ / ") !== false) or (strpos($languageString, "česky") !== false) or (strpos($languageString, "CZ dabing") !== false)) {
						$language = "česky";
					}
					if(strpos($languageString, "DE / ") !== false) {
						$language = "německy";
					}
					if(strpos($languageString, "EN / ") !== false) {
						$language = "anglicky";
					}
					if(strpos($languageString, "IT / ") !== false) {
						$language = "italsky";
					}
					if(strpos($languageString, "SWE / ") !== false) {
						$language = "švédsky";
					}
					
					if((strpos($languageString, "titulky CZ") !== false) or (strpos($languageString, "CZ titulky") !== false)) {
						$subtitles = "české";
					}
					if(strpos($languageString, "titulky EN") !== false) {
						$subtitles = "anglické";
					}
				}
				$screeningItems++;
			}
			$this->setUrl($programmeUrl);*/
			
			$this->movies[] = new \Zitkino\Movie($name, $datetimes);
			$this->movies[count($this->movies)-1]->setLink($link);
			//$this->movies[count($this->movies)-1]->setLanguage($language);
			//$this->movies[count($this->movies)-1]->setSubtitles($subtitles);
			$movieItems++;
			/*if($movieItems == 10) {
				break;
			}*/
		}
		
		$this->setMovies($this->movies);
	}
}
