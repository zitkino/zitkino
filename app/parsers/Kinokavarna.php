<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Zitkino\Cinemas\Cinema;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Kinokavarna parser.
 */
class Kinokavarna extends Parser {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->setUrl("http://www.kinokavarna.cz/program.html");
	}
	
	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@id='content-in']/div[@class='aktuality']");
		foreach($events as $event) {
			$nameQuery = $xpath->query(".//h4", $event);
			$nameString = $nameQuery->item(0)->nodeValue;
			
			$dateQuery = $xpath->query(".//h4//span", $event);
			$date = $dateQuery->item(0)->nodeValue;
			
			$timeQuery = $xpath->query(".//p[@class='start']", $event);
			$timeReplacing = ["Začátek: ", "od "];
			$timeString = str_replace($timeReplacing, "", $timeQuery->item(0)->nodeValue);
			$time = str_replace(".", ":", mb_substr($timeString, 0, 5));
			
			$name = mb_substr($nameString, strlen($date));
			$badNames = ["", "ZAVŘENO", "Zavřeno", "STÁTNÍ SVÁTEK- ZAVŘENO"];
			if(($time == " ") and (in_array($name, $badNames) or (strpos($name, "OTEVÍRACÍ DOBA-") !== false))) {
				continue;
			}
			
			$link = "http://www.kinokavarna.cz/program.html";
			
			$infoQuery = $xpath->query(".//p[2]", $event);
			$info = explode(",", $infoQuery->item(0)->nodeValue);
			
			if(isset($info[3])) {
				$length = (int)str_replace(" min.", "", $info[3]);
			} else {
				$length = null;
			}
			
			$dubbing = null;
			$subtitles = null;
			foreach($infoQuery as $lang) {
				if(strpos($lang->nodeValue, ", ČR,") !== false) {
					$dubbing = "česky";
					break;
				}
				if(strpos($lang->nodeValue, "čes. tit") !== false) {
					$subtitles = "české";
					break;
				}
			}
			
			$datetimes = [];
			$datetime = \DateTime::createFromFormat("j.n.Y", $date);
			$datetime->setTime(intval(substr($time, 0, 2)), intval(substr($time, 3, 2)));
			$datetimes[] = $datetime;
			
			$priceString = mb_substr($timeString, 6, 11);
			if(!empty($priceString)) {
				$price = (int)str_replace("Vstupné: ", "", $priceString);
			} else {
				$price = null;
			}
			
			$movie = $this->parserService->getMovieFacade()->getByName($name);
			if(!isset($movie)) {
				$movie = new Movie($name);
				$movie->setLength($length);
				$this->parserService->getMovieFacade()->save($movie);
			}
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setLanguages($dubbing, $subtitles);
			$screening->setPrice($price);
			$screening->setLink($link);
			$screening->setShowtimes($datetimes);
			
			$this->parserService->getEntityManager()->persist($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getEntityManager()->persist($this->cinema);
		$this->parserService->getEntityManager()->flush();
	}
}
