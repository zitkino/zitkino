<?php
namespace Zitkino\parsers;

/**
 * Spilberk parser.
 */
class Spilberk extends Parser {
	public function __construct() {
		$this->setUrl("http://www.letnikinospilberk.cz");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
		$events = $xpath->query("//div[@id='page-program']/div[@class='film']");
		$days = 0;
		$movieItems = 0;
		foreach($events as $event) {
			$nameQuery = $xpath->query("//div[@class='right']//h2", $event);
			$name = $nameQuery->item($movieItems)->nodeValue;

			$csfdQuery = $xpath->query("//a[@class='vice']", $event);
			$csfdString = $csfdQuery->item($movieItems)->getAttribute("href");
			$csfd = str_replace("https://www.csfd.cz/film/", "", $csfdString);
			
//			$language = null;
//			if(\Lib\Strings::endsWith($name, "- cz dabing")) {
//				$language = "česky";
//				$name = str_replace(" - cz dabing", "", $name);
//			}
			
			$dateQuery = $xpath->query("//div[@class='left']//p", $event);
			$dateString = $dateQuery->item($movieItems)->nodeValue;
			$date = rtrim(substr($dateString, 0, 6));
			
			$timeString = substr($dateString, -5);
			$time = explode(":", $timeString);
			
			$datetime = \DateTime::createFromFormat("j. n.", $date);
			$datetime->setTime(intval($time[0]), intval($time[1]));
			$datetimes = [$datetime];
			
			$this->movies[] = new \Zitkino\Movie($name, $datetimes);
			$this->movies[count($this->movies)-1]->setCsfd($csfd);
			//$this->movies[count($this->movies) - 1]->setLanguage($language);
			$movieItems++;
			$days++;
		}
		
		$this->setMovies($this->movies);
	}
}
