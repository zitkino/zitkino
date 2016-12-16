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
		$movieItems = 0;
		foreach($events as $event) {
			$nameQuery = $xpath->query("//div[@class='eventtitle']//a", $event);
			$name = $nameQuery->item($movieItems)->nodeValue;
			
			$badNames = array("KINO NEHRAJE – Vánoční prázdniny", "KINO NEHRAJE – Novoroční prázdniny");
			if(in_array($name, $badNames)) {
				$movieItems++;
				continue;
			}
			
			$link = "http://www.kinolucerna.info".$nameQuery->item($movieItems)->getAttribute("href");
			
			$languageQuery = $xpath->query(".//div[@class='descshort']", $event);
			$languageString = $languageQuery->item(0)->nodeValue;
			$language = "česky";
			$subtitles = null;
			if(strpos(strtolower($languageString), "dabing") !== false) {
				$language = "česky";
			}
			if(strpos($languageString, "Titulky") !== false) {
				$language = "anglicky";
				$subtitles = "české";
			}
			
			$dateQuery = $xpath->query("//div[@class='nextdate']//strong", $event);
			$datetext = explode(",", $dateQuery->item($movieItems)->nodeValue);
			
			$cz = array("leden","únor","březen","duben","květen","červen","červenec","srpen","září","říjen","listopad","prosinec");
			$en = array("January","February","March","April","May","June","July","August","September","October","November","December");
			$date = str_replace($cz, $en, $datetext[1]);
			
			$datetimes =[];
			$datetime = \DateTime::createFromFormat(" j. F Y H:i", $date);
			$datetimes[] = $datetime;
			
			$this->movies[] = new \Zitkino\Movie($name, $datetimes);
			$this->movies[count($this->movies)-1]->setLink($link);
			$this->movies[count($this->movies)-1]->setLanguage($language);
			$this->movies[count($this->movies)-1]->setSubtitles($subtitles);
			$movieItems++;
		}
		
		$this->setMovies($this->movies);
	}
}
