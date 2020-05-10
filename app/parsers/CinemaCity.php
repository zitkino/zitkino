<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Nette\Utils\JsonException;
use Nette\Utils\Strings;
use Zitkino\Cinemas\Cinema;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\ScreeningType;

/**
 * Cinema City parser.
 */
abstract class CinemaCity extends Parser {
	/** @var string */
	private $id;
	
	public function __construct(ParserService $parserService, Cinema $cinema, string $id) {
		parent::__construct($parserService, $cinema);
		$this->id = $id;
	}
	
	/**
	 * @return bool
	 * @throws JsonException
	 * @throws ParserException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function getContent() {
		$json = $this->getJson();
		
		$screenings = [];
		$datetimes = [];
		
		foreach($json["body"]["events"] as $event) {
			$key = array_search($event["filmId"], array_column($json["body"]["films"], "id"));
			$film = $json["body"]["films"][$key];
			
			$name = $film["name"];
			$link = $event["bookingLink"];
			
			$type = "2D";
			switch(true) {
				case (in_array("2d", $event["attributeIds"])):
					$type = "2D";
					break;
				case (in_array("3d", $event["attributeIds"])):
					$type = "3D";
					break;
			}
			
			$dubbing = null;
			switch(true) {
				case (in_array("dubbed-lang-cs", $event["attributeIds"])):
				case (in_array("original-lang-cs", $event["attributeIds"])):
					$dubbing = "česky";
					break;
				case (in_array("original-lang-en-us", $event["attributeIds"])):
					$dubbing = "anglicky";
					break;
			}
			
			$subtitles = null;
			switch(true) {
				case (in_array("first-subbed-lang-cs", $event["attributeIds"])):
					$subtitles = "české";
					break;
			}
			
			$key = Strings::webalize($name."-".$type."-".$dubbing."-".$subtitles."-".$event["businessDay"]);
			
			$datetime = \DateTime::createFromFormat("Y-m-d\TH:i:s", $event["eventDateTime"]);
			$datetimes[$key][] = $datetime;
			
			$length = $film["length"];
			
			$dayOfWeek = $datetime->format("w");
			if($dayOfWeek == 1) {
				$price = 175;
				if($type == "3D") {
					$price = 220;
				}
			} else {
				$price = 205;
				if($type == "3D") {
					$price = 250;
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
			}
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setType($screeningType);
			$screening->setLanguages($dubbing, $subtitles);
			$screening->setPrice($price);
			$screening->setLink($link);
			
			$screenings[$key] = $screening;
		}
		
		foreach($screenings as $key => $screening) {
			$screening->setShowtimes($datetimes[$key]);
			$this->parserService->getEntityManager()->persist($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getEntityManager()->persist($this->cinema);
		$this->parserService->getEntityManager()->flush();
		
		return true;
	}
	
	/**
	 * @param \DateTime $datetime
	 * @return bool
	 * @throws JsonException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws ParserException
	 */
	public function getOneDay(\DateTime $datetime) {
		$date = $datetime->format("Y-m-d");
		$this->setUrl("https://www.cinemacity.cz/cz/data-api-service/v1/quickbook/10101/film-events/in-cinema/".$this->id."/at-date/".$date."?attr=&lang=cs_CZ");
		
		return $this->getContent();
	}
	
	/**
	 * @throws JsonException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws ParserException
	 */
	public function parse(): void {
		$datetime = new \DateTime();
		$this->getOneDay($datetime);
		
		$datetime->modify("+1 days");
		$this->getOneDay($datetime);
	}
}
