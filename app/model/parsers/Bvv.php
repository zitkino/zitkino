<?php
namespace Zitkino\parsers;

/**
 * BVV parser.
 */
class Bvv extends Parser {
	public function __construct() {
		$this->setUrl("http://www.bvv.cz/letni-kino/#Program");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		$events = "//div[@id='content']//div[@class='mainCol']//div[@class='documentText'][position()=2]";
		
		$titles = $xpath->query($events."//h2");
		foreach($titles as $title) {
			$items = explode(" ", $title->nodeValue, 4);
			
			$name = trim($items[3]);
			
			$date = $items[2]."2017";
			$datetime = \DateTime::createFromFormat("j.m.Y", $date);
			$datetime->setTime(20, 0);
			$datetimes = [$datetime];
			
			$price = 50;
			$freeStart = new \DateTime("2017-07-10");
			if($datetime < $freeStart) {
				$price = 0;
			}
			
			$this->movies[] = new \Zitkino\Movie($name, $datetimes);
			$this->movies[count($this->movies)-1]->setPrice($price);
		}
		
		$movieItems = 0;
		$informations = $xpath->query($events."//p");
		foreach($informations as $info) {
			$language = null;
			if((strpos($info->nodeValue, ", ČR,") !== false) or (strpos($info->nodeValue, "Česko") !== false)) {
				$language = "česky";
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
				
				$this->movies[$movieItems]->setLanguage($language);
				$this->movies[$movieItems]->setLength($length);
			}
			
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
