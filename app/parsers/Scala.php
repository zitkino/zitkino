<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Zitkino\Cinemas\Cinema;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Scala parser.
 */
class Scala extends Parser {
	/**
	 * Scala constructor.
	 * @param ParserService $parserService
	 * @param Cinema $cinema
	 */
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->setUrl("https://www.kinoscala.cz/cz/program");
	}
	
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
				
				$link = "http://www.kinoscala.cz".$nameQuery->item($movieItems)->getAttribute("href");
				
				$priceQuery = $xpath->query("//td[@class='col_price']", $event);
				$priceItem = $priceQuery->item($movieItems)->nodeValue;
				$priceString = htmlentities($priceItem, null, "utf-8");
				$price = trim(str_replace("&nbsp;Kč", "", $priceString));
				
				$movie = $this->parserService->getMovieFacade()->getByName($name);
				if(!isset($movie)) {
					$movie = new Movie($name);
					$this->parserService->getMovieFacade()->save($movie);
				}
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setLanguages($dubbing, null);
				$screening->setPrice($price);
				$screening->setLink($link);
				$screening->setShowtimes($datetimes);
				
				$this->parserService->getEntityManager()->persist($screening);
				$this->cinema->addScreening($screening);
				
				$movieItems++;
			}
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getEntityManager()->persist($this->cinema);
		$this->parserService->getEntityManager()->flush();
	}
}
