<?php
namespace Zitkino\parsers;

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
			
			if($itemString == "Židenické letní kino na Dělňáku") {
				$name = $dubbing = $length = $csfd = null;
				
				$details = $xpath->query(".//div//p", $event);
				foreach($details as $detail) {
					if(strpos($detail->nodeValue, "min.") !== false) {
						$nameQuery = $xpath->query(".//strong", $detail);
						$nameString = $nameQuery->item($nameQuery->length-1)->nodeValue;
						$name = str_replace(["film ", "21:00"], "", $nameString);
						
						$matches = [];
						preg_match_all("/\((.*?)\)/", $detail->nodeValue, $matches);
						$data = explode(",", $matches[1][0]);
						
						if(strpos($data[0], "CZ") !== false) {
							$dubbing = "česky";
						}
						
						$length = str_replace("min.", "", $data[2]);
						
						$csfdQuery = $xpath->query(".//a", $detail);
						$csfdString = $csfdQuery->item(0)->getAttribute("href");
						$csfd = str_replace(["https://www.csfd.cz/film/", "/prehled/", "/komentare/"], "", $csfdString);
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
				$price = str_replace(["Vstupné: ", "Kč"], "", $priceString);
				
				$movie = new \Zitkino\Movie($name, $datetimes);
				$movie->setLink($link);
				$movie->setDubbing($dubbing);
				$movie->setLength($length);
				$movie->setPrice($price);
				$movie->setCsfd($csfd);
				$this->movies[] = $movie;
			}
			
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
