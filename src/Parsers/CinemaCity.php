<?php
namespace App\Parsers;

use App\Exceptions\ParserException;
use App\Models\Entities\{Cinema};
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
			$length = (int)$film["length"];
			
			$link = $event["bookingLink"];
			$auditorium = $event["auditorium"];
			
			$format = match (true) {
				in_array("4dx", $event["attributeIds"]) => "4DX",
				in_array("3d", $event["attributeIds"]) => "3D",
				in_array("imax", $event["attributeIds"]) => "IMAX",
				in_array("2d", $event["attributeIds"]) => "2D",
				default => null
			};
			
			$type = match (true) {
				in_array("laser-barco", $event["attributeIds"]) => "Laserová projekce Barco",
				default => null
			};
			
			switch(true) {
				case in_array("dubbed-lang-cs", $event["attributeIds"]):
				case in_array("original-lang-cs", $event["attributeIds"]):
					$dubbing = "česky";
					break;
				case in_array("original-lang-en", $event["attributeIds"]):
				case in_array("original-lang-en-us", $event["attributeIds"]) and !in_array("dubbed", $event["attributeIds"]):
					$dubbing = "anglicky";
					break;
				default:
					$dubbing = null;
					break;
			}
			
			$subtitles = match (true) {
				in_array("first-subbed-lang-cs", $event["attributeIds"]) => "české",
				default => null,
			};
			
			$price = null;
			
			$movie = $this->parserService->builderService->movie(name: $name, length: $length);
			$place = $this->parserService->builderService->place(name: $auditorium, cinema: $this->cinema);
			$screeningFormat = $this->parserService->builderService->screeningFormat(name: $format);
			$screeningType = $this->parserService->builderService->screeningType(name: $type);
			
			$this->parserService->builderService->save = false;
			$screening = $this->parserService->builderService->screening(cinema: $this->cinema, movie: $movie, place: $place, format: $screeningFormat, type: $screeningType, dubbing: $dubbing, subtitles: $subtitles, price: $price, link: $link);
			$this->parserService->builderService->save = true;
			
			$key = Strings::webalize($name."-".$format."-".$dubbing."-".$subtitles."-".$event["businessDay"]);
			
			$datetime = \DateTime::createFromFormat("Y-m-d\TH:i:s", $event["eventDateTime"]);
			$datetimes[$key][] = $datetime;
			$screenings[$key] = $screening;
		}
		
		foreach($screenings as $key => $screening) {
			$screening->setShowtimes($datetimes[$key]);
			$this->parserService->screeningFacade->save($screening);
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->parsed = new \DateTime();
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
