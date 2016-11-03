<?php
namespace Zitkino\parsers;
use DOMDocument, DOMXPath;

/**
 * Lucerna Parser.
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
			$nameQuery = $xpath->query("//div[@class='eventtitle']//a", $event);
			
			$link = "http://www.kinolucerna.info".$nameQuery->item($i)->getAttribute("href");
			
			$dateQuery = $xpath->query("//div[@class='nextdate']//strong", $event);
			$datetext = explode(",", $dateQuery->item($i)->nodeValue);
			
			$cz = array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");
			$en = array("January","February","March","April","May","June","July","August","September","October","November","December");
			$date = str_replace($cz, $en, $datetext[1]);
			$datetime = \DateTime::createFromFormat(" j. F Y H:i", $date);
			
			$this->movies[] = new \Zitkino\Movie($nameQuery->item($i)->nodeValue, $datetime);
			$this->movies[count($this->movies)-1]->setLink($link);
			$i++;
		}
	}
}
