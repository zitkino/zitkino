<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\Screenings;

/**
 * Scalní letňák parser.
 */
class ScalaLetni extends Parser {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		$this->setUrl("https://www.kinoscala.cz/cz/cyklus/scalni-letnak-251");
		$this->parse();
	}
	
	public function parse(): Screenings {
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@id='program']/table//tr");
		$days = 0;
		$movieItems = 0;
		foreach($events as $event) {
			$nameQuery = $xpath->query("//td[@class='col_movie_name']//a", $event);
			$nameString = $nameQuery->item($movieItems)->nodeValue;
			$name = str_replace("feat. Kmeny90/BU2R", "", $nameString);

			$link = "http://www.kinoscala.cz".$nameQuery->item($movieItems)->getAttribute("href");
			
			$dubbing = null;
			if(\Lib\Strings::endsWith($name, "- cz dabing")) {
				$dubbing = "česky";
				$name = str_replace(" - cz dabing", "", $name);
			}
			
			$dateQuery = $xpath->query("//td[@class='col_date col_text']", $event);
			$date = $dateQuery->item($days)->nodeValue;
			
			$timeQuery = $xpath->query("//td[@class='col_time_reservation']", $event);
			$time = explode(":", $timeQuery->item($movieItems)->nodeValue);
			
			$datetime = \DateTime::createFromFormat("j. n. Y", $date);
			$datetime->setTime(intval($time[0]), intval($time[1]));
			$datetimes = [$datetime];
			
			$priceQuery = $xpath->query("//td[@class='col_price']", $event);
			$priceItem = $priceQuery->item($movieItems)->nodeValue;
			$priceString = htmlentities($priceItem, null, "utf-8");
			$price = trim(str_replace("&nbsp;Kč", "", $priceString));
			
			
			$movie = new Movie($name);
			
			$screening = new Screening($movie, $this->cinema);
			$screening->setLanguages($dubbing, null);
			$screening->setPrice($price);
			$screening->setLink($link);
			$screening->setShowtimes($datetimes);
			
			$this->screenings[] = $screening;
			
			$movieItems++;
			$days++;
		}
		
		$this->setScreenings($this->screenings);
		return $this->screenings;
	}
}
