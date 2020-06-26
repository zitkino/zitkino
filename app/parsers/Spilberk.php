<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Zitkino\Cinemas\Cinema;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Spilberk parser.
 */
class Spilberk extends Parser {
	/**
	 * Spilberk constructor.
	 * @param ParserService $parserService
	 * @param Cinema $cinema
	 */
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->setUrl("http://www.letnikinospilberk.cz");
	}
	
	/**
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@id='page-program']/div[@class='film']");
		foreach($events as $event) {
			$nameQuery = $xpath->query(".//div[@class='right']//h2", $event);
			$nameString = $nameQuery->item(0)->nodeValue;
			$name = str_replace([" - repríza"], "", $nameString);
			
			$csfd = null;
			$csfdQuery = $xpath->query(".//a[@class='vice']", $event);
			$csfdItem = $csfdQuery->item(0);
			if(isset($csfdItem)) {
				$csfdString = $csfdItem->getAttribute("href");
				$csfd = str_replace(["https://www.csfd.cz/film/", "/prehled/"], "", $csfdString);
			}
			
			$link = null;
			$linkQuery = $xpath->query(".//a[@class='goout']", $event);
			$linkItem = $linkQuery->item(0);
			if(isset($linkItem)) {
				$link = $linkItem->getAttribute("href");
			}
			
			$itemsQuery = $xpath->query(".//div[@class='right']//p[@class='popisek']", $event);
			$itemString = $itemsQuery->item(0)->nodeValue;
			
			switch(true) {
				case(strpos($itemString, "Česko") !== false):
				case(strpos($itemString, "český dabing") !== false):
					$dubbing = "český";
					$subtitles = null;
					break;
				case(strpos($itemString, "čes. titulky") !== false):
					$dubbing = null;
					$subtitles = "české";
					break;
				default:
					$dubbing = null;
					$subtitles = null;
					break;
			}
			
			$dateQuery = $xpath->query(".//div[@class='left']//p", $event);
			$dateString = $dateQuery->item(0)->nodeValue;
			$date = rtrim(substr($dateString, 0, 6));
			
			$timeString = substr($dateString, -5);
			$time = explode(":", $timeString);
			
			$datetime = \DateTime::createFromFormat("j. n.", $date);
			$datetime->setTime(intval($time[0]), intval($time[1]));
			$datetimes = [$datetime];
			
			$lengthString = explode("min", $itemString);
			$length = (int)trim($lengthString[0]);
			if($length == 0) {
				$length = null;
			}
			
			$priceQuery = $xpath->query(".//div[@class='right']//p[@class='cena']", $event);
			$priceString = $priceQuery->item(0)->nodeValue;
			$price = str_replace([",- Kč"], "", $priceString);
			
			$movie = $this->parserService->getMovieFacade()->getByName($name);
			if(!isset($movie)) {
				$movie = new Movie($name);
				$movie->setLength($length);
				$movie->setCsfd($csfd);
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
