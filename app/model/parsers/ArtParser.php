<?php
namespace Zitkino\parsers;

/**
 * Art Parser.
 */
class ArtParser extends Parser {
	public function __construct() {
		$this->setUrl("http://kinoart.cz/program/");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$this->getHall($xpath, "leftcol");
		$this->getHall($xpath, "rightcol");
	}
	
	/**
	 * Parses data for selected hall.
	 * @param DOMXPath $xpath XPath document for parsing.
	 * @param string $which ID which hall to parse.
	 */
	public function getHall($xpath, $which) {
		$events = $xpath->query("//div[@id='content']//div[@class='".$which."']//tr");
		$movieItems = 0;
		foreach($events as $event) {
			$datetimes = [];
			
			$nameQuery = $xpath->query(".//td[@class='movie']//a", $event);
			$name = $nameQuery->item(0)->nodeValue;
			
			$link = $nameQuery->item(0)->getAttribute("href");
			
			$dateQuery = $xpath->query("//td[@class='date']", $event);
			$date = mb_substr($dateQuery->item($movieItems)->nodeValue, 3);
			$datetime = \DateTime::createFromFormat("j.m.H:i", $date);
			$datetimes[] = $datetime;
			
			$this->movies[] = new \Zitkino\Movie($name, $datetimes);
			$this->movies[count($this->movies)-1]->setLink($link);
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
