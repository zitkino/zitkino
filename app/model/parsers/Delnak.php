<?php
namespace Zitkino\Parsers;

/**
 * Delnak parser.
 */
class Delnak extends Parser {
	public function __construct() {
		$this->setUrl("http://www.delnickydumbrno.cz/cely-program.html");
		$this->initiateDocument();
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$movieItems = 0;
		$events = $xpath->query("//div[@id='content-in']//div[@class='aktuality']//div[@class='content']");
		foreach($events as $event) {
			$itemQuery = $xpath->query("//h4//a", $event);
			$itemString = $itemQuery->item($movieItems)->nodeValue;
			
			if(strpos($itemString, "Letní kino") !== false) {
				$name = str_replace("Letní kino - ", "", $itemString);
				$dubbing = $length = $csfd = null;
				
				$details = $xpath->query(".//div//p", $event);
				foreach($details as $detail) {
					if(strpos($detail->nodeValue, "min.") !== false) {
						$matches = [];
						preg_match_all("/\((.*?)\)/", $detail->nodeValue, $matches);
						$data = explode(",", $matches[1][0]);
						
						$dubbing = null;
						if(strpos($data[0], "CZ") !== false) {
							$dubbing = "česky";
						}
						
						$subtitles = null;
						if(isset($data[3])) {
							if(strpos($data[3], "české titulky") !== false) {
								$subtitles = "české";
							}
						}
						
						$length = str_replace("min.", "", $data[2]);
					}
				}
				
				$link = "http://www.delnickydumbrno.cz".$itemQuery->item($movieItems)->getAttribute("href");
				
				$dateQuery = $xpath->query("//p[@class='date']", $event);
				$date = $dateQuery->item($movieItems)->nodeValue;
				
				$timeQuery = $xpath->query("//p[@class='start']", $event);
				$time = explode(":", $timeQuery->item($movieItems)->nodeValue);
				
				$datetime = \DateTime::createFromFormat("j.m.Y", $date);
				$datetime->setTime(intval($time[0]), intval($time[1]));
				$datetimes = [$datetime];
				
				$priceQuery = $xpath->query("//p[@class='entry']", $event);
				$priceString = $priceQuery->item($movieItems)->nodeValue;
				$price = str_replace(["Vstupné: ", " Kč"], "", $priceString);
				
				$movie = new \Zitkino\Movies\Movie($name, $datetimes);
				$movie->setLink($link);
				$movie->setDubbing($dubbing);
				$movie->setSubtitles($subtitles);
				$movie->setLength($length);
				$movie->setPrice($price);
				$this->movies[] = $movie;
			}
			
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
