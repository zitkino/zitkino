<?php
namespace App\Parsers;

use App\Exceptions\ParserException;
use App\Models\Entities\{Screening};
use App\Models\Entities\Cinema;
use App\Models\Entities\Movie;
use App\Models\Entities\ScreeningType;
use App\Services\ParserService;
use Nette\Utils\{JsonException, Strings};

/**
 * Cinema City parser.
 */
abstract class CinemaCity extends Parser {
	private string $id;
	
	public function __construct(ParserService $parserService, Cinema $cinema, string $id) {
		parent::__construct($parserService, $cinema);
		$this->id = $id;
	}
	
	/**
	 * @throws JsonException
	 * @throws ParserException
	 */
	public function getContent(): bool {
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
				case (in_array("4dx", $event["attributeIds"])):
					$type = "4DX";
					break;
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
			
			$length = (int)$film["length"];
			$price = null;
			
			$movie = $this->parserService->movieFacade->grabByName($name);
			if(!isset($movie)) {
				$movie = new Movie($name);
				$movie->setLength($length);
				$this->parserService->movieFacade->save($movie);
			}
			
			$screeningType = $this->parserService->screeningFacade->grabType($type);
			if(!isset($screeningType)) {
				$screeningType = new ScreeningType($type);
			}
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setType($screeningType)
				->setLanguages($dubbing, $subtitles)
				->setPrice($price)
				->setLink($link);
			
			$screenings[$key] = $screening;
		}
		
		foreach($screenings as $key => $screening) {
			$screening->setShowtimes($datetimes[$key]);
			$this->parserService->screeningFacade->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->cinemaFacade->save($this->cinema);
		
		return true;
	}
	
	/**
	 * @throws JsonException
	 * @throws ParserException
	 */
	public function getOneDay(\DateTime $datetime): bool {
		$date = $datetime->format("Y-m-d");
		$this->setUrl("https://www.cinemacity.cz/cz/data-api-service/v1/quickbook/10101/film-events/in-cinema/".$this->id."/at-date/".$date."?attr=&lang=cs_CZ");
		
		return $this->getContent();
	}
	
	/**
	 * @throws JsonException
	 * @throws ParserException
	 * @throws \DateMalformedStringException
	 */
	public function parse(): void {
		$datetime = new \DateTime();
		$this->getOneDay($datetime);
		
		$datetime->modify("+1 days");
		$this->getOneDay($datetime);
	}
}
