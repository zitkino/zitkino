<?php
namespace Zitkino\parsers;

/**
 * Lucerna Parser.
 */
class LucernaParser extends Parser {
	public function __construct() {
		$this->setUrl("http://www.kinolucerna.info/index.php/program");
		$this->initiateDocument();
		
		$this->getContent();
	}
	
	public function getContent() {
		$xpath = $this->downloadData();
		
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
		
		$this->setMovies($this->movies);
	}
}
