<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\{OptimisticLockException, ORMException};
use Nette\Utils\Strings;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Letní kino na Dvoře Městského divadla parser.
 */
class Mdb extends Parser {
	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@class='wpb_wrapper']//div[@class='table-events-content']//p");
		foreach($events as $event) {
			if(!empty(trim($event->nodeValue))) {
				$nameQuery = $xpath->query(".//strong[2]", $event);
				$nameItem = $nameQuery->item(0);
				$name = "";
				if(isset($nameItem)) {
					$name = trim($nameItem->nodeValue);
				}

//				$linkQuery = $xpath->query(".//a", $event);
//				$linkItem = $linkQuery->item(0);
//				$link = null;
//				if($linkItem != null) {
//					$link = $linkQuery->item(0)->attributes->getNamedItem("href")->nodeValue;
//				}
				
				$dateQuery = $xpath->query(".//strong[1]", $event);
				$datetime = \DateTime::createFromFormat("j.n.", trim($dateQuery->item(0)->nodeValue));
				if($datetime != false) {
					$month = $datetime->format("m");
					switch($month) {
						case "6":
						case "7":
							$datetime->setTime(21, 30);
							break;
						case "8":
						case "9":
							$datetime->setTime(21, 0);
							break;
					}
				}
				$datetimes = [$datetime];
				
				$length = null;
				if(Strings::endsWith($event->nodeValue, " min")) {
					$parts = Strings::split($event->nodeValue, "#/#");
					$minutes = $parts[array_key_last($parts)];
					$length = (int)str_replace(" min", "", $minutes);
				}
				
				$price = 99;
				
				$movie = $this->parserService->getMovieFacade()->getByName($name);
				if(!isset($movie)) {
					$movie = new Movie($name);
					$movie->setLength($length);
					$this->parserService->getMovieFacade()->save($movie);
				}
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setPrice($price)
//				->setLink($link)
					->setShowtimes($datetimes);
				
				$this->parserService->getScreeningFacade()->save($screening);
				$this->cinema->addScreening($screening);
			}
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
