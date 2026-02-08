<?php
namespace App\Parsers;

use App\Exceptions\ParserException;
use App\Models\Entities\{Movie, Screening};

/**
 * Kinokavarna parser.
 */
class Kinokavarna extends Parser {
	/**
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
			if(($time == " ") and (in_array($name, $badNames) or (str_contains($name, "OTEVÍRACÍ DOBA-")))) {
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
				if(str_contains($lang->nodeValue, ", ČR,")) {
					$dubbing = "česky";
					break;
				}
				if(str_contains($lang->nodeValue, "čes. tit")) {
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
			
			$movie = $this->parserService->builderService->movie(name: $name, length: $length);
			$screening = $this->parserService->builderService->screening(cinema: $this->cinema, movie: $movie, dubbing: $dubbing, subtitles: $subtitles, price: $price, link: $link, showtimes: $datetimes);
			
			$this->cinema->addScreening($screening);
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->cinemaFacade->save($this->cinema);
	}
}
