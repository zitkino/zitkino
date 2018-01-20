<?php
namespace Zitkino\parsers;
use ICal\ICal;

/**
 * Stred parser.
 */
class Stred extends Parser {
	public function __construct() {
//		$this->setUrl("https://calendar.google.com/calendar/ical/n6a7pqdcgeprq9v7pf84dk3djo%40group.calendar.google.com/public/basic.ics");
		$this->setUrl("http://www.kinobude.cz");
		$this->getContent();
	}
	
	public function getContent() {
		$ical = new ICal();
		$ical->initUrl($this->getUrl());
		
		$events = $ical->eventsFromInterval("2 month");
		$movieItems = 0;
		foreach($events as $event) {
			$name = $event->summary;
			
			$items = explode(", ", $event->description);
			
			$dubbing = null;
			if(strpos($items[1], "Česko") !== false) {
				$dubbing = "česky";
			}
			
			$subtitles = null;
			if(strpos($event->description, "čes. tit.") !== false) {
				$subtitles = "české";
			}
			
			$datetime = $ical->iCalDateToDateTime($event->dtstart, true);
			$datetime->setTimezone(new \DateTimeZone("Europe/Prague"));
			$datetimes = [$datetime];
			
			$length = null;
			if(isset($items[3])) {
				$replacing = ["min", ".", ",", "čes tit"];
				
				$lengthQuery = [""];
				if(strpos($items[2], "min") !== false) {
					$lengthQuery = explode("R:", $items[2]);
				}
				if(strpos($items[3], "min") !== false) {
					$lengthQuery = explode("R:", $items[3]);
				}
				$length = str_replace($replacing, "", $lengthQuery[0]);
			}
			
			$price = 90;
			if(strpos($name, "Swingový večer") !== false) {
				$price = 50;
			}
			
			$movie = new \Zitkino\Movie($name, $datetimes);
			$movie->setDubbing($dubbing);
			$movie->setSubtitles($subtitles);
			$movie->setLength($length);
			$movie->setPrice($price);
			$this->movies[] = $movie;
			
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
