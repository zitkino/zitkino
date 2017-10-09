<?php
namespace Zitkino\parsers;

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
				
				$languageQuery = $xpath->query($info."//div[@class='left']/div/span[2]", $event);
				$languageString = $languageQuery->item(0)->nodeValue;
				
				switch(true) {
					case stripos($languageString, "ČV") !== false: $language = "česky"; $subtitles = null; break;
					case stripos($languageString, "ČT") !== false: $language = null; $subtitles = "české"; break;
					case stripos($languageString, "ČD") !== false: $language = "česky"; $subtitles = null; break;
					default: $language = null; $subtitles = null;
				}
				
				$timesQuery = $xpath->query("./div[@class='times']/div[@class='right']/span/a", $event);
				
				if($timesQuery->length == 1) {
					$priceString = $timesQuery->item(0)->getAttribute("title");
					$price = str_replace(["Rezervovat vstupenku (", ",- Kč)\nKino sál"], "", $priceString);
				} else {
					$timesQuery = $xpath->query("./div[@class='times']/div/span/span", $event);
					$price = null;
				}
				
				$timeString = $timesQuery->item(0)->nodeValue;
				$time = explode(":", $timeString);
				
				$datetime = \DateTime::createFromFormat("j.m.", $dayString);
				$datetimes = [$datetime->setTime($time[0], $time[1])];
				
				$this->movies[] = new \Zitkino\Movie($name, $datetimes);
				$this->movies[count($this->movies)-1]->setLink($link);
				$this->movies[count($this->movies)-1]->setLanguage($language);
				$this->movies[count($this->movies)-1]->setSubtitles($subtitles);
				$this->movies[count($this->movies)-1]->setLength($length);
				$this->movies[count($this->movies)-1]->setPrice($price);
			}
		}
		
		$this->setMovies($this->movies);
	}
}
