<?php
namespace Zitkino\Parsers;

use GuzzleHttp\Exception\GuzzleException;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Place;
use Zitkino\Screenings\{Screening, ScreeningType};

/**
 * Scala parser.
 */
class Scala extends Parser {
	protected function downloadData(): string {
		try {
			$parameters = ["cinema" => ["5"], "hall" => [["4"], ["16"]], "_locale" => "cs"];
			$response = $this->parserService->getClientFactory()->createClient()->post($this->getUrl(), ["form_params" => $parameters]);
			$body = (string)$response->getBody();
		} catch(GuzzleException $e) {
			$e = new ParserException($e->getMessage());
			$e->setUrl($this->getUrl());
			throw $e;
		}
		
		return $body ?: "";
	}
	
	/**
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		$weekdays = ["Po", "Út", "St", "Čt", "Pá", "So", "Ne"];
		
		$days = $xpath->query("//div[@class='program']");
		$dayItems = 0;
		foreach($days as $day) {
			$dateQuery = $xpath->query(".//div[@class='program__day']//span[@class='desktop']", $day);
			$dateString = $dateQuery->item(0)->nodeValue;
			switch($dateString) {
				case "Dnes":
					$datetime = new \DateTime();
					break;
				case "Zítra":
					$datetime = new \DateTime("tomorrow");
					break;
				default:
					$date = trim(str_replace($weekdays, "", $dateString));
					$datetime = \DateTime::createFromFormat("d/m", $date);
					break;
			}
			
			$events = $xpath->query(".//div[@class='program__info']//div[@class='program__info-row']", $day);
			foreach($events as $event) {
				$hourQuery = $xpath->query(".//div[@class='program__hour']", $event);
				$hourString = $hourQuery->item(0)->nodeValue;
				$hour = explode(":", $hourString);
				$datetime->setTime((int)$hour[0], (int)$hour[1]);
				
				$datetimes = [$datetime];
				
				$placeQuery = $xpath->query(".//div[contains(@class, 'program__place--desktop')]", $event);
				$placeValue = $placeQuery->item(0)->nodeValue;
				$placeString = trim(str_replace(PHP_EOL, "", $placeValue));
				$placeName = preg_replace("/\s+/", " ", $placeString);
				
				if(!str_contains($placeName, "Scala") or $placeName === "Scala Studio Scala") {
					continue;
				}
				
				$place = $this->parserService->getPlaceFacade()->getByName($placeName);
				if(!isset($place)) {
					$place = new Place($placeName);
					$place->setCinema($this->cinema);
				}
				$this->parserService->getPlaceFacade()->save($place);
				
				$nameQuery = $xpath->query(".//div[contains(@class, 'program__movie-name')]", $event);
				$name = $nameQuery->item(0)->nodeValue;
				
				$scalaType = "classic";
				$screeningType = null;
				$tags = $xpath->query(".//div[@class='program__tags']//span[@class='program__tag']", $event);
				foreach($tags as $tag) {
					$type = trim($tag->nodeValue);
					
					if(str_contains($type, "Scalní letňák")) {
						$scalaType = "summer";
						continue;
					}
					
					switch($type) {
						case "Scalní letňák":
							break;
						default:
							$screeningType = $this->parserService->getScreeningFacade()->getType($type);
							if(!isset($screeningType)) {
								$screeningType = new ScreeningType($type);
								$this->parserService->getScreeningFacade()->save($screeningType);
							}
							break;
					}
				}
				
				if(get_class($this) == ScalaLetni::class) {
					if($scalaType !== "summer") {
						continue;
					}
				}
				
				$priceQuery = $xpath->query(".//div[@class='program__price']//button[@class='program__ticket']//span", $event);
				if($priceQuery->count() > 0) {
					$priceString = $priceQuery->item(0)->nodeValue;
					$price = (int)str_replace(" Kč", "", $priceString);
				} else {
					$price = null;
				}
				
				$linkQuery = $xpath->query(".//div[@class='program__price']//input[@name='successredirect']", $event);
				if($linkQuery->count() > 0) {
					/** @var \DOMElement $linkItem */
					$linkItem = $linkQuery->item(0);
					$link = $linkItem->getAttribute("value");
				} else {
					$link = null;
				}
				
				$movie = $this->parserService->getMovieFacade()->getByName($name);
				if(!isset($movie)) {
					$movie = new Movie($name);
					$this->parserService->getMovieFacade()->save($movie);
				}
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setPlace($place)
					->setType($screeningType)
					->setPrice($price)
					->setLink($link)
					->setShowtimes($datetimes);
				
				$this->parserService->getScreeningFacade()->save($screening);
				$this->cinema->addScreening($screening);
			}
			
			$dayItems++;
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
