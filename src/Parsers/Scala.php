<?php
namespace App\Parsers;

use App\Exceptions\ParserException;
use App\Models\Entities\{Place};
use App\Models\Entities\Movie;
use App\Models\Entities\Screening;
use App\Models\Entities\ScreeningType;
use Monolog\Attribute\WithMonologChannel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

/**
 * Scala parser.
 */
#[WithMonologChannel("scala")]
class Scala extends Parser {
	protected function downloadData(): string {
		try {
			$parameters = ["cinema" => ["5"], "hall" => [["4"], ["16"]], "_locale" => "cs"];
			$response = $this->parserService->getHttpClient()
				->request(Request::METHOD_POST, $this->getUrl(), ["body" => $parameters]);
			$body = $response->getContent();
		} catch(ExceptionInterface $e) {
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
		
		$days = $xpath->query("//div[@class='program']");
		$dayItems = 0;
		foreach($days as $day) {
			$dateQuery = $xpath->query(".//a[@class='program__day']//span[@class='desktop']", $day);
			$dateString = $dateQuery->item(0)->nodeValue;
			switch($dateString) {
				case "Dnes":
					$datetime = new \DateTime();
					break;
				case "Zítra":
					$datetime = new \DateTime("tomorrow");
					break;
				default:
					$weekdays = ["Po", "Út", "St", "Čt", "Pá", "So", "Ne"];
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
				
				$place = $this->parserService->placeFacade->grabByName($placeName);
				if(!isset($place)) {
					$place = new Place($placeName);
					$place->setCinema($this->cinema);
				}
				$this->parserService->placeFacade->save($place);
				
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
							$screeningType = $this->parserService->screeningFacade->grabType($type);
							if(!isset($screeningType)) {
								$screeningType = new ScreeningType($type);
								$this->parserService->screeningFacade->save($screeningType);
							}
							break;
					}
				}
				
				if(get_class($this) == ScalaLetni::class) {
					if($scalaType !== "summer") {
						continue;
					}
				}
				
				$price = null;
				$priceQuery = $xpath->query(".//div[@class='program__price']//button[contains(@class, 'program__ticket')]//span", $event);
				if($priceQuery->count() > 0) {
					$priceString = $priceQuery->item(0)->nodeValue;
					$price = (int)str_replace(" Kč", "", $priceString);
				} else {
					$priceQuery = $xpath->query(".//div[@class='program__price']//span[contains(@class, 'program__ticket--zero')]", $event);
					if($priceQuery->count() > 0) {
						$priceString = $priceQuery->item(0)->nodeValue;
						if(trim($priceString) === "Zdarma") {
							$price = 0;
						}
					}
				}
				
				$linkQuery = $xpath->query(".//div[@class='program__price']//input[@name='successredirect']", $event);
				if($linkQuery->count() > 0) {
					/** @var \DOMElement $linkItem */
					$linkItem = $linkQuery->item(0);
					$link = $linkItem->getAttribute("value");
				} else {
					$link = null;
				}
				
				$movie = $this->parserService->movieFacade->grabByName($name);
				if(!isset($movie)) {
					$movie = new Movie($name);
					$this->parserService->movieFacade->save($movie);
				}
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setPlace($place)
					->setType($screeningType)
					->setPrice($price)
					->setLink($link)
					->setShowtimes($datetimes);
				
				$this->parserService->screeningFacade->save($screening);
				$this->cinema->addScreening($screening);
			}
			
			$dayItems++;
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->cinemaFacade->save($this->cinema);
	}
}
