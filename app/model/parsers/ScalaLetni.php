<?php
namespace Zitkino\parsers;

/**
 * Scalní letňák parser.
 */
class ScalaLetni extends Parser {
	public function __construct() {
		$this->setUrl("http://www.kinoscala.cz/cz/cyklus/scalni-letnak-251");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$events = $xpath->query("//div[@id='program']/table//tr");
		$days = 0;
		$movieItems = 0;
		foreach($events as $event) {
			$nameQuery = $xpath->query("//td[@class='col_movie_name']//a", $event);
			$nameString = $nameQuery->item($movieItems)->nodeValue;
			$name = str_replace("feat. Kmeny90/BU2R", "", $nameString);

			$link = "http://www.kinoscala.cz".$nameQuery->item($movieItems)->getAttribute("href");
			
			$language = null;
			if(\Lib\Strings::endsWith($name, "- cz dabing")) {
				$language = "česky";
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
			
			$this->movies[] = new \Zitkino\Movie($name, $datetimes);
			$this->movies[count($this->movies)-1]->setLink($link);
			$this->movies[count($this->movies)-1]->setLanguage($language);
			$this->movies[count($this->movies)-1]->setPrice($price);
			$movieItems++;
			$days++;
		}
		
		$this->setMovies($this->movies);
	}
}
