<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;
use Zitkino\Movies\Movie;
use Zitkino\Place;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\Screenings;

/**
 * Art parser.
 */
class Art extends Parser {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		$this->setUrl("https://kinoart.cz/cs/program/");
		$this->initiateDocument();
		
		$this->parse();
	}
	
	public function parse(): Screenings {
		$xpath = $this->downloadData();
		
		$days = $xpath->query("//div[@class='events-calendar']//div[@class='grid events-calendar__day']");
		foreach($days as $day) {
			$dateQuery = $xpath->query(".//h2[@class='events-calendar__day-date']", $day);
			$dateArray = explode(" ", $dateQuery->item(0)->nodeValue);
			
			switch(true) {
				case (strpos($dateArray[1], "července") !== false):
					$month = 7;
					break;
				case (strpos($dateArray[1], "srpna") !== false):
					$month = 8;
					break;
				default:
					$month = null;
					break;
			}
			
			$events = $xpath->query(".//div[@class='events-calendar__events']//div[@class='events-calendar__event']", $day);
			foreach($events as $event) {
				$nameQuery = $xpath->query(".//h3[contains(@class, 'events-calendar__event-title')]//a", $event);
				$name = $nameQuery->item(0)->nodeValue;
				
				$link = $nameQuery->item(0)->getAttribute("href");
				
				$placeQuery = $xpath->query(".//p[@class='events-calendar__event-time--desktop']//a[@class='boxed boxed--custom']", $event);
				$placeName = $placeQuery->item(0)->nodeValue;
				$placeLink = $placeQuery->item(0)->getAttribute("href");
				
				$place = new Place($placeName);
				$place->setLink($placeLink);
				
				$timeQuery = $xpath->query(".//p[@class='events-calendar__event-time--desktop']//a[@class='boxed boxed--black']", $event);
				$time = $timeQuery->item(0)->nodeValue;
				
				$datetime = \DateTime::createFromFormat("j.m H:i", trim($dateArray[0].$month." ".$time));
				$datetimes = [$datetime];
				
				$languagesQuery = $xpath->query(".//div[@class='credits__event-movie-languages']//a", $event);
				$dubbing = "";
				$subtitles = "";
				for($i = 0; $i < $languagesQuery->length; $i++) {
					if($i == $languagesQuery->length - 1) {
						$subtitles = $languagesQuery->item($i)->nodeValue;
						break;
					}
					
					$dubbing .= $languagesQuery->item($i)->nodeValue." ";
				}
				
				$lengthQuery = $xpath->query(".//div[@class='credits__countries-year']//p[@class='credits__duration']", $event);
				$lengthString = $lengthQuery->item(0)->nodeValue;
				$length = str_replace("min", "", intval($lengthString));
				
				$movie = new Movie($name);
				$movie->setLength($length);
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setPlace($place);
				$screening->setLanguages($dubbing, $subtitles);
				$screening->setLink($link);
				$screening->setShowtimes($datetimes);
				
				$movie->addScreening($screening);
				$this->screenings[] = $screening;
			}
		}
		
		$this->setScreenings($this->screenings);
		return $this->screenings;
	}
}
