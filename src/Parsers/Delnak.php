<?php
namespace App\Parsers;

use App\Exceptions\ParserException;
use Nette\Utils\Strings;

/**
 * Delnak parser.
 */
class Delnak extends Parser {
	/**
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$movieItems = 0;
		$events = $xpath->query("//div[@id='content-in']//div[@class='aktuality']//div[@class='content']");
		foreach($events as $event) {
			$itemQuery = $xpath->query("//h4//a", $event);
			$itemString = $itemQuery->item($movieItems)->nodeValue;
			
			if(str_contains($itemString, "Letní kino")) {
				$name = str_replace("Letní kino - ", "", $itemString);
				$dubbing = $subtitles = $length = $csfd = null;
				
				$details = $xpath->query(".//div//p", $event);
				foreach($details as $detail) {
					if(str_contains($detail->nodeValue, "min.")) {
						$matches = [];
						preg_match_all("/\((.*?)\)/", $detail->nodeValue, $matches);
						if(!empty($matches[1])) {
							$data = explode(",", $matches[1][0]);
							
							$dubbing = null;
							if(str_contains($data[0], "CZ")) {
								$dubbing = "česky";
							}
							
							$subtitles = null;
							if(isset($data[3])) {
								if(str_contains($data[3], "české titulky")) {
									$subtitles = "české";
								}
							}
							
							$length = (int)str_replace("min.", "", $data[2]);
						}
					}
				}
				
				$link = "http://www.delnickydumbrno.cz".$itemQuery->item($movieItems)->attributes->getNamedItem("href")->nodeValue;
				
				$dateQuery = $xpath->query("//p[@class='date']", $event);
				$date = $dateQuery->item($movieItems)->nodeValue;
				
				$timeQuery = $xpath->query("//p[@class='start']", $event);
				$timeString = $timeQuery->item($movieItems)->nodeValue;
				if(Strings::contains($timeString, "°°")) {
					$time = [str_replace("°°", "", $timeString), "00"];
				} else {
					$time = explode(":", $timeString);
				}
				
				$datetime = \DateTime::createFromFormat("j.m.Y", $date);
				$datetime->setTime(intval($time[0]), intval($time[1]));
				$datetimes = [$datetime];
				
				$price = null;
				$priceQuery = $xpath->query("//p[@class='entry']", $event);
				$priceItem = $priceQuery->item($movieItems);
				if(isset($priceItem)) {
					$priceString = $priceItem->nodeValue;
					$price = (int)str_replace(["Vstupné: ", " Kč"], "", $priceString);
				}
				
				$movie = $this->parserService->builderService->movie(name: $name, length: $length);
				$screening = $this->parserService->builderService->screening(cinema: $this->cinema, movie: $movie, dubbing: $dubbing, subtitles: $subtitles, price: $price, link: $link, showtimes: $datetimes);
				
				$this->cinema->addScreening($screening);
			}
			
			$movieItems++;
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->cinemaFacade->save($this->cinema);
	}
}
