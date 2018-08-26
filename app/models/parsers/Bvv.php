<?php
namespace Zitkino\Parsers;

use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\Showtime;

/**
 * BVV parser.
 */
class Bvv extends Parser {
	public function __construct() {
		$this->setUrl("http://www.bvv.cz/letni-kino/");
		$this->initiateDocument();
		
		$this->parse();
	}
	
	public function parse() {
		$xpath = $this->downloadData();
		$events = "//*[@id='content']/div[1]/div[2]";
		
		$movieItems = 0;
		$titles = $xpath->query($events."//h2");
		foreach($titles as $title) {
			if(!isset($title->nodeValue) or empty(trim($title->nodeValue))) {
				continue;
			}
			
			$value = str_replace(["&nbsp;", "  ", " "]," ", $title->nodeValue);
			$items = preg_split("/\s+/", $value, 4); //explode(" ", $title->nodeValue, 4);
			
			$nameQuery = $xpath->query(".//a", $title);
			$nameItem = $nameQuery->item(0);
			if(isset($nameItem)) {
				$name = $nameItem->nodeValue;
			} else { $name = null; continue; }
			
			$dateString = "";
			foreach($items as $item) {
				$dateString = trim($item, "\xC2\xA0");
				$date = \DateTime::createFromFormat("j.m.", $dateString);
				
				if($date !== false) {
					$dateString = $dateString."2017";
					break;
				}
			}
			
			$datetime = \DateTime::createFromFormat("j.m.Y", $dateString);
			if($datetime !== false) {
				$datetime->setTime(20, 0);
			}
			$datetimes = [$datetime];
			
			$price = 50;
			$freeStart = new \DateTime("2017-07-10");
			if($datetime < $freeStart) {
				$price = 0;
			}
			
			$csfdItem = $nameQuery->item(0);
			if(isset($csfdItem)) {
				$csfdString = $csfdItem->getAttribute("href");
				$csfd = str_replace("https://www.csfd.cz/film/", "", $csfdString);	
			} else { $csfd = null; }
			
			$movie = new Movie($name);
//			$movie->csfd = $csfd;
			
			$screening = new Screening();
			$screening->price = $price;
			
			foreach($datetimes as $datetime) {
				$showtime = new Showtime();
				$showtime->screening = $screening;
				$showtime->datetime = $datetime;
				$screening->addShowtime($showtime);
			}
			
			$movie->addScreening($screening);
			$this->screenings[] = $movie;
			
			$movieItems++;
		}
		
		$movieItems = 0;
		$informations = $xpath->query($events."//p");
		foreach($informations as $info) {
			if(!isset($info->nodeValue) or empty(trim($info->nodeValue))) {
				continue;
			}
			
			$dubbing = null;
			if((strpos($info->nodeValue, ", ČR,") !== false) or (strpos($info->nodeValue, "Česko") !== false)) {
				$dubbing = "česky";
			}
			
			$items = explode(", ", $info->nodeValue);
			
			$length = null;
			if(count($items) > 2) {
				$lengthItems = [1, 2, 3, 4, 5];
				foreach($lengthItems as $item) {
					if(isset($items[$item]) and (strpos($items[$item], " min") !== false)) {
						$lengthArray = explode(" ", $items[$item]);
						
						if(strpos($lengthArray[0], ",") !== false) {
							$length = $lengthArray[2];
						} else { $length = $lengthArray[0]; }
						
						break;
					}
				}
				
				$this->screenings[$movieItems]->screenings[0]->dubbing = $dubbing;
				$this->screenings[$movieItems]->screenings[0]->length = $length;
			}
			
			$movieItems++;
		}
		
		$this->setScreenings($this->screenings);
	}
}
