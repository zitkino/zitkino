<?php
namespace App\Parsers;

use App\Exceptions\ParserException;

/**
 * BVV parser.
 */
class BvvAutokino extends Parser {
	/**
	 * @throws ParserException
	 */
	public function parse(): void {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//*[contains(@class, 'accompanying-program-wrapper')]//*[contains(@class, 'accompanying-program-list')]");
		foreach($events as $event) {
			$dateQuery = $xpath->query(".//*[contains(@class, 'accompanying-program-label')]", $event);
			$dateValue = $dateQuery->item(0)->nodeValue;
			$dateParts = explode(" ", $dateValue, 2);
			
			$timeQuery = $xpath->query(".//*[contains(@class, 'accompanying-program-item')]/*[contains(@class, 'time')]", $event);
			$timeValue = trim($timeQuery->item(0)->nodeValue);
			$timeParts = explode(" - ", $timeValue, 2);
			
			$start = null;
			$length = null;
			if(!empty($dateParts[1])) {
				if(!empty($timeParts[0])) {
					$start = \DateTime::createFromFormat("d.m.Y H:i", $dateParts[1]." ".$timeParts[0]);
				}
				
				if(!empty($timeParts[1])) {
					$end = \DateTime::createFromFormat("d.m.Y H:i", $dateParts[1]." ".$timeParts[1]);
					$hours = (int)$end->diff($start)
							->format("%H") * 60;
					$minutes = (int)$end->diff($start)
						->format("%i");
					$length = $hours + $minutes;
				}
			}
			
			if(isset($start) and $start instanceof \DateTime) {
				$showtime = $start;
			} else {
				$showtime = null;
			}
			
			$placeQuery = $xpath->query(".//*[contains(@class, 'accompanying-program-item')]/*[contains(@class, 'info')]/*[contains(@class, 'place')]", $event);
			$placeName = $placeQuery->item(0)->nodeValue;
			
			$nameQuery = $xpath->query(".//*[contains(@class, 'accompanying-program-item')]/*[contains(@class, 'info')]/h3", $event);
			$name = $nameQuery->item(0)->nodeValue;
			
			$priceQuery = $xpath->query(".//*[contains(@class, 'accompanying-program-item')]/*[contains(@class, 'info')]/*[contains(@class, 'price')]", $event);
			$priceValue = trim($priceQuery->item(0)->nodeValue);
			$price = (int)str_replace(" Kč", "", $priceValue);
			
			$linkQuery = $xpath->query(".//*[contains(@class, 'accompanying-program-item')]/*[contains(@class, 'detail')]//a[contains(@class, 'copy-to-clipboard')]", $event);
			$link = $linkQuery->item(0)->attributes->getNamedItem("href")->nodeValue;
			
			$movie = $this->parserService->builderService->movie(name: $name, length: $length ? (int)$length : null);
			$place = $this->parserService->builderService->place(name: $placeName, cinema: $this->cinema);
			$screening = $this->parserService->builderService->screening(cinema: $this->cinema, movie: $movie, place: $place, price: $price, link: $link, showtimes: [$showtime]);
			
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->parsed = new \DateTime();
  $this->parserService->cinemaRepository->save($this->cinema);
	}
}
