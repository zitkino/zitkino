<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\{OptimisticLockException, ORMException};
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Scala parser.
 */
class Scala extends Parser {
	/**
	 * @throws ParserException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@id='content']/table//tr");
		$days = 0;
		$movieItems = 0;
		foreach($events as $event) {
			$datetimes = [];
			
			if($event->getAttribute("class") === "day") {
				$dateQuery = $xpath->query("//tr[@class='day']//h2", $event);
				$dateFullString = explode(",", $dateQuery->item($days)->nodeValue);
				$dateString = explode(".", $dateFullString[1]);
				
				$day = $dateString[0];
				
				$monthString = $dateString[1];
				$monthArray = ["ledna", "února", "března", "dubna", "května", "června", "července", "srpna", "září", "října", "listopadu", "prosince"];
				$monthNumbers = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12"];
				$month = str_replace($monthArray, $monthNumbers, $monthString);
				
				$year = date("Y");
				
				$date = trim($day).".".trim($month).".".$year;
				$days++;
			} else {
				$nameQuery = $xpath->query("//td[@class='col_movie_name']//a", $event);
				$nameString = $nameQuery->item($movieItems)->nodeValue;
				$name = str_replace("feat. Kmeny90/BU2R", "", $nameString);
				
				$dubbing = null;
				if(\Lib\Strings::endsWith($name, "- cz dabing")) {
					$dubbing = "česky";
					$name = str_replace(" - cz dabing", "", $name);
				}
				
				$timeQuery = $xpath->query("//td[@class='col_time_reservation']", $event);
				$time = explode(":", $timeQuery->item($movieItems)->nodeValue);
				
				if(isset($date)) {
					$datetime = \DateTime::createFromFormat("j.n.Y", $date);
					$datetime->setTime(intval($time[0]), intval($time[1]));
					$datetimes[] = $datetime;
				}
				
				$link = "http://www.kinoscala.cz".$nameQuery->item($movieItems)->attributes->getNamedItem("href")->nodeValue;
				
				$priceQuery = $xpath->query("//td[@class='col_price']", $event);
				$priceItem = $priceQuery->item($movieItems)->nodeValue;
				$priceString = htmlentities($priceItem, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, "utf-8");
				$price = (int)trim(str_replace("&nbsp;Kč", "", $priceString));
				
				$movie = $this->parserService->getMovieFacade()->getByName($name);
				if(!isset($movie)) {
					$movie = new Movie($name);
					$this->parserService->getMovieFacade()->save($movie);
				}
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setLanguages($dubbing, null)
					->setPrice($price)
					->setLink($link)
					->setShowtimes($datetimes);
				
				$this->parserService->getScreeningFacade()->save($screening);
				$this->cinema->addScreening($screening);
				
				$movieItems++;
			}
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
