<?php
namespace Zitkino\parsers;
use DOMDocument, DOMXPath;

/**
 * Description of LucernaParser
 */
class LucernaParser extends Parser {
	private $url = "http://www.kinolucerna.info/index.php/program";
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
		
		$events = $xpath->query("//div[@id='icagenda']//div[@class='event']");
		$i = 0;
		foreach($events as $event) {
			$name = $xpath->query("//div[@class='eventtitle']//a", $event);
			//echo $name->item($i)->nodeValue;
			
			$nextdate = $xpath->query("//div[@class='nextdate']//strong", $event);
			$datetext = explode(",",$nextdate->item($i)->nodeValue);
			
			//$cz = array("ledna","února","března","dubna","května","června","července","srpna","září","října","listopadu","prosince");
			//$en = array("January","February","March","April","May","June","July","August","September","October","November","December");
			//$date = str_replace($cz, $en, $datetext[1]);
			//echo $date."<br>";
			//$datetime = \DateTime::createFromFormat(" j. F Y H:i", $date);
			//echo $datetime->format('d.m.Y H:i')."<br>";
			
			$this->movies[] = new \Zitkino\Movie($name->item($i)->nodeValue, $datetext[1]);
			$i++;
		}
	}
}
