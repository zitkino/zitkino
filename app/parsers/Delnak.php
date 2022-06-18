<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\{OptimisticLockException, ORMException};
use Nette\Utils\Strings;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Delnak parser.
 */
class Delnak extends Parser {
	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$movieItems = 0;
		$events = $xpath->query("//div[@id='content-in']//div[@class='aktuality']//div[@class='content']");
		foreach($events as $event) {
			$itemQuery = $xpath->query("//h4//a", $event);
			$itemString = $itemQuery->item($movieItems)->nodeValue;
			
			if(strpos($itemString, "Letní kino") !== false) {
				$name = str_replace("Letní kino - ", "", $itemString);
				$dubbing = $subtitles = $length = $csfd = null;
				
				$details = $xpath->query(".//div//p", $event);
				foreach($details as $detail) {
					if(strpos($detail->nodeValue, "min.") !== false) {
						$matches = [];
						preg_match_all("/\((.*?)\)/", $detail->nodeValue, $matches);
						if(!empty($matches[1])) {
							$data = explode(",", $matches[1][0]);
							
							$dubbing = null;
							if(strpos($data[0], "CZ") !== false) {
								$dubbing = "česky";
							}
							
							$subtitles = null;
							if(isset($data[3])) {
								if(strpos($data[3], "české titulky") !== false) {
									$subtitles = "české";
								}
							}
							
							$length = (int)str_replace("min.", "", $data[2]);
						}
					}
				}
				
				$link = "http://www.delnickydumbrno.cz".$itemQuery->item($movieItems)->attributes->getNamedItem("href")->nodeValue;
				
				$dateQuery = $xpath->query("//p[@class='date']", $event);
				$date = $dateQuery->item($movieItems)->nodeValue;
				
				$timeQuery = $xpath->query("//p[@class='start']", $event);
				$timeString = $timeQuery->item($movieItems)->nodeValue;
				if(Strings::contains($timeString, "°°")) {
					$time = [str_replace("°°", "", $timeString), "00"];
				} else {
					$time = explode(":", $timeString);
				}
				
				$datetime = \DateTime::createFromFormat("j.m.Y", $date);
				$datetime->setTime(intval($time[0]), intval($time[1]));
				$datetimes = [$datetime];
				
				$price = null;
				$priceQuery = $xpath->query("//p[@class='entry']", $event);
				$priceItem = $priceQuery->item($movieItems);
				if(isset($priceItem)) {
					$priceString = $priceItem->nodeValue;
					$price = (int)str_replace(["Vstupné: ", " Kč"], "", $priceString);
				}
				
				$movie = $this->parserService->getMovieFacade()->getByName($name);
				if(!isset($movie)) {
					$movie = new Movie($name);
					$movie->setLength($length);
					$this->parserService->getMovieFacade()->save($movie);
				}
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setLanguages($dubbing, $subtitles)
					->setPrice($price)
					->setLink($link)
					->setShowtimes($datetimes);
				
				$this->parserService->getScreeningFacade()->save($screening);
				$this->cinema->addScreening($screening);
			}
			
			$movieItems++;
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
