<?php
namespace Zitkino\parsers;
use DOMDocument, DOMXPath;

/**
 * Kinokavarna Parser.
 */
class KinokavarnaParser extends Parser {
	private $url = "http://www.kinokavarna.cz/program.html";
	private $doc;
	private $movies = [];
	
	function getUrl() {
		return $this->url;
	}
	function getMovies() {
		return $this->movies;
	}

	function setUrl($url) {
		$this->url = $url;
	}
	function setMovies($movies) {
		$this->movies = $movies;
	}
	
	public function __construct() {
		$this->doc = new DOMDocument("1.0", "UTF-8");
		$this->doc->formatOutput = true;
		$this->doc->preserveWhiteSpace = true;
		
		$this->getContent();
	}
	
	public function getContent() {
		$handle = curl_init($this->url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		$html = curl_exec($handle);
		libxml_use_internal_errors(true); // Prevent HTML errors from displaying
		$this->doc->loadHTML($html);
		$xpath = new DOMXPath($this->doc);
		
		$movieQuery = "//div[@class='aktuality'][count(p)>4]";
		$events = $xpath->query("//div[@id='content-in']".$movieQuery);
		
		$i = 0;
		foreach($events as $event) {
			$movieEvent = $xpath->query("//p[@class='MsoNormal']", $event);
			if($movieEvent->length>4) {
				$nameQuery = $xpath->query($movieQuery."//h4", $event);
				$name = mb_substr($nameQuery->item($i)->nodeValue, 10);
				
				$date = $xpath->query($movieQuery."//h4//span", $event);
				$timeQuery = $xpath->query($movieQuery."//p[@class='start']", $event);
				$time = mb_substr($timeQuery->item($i)->nodeValue, 9, 5);
				$time = str_replace(".", ":", $time);
				
				$datetime = \DateTime::createFromFormat("j.n.Y", $date->item($i)->nodeValue);
				$datetime->setTime(intval(substr($time, 0, 2)), intval(substr($time, 3, 2)));
				
				$this->movies[] = new \Zitkino\Movie($name, $datetime);
				$this->movies[count($this->movies)-1]->setLink($this->url);
			}
			$i++;
		}
	}
}
