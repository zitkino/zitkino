<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\Screenings;
use Zitkino\Screenings\Showtime;

/**
 * Art parser.
 */
class Art extends Parser {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		$this->setUrl("http://kinoart.cz/program/");
		$this->initiateDocument();
		
		$this->parse();
	}
	
	public function parse(): Screenings {
		$xpath = $this->downloadData();
		
		$screenings = $this->getHall($xpath, "program");
		$this->setScreenings($screenings);
		return new Screenings($screenings);
	}

	/**
	 * Parses data for selected hall.
	 * @param \DOMXPath $xpath XPath document for parsing.
	 * @param string $which ID which hall to parse.
	 * @return array
	 */
	public function getHall($xpath, $which) {
		$movies = [];
		
		$events = $xpath->query("//div[@id='content']//div[@class='".$which."']//tr");
		$movieItems = 0;
		foreach($events as $event) {
			$datetimes = [];
			
			$nameQuery = $xpath->query(".//td[@class='movie']//a", $event);
			$name = $nameQuery->item(0)->nodeValue;
			
			$link = $nameQuery->item(0)->getAttribute("href");
			
			$dateQuery = $xpath->query(".//td[@class='date']", $event);
			$date = mb_substr($dateQuery->item(0)->nodeValue, 3);
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

				$dubbing = null;
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
						$dubbing = "česky";
					}
					if(strpos($languageString, "DE / ") !== false) {
						$dubbing = "německy";
					}
					if(strpos($languageString, "EN / ") !== false) {
						$dubbing = "anglicky";
					}
					if(strpos($languageString, "IT / ") !== false) {
						$dubbing = "italsky";
					}
					if(strpos($languageString, "SWE / ") !== false) {
						$dubbing = "švédsky";
					}
					
					if((strpos($languageString, "titulky CZ") !== false) or (strpos($languageString, "CZ titulky") !== false)) {
						$subtitles = "české";
					}
					if(strpos($dubbingString, "titulky EN") !== false) {
						$subtitles = "anglické";
					}
				}
				$screeningItems++;
			}
			$this->setUrl($programmeUrl);*/
			
			$priceReplace = ["akreditace celý festival", "Kč"];
			$priceQuery = $xpath->query(".//td[@class='price']//a", $event);
			$priceItem = $priceQuery->item(0);
			if(!isset($priceItem)) {
				$spanQuery = $xpath->query(".//td[@class='price']//span", $event);
				if($spanQuery->length > 0) {
					$priceReplace[] = $spanQuery->item(0)->nodeValue;
				}
				
				$priceQuery = $xpath->query(".//td[@class='price']", $event);
				$priceString = $priceQuery->item(0)->nodeValue;
			} else { $priceString = $priceItem->nodeValue; }
			
			if(strpos($priceString, "/") !== false) {
				$priceString = explode("/", $priceString)[0];
			}
			
			$price = trim(str_replace($priceReplace, "", $priceString));
			
			
			$movie = new Movie($name);
			
			$screening = new Screening($movie, $this->cinema);
//			$screening->setLanguages($dubbing, $subtitles);			
			$screening->price = $price;
			$screening->link = $link;
			$screening->setShowtimes($datetimes);
			
			$movie->addScreening($screening);
			
			$this->screenings[] = $screening;
			
			$movieItems++;
			/*if($movieItems == 10) {
				break;
			}*/
		}
		
		return $this->screenings;
	}
}
