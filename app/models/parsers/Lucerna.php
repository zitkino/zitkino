<?php
namespace Zitkino\Parsers;

/**
 * Lucerna parser.
 */
class Lucerna extends Parser {
	public function __construct() {
		$this->setUrl("http://www.kinolucerna.info");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$days = $xpath->query("//ul[@id='table_days']//div[@class='scroll-pane-wrapper']//li");
		foreach($days as $day) {
			$dayQuery = $xpath->query("./a", $day);
			$dayId = $dayQuery->item(0)->getAttribute("data-den");
			$dayString = $dayQuery->item(0)->getElementsByTagName("span")->item(0)->nodeValue;
			
			$events = $xpath->query("//div[@id='den_".$dayId."']//div[@class='item']");
			foreach($events as $key => $event) {
				$info = "./div[@class='heading']";
				
				$nameQuery = $xpath->query($info."//h2//a", $event);
				$name = $nameQuery->item(0)->nodeValue;
				
				$link = "http://www.kinolucerna.info".$nameQuery->item(0)->getAttribute("href");
				
				$lengthQuery = $xpath->query($info."//div[@class='eventlenght']", $event);
				$lengthString = $lengthQuery->item(0)->nodeValue;
				$length = str_replace("&nbsp;min", "", htmlentities($lengthString, null, "utf-8"));
				
				$typeQuery = $xpath->query($info."//div[@class='left']/div/span[1]", $event);
				if($typeQuery->length >= 1) {
					$typeString = $typeQuery->item(0)->nodeValue;
					if($typeString == "3D") {
						$type = $typeString;
					} else {
						$type = null;
					}	
				}
				
				$languageQuery = $xpath->query($info."//div[@class='left']/div/span[2]", $event);
				if($languageQuery->length >= 1) {
					$languageString = $languageQuery->item(0)->nodeValue;
					switch(true) {
						case stripos($languageString, "ČV") !== false: $dubbing = "česky"; $subtitles = null; break;
						case stripos($languageString, "ČT") !== false: $dubbing = null; $subtitles = "české"; break;
						case stripos($languageString, "ČD") !== false: $dubbing = "česky"; $subtitles = null; break;
						case stripos($languageString, "anglicka_verzia_ceske_titulky") !== false: $dubbing = "anglicky"; $subtitles = "české"; break;
						case stripos($languageString, "Anglická verze s českými titulky") !== false: $dubbing = "anglicky"; $subtitles = "české"; break;
						default: $dubbing = null; $subtitles = null;
					}
				}
				
				$datetimes = [];
				$timesQuery = $xpath->query("./div[@class='times']/div[@class='right']/span", $event);
				/** @var \DOMElement $timeElement */
				foreach($timesQuery as $timeElement) {
					$timeString = $timeElement->nodeValue;
					$time = explode(":", $timeString);
					
					$datetime = \DateTime::createFromFormat("j.m.", $dayString);
					$datetime->setTime((int)$time[0], (int)$time[1]);
					$datetimes[] = $datetime;
					
					$a = $timeElement->getElementsByTagName("a");
					if($a->length == 1) {
						if($a->item(0)->hasAttribute("title")) {
							$priceString = $a->item(0)->getAttribute("title");
							$price = str_replace(["Koupit / rezervovat vstupenku (", ",- Kč)\nKino sál"], "", $priceString);
						}
					} else {
						$price = null;
					}
				}
				
				$movie = new \Zitkino\Movies\Movie($name, $datetimes);
				$movie->setLink($link);
				$movie->setType($type);
				$movie->setDubbing($dubbing);
				$movie->setSubtitles($subtitles);
				$movie->setLength($length);
				$movie->setPrice($price);
				$this->movies[] = $movie;
			}
		}
		
		$this->setMovies($this->movies);
	}
}