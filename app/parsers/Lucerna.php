<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\{OptimisticLockException, ORMException};
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\{Screening, ScreeningType};

/**
 * Lucerna parser.
 */
class Lucerna extends Parser {
	/**
	 * @throws ParserException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$days = $xpath->query("//div[@class='tabs programtabs']//ul[@id='table_days']//div[@class='scroll-pane-wrapper']//li");
		foreach($days as $day) {
			$dayQuery = $xpath->query("./a", $day);
			/** @var \DOMElement $dayElement */
			$dayElement = $dayQuery->item(0);
			$dayId = $dayElement->getAttribute("data-den");
			
			/** @var \DOMElement $dayStringElement */
			$dayStringElement = $dayQuery->item(0);
			$dayString = $dayStringElement->getElementsByTagName("span")->item(0)->nodeValue;
			
			$events = $xpath->query("//div[@class='tabs programtabs']//div[@id='den_".$dayId."']//div[@class='item']");
			foreach($events as $key => $event) {
				$info = "./div[@class='heading']";
				
				$nameQuery = $xpath->query($info."//h2//a", $event);
				$name = $nameQuery->item(0)->nodeValue;
				
				$smallQuery = $xpath->query($info."//h2//a/small", $event);
				$smallItem = $smallQuery->item(0);
				if(isset($smallItem)) {
					$small = $smallItem->nodeValue;
				} else {
					$small = "";
				}
				$name = str_replace($small, "", $name);
				
				$link = "http://www.kinolucerna.info".$nameQuery->item(0)->attributes->getNamedItem("href")->nodeValue;
				
				$lengthQuery = $xpath->query($info."//div[@class='eventlenght']", $event);
				$lengthString = $lengthQuery->item(0)->nodeValue;
				$length = (int)str_replace("&nbsp;min", "", htmlentities($lengthString, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, "utf-8"));
				
				$type = null;
				$typeQuery = $xpath->query($info."//div[@class='left']/div/span[1]", $event);
				if($typeQuery->length >= 1) {
					$typeString = $typeQuery->item(0)->nodeValue;
					if($typeString == "3D") {
						$type = $typeString;
					} else {
						$type = null;
					}
				}
				
				$dubbing = null;
				$subtitles = null;
				$languageQuery = $xpath->query($info."//div[@class='left']/div/span[2]", $event);
				if($languageQuery->length >= 1) {
					$languageString = $languageQuery->item(0)->nodeValue;
					switch(true) {
						case stripos($languageString, "ČD") !== false:
						case stripos($languageString, "ČV") !== false:
							$dubbing = "česky";
							$subtitles = null;
							break;
						case stripos($languageString, "ČT") !== false:
							$dubbing = null;
							$subtitles = "české";
							break;
						case stripos($languageString, "Anglická verze s českými titulky") !== false:
						case stripos($languageString, "anglicka_verzia_ceske_titulky") !== false:
							$dubbing = "anglicky";
							$subtitles = "české";
							break;
						default:
							$dubbing = null;
							$subtitles = null;
					}
				}
				
				$datetimes = [];
				$price = null;
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
							$price = (int)str_replace(["Koupit / rezervovat vstupenku (", ",- Kč)\nKino sál"], "", $priceString);
						}
					} else {
						$price = null;
					}
				}
				
				$movie = $this->parserService->getMovieFacade()->getByName($name);
				if(!isset($movie)) {
					$movie = new Movie($name);
					$movie->setLength($length);
					$this->parserService->getMovieFacade()->save($movie);
				}
				
				$screeningType = $this->parserService->getScreeningFacade()->getType($type);
				if(!isset($screeningType)) {
					$screeningType = new ScreeningType($type);
					$this->parserService->getScreeningFacade()->save($screeningType);
				}
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setType($screeningType)
					->setLanguages($dubbing, $subtitles)
					->setPrice($price)
					->setLink($link)
					->setShowtimes($datetimes);
				
				$this->parserService->getScreeningFacade()->save($screening);
				$this->cinema->addScreening($screening);
			}
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
