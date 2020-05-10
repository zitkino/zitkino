<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;
use Zitkino\Screenings\Screenings;

/**
 * Rubin parser.
 */
class Rubin extends Parser {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->setUrl("http://www.kdrubin.cz/program");
	}
	
	/**
	 * @return Screenings
	 * @throws \Exception
	 */
	public function parse(): void {
		$movies = [];
		
		$xpath = $this->getXpath();
		
		$events = $xpath->query("//div[@id='itemListLeading']//div[@class='K2ItemsRow']");
		foreach($events as $event) {
			$itemQuery = $xpath->query(".//h2[@class='tagItemTitle']/a", $event);
			$itemString = $itemQuery->item(0)->nodeValue;
			
			if(strpos($itemString, "Letní kino") !== false) {
				$nameQuery = $xpath->query(".//div[@class='catItemIntroText']/p/strong", $event);
				$name = $nameQuery->item(0)->nodeValue;
				
				$linkString = $itemQuery->item(0)->getAttribute("href");
				$link = "http://www.kdrubin.cz".$linkString;
				
				$datetimeQuery = $xpath->query(".//span[@class='catItemDateCreated']", $event);
				$datetimeString = $datetimeQuery->item(0)->nodeValue;
				$dateArray = explode(",", $datetimeString);
				
				$months = ["červenec", "červen", "srpen", "září"];
				$monthsNumbers = [7, 6, 8, 9];
				$date = trim(str_replace($months, $monthsNumbers, $dateArray[1]));
				
				$time = trim($dateArray[2]);
				$datetime = \DateTime::createFromFormat("d. m Y H:i", $date.$time);
				
				$movie = new Movie($name);
				
				$screening = new Screening($movie, $this->cinema);
				$screening->setLink($link);
				$screening->setShowtimes([$datetime]);
				
				$this->parserService->getEntityManager()->persist($screening);
				$this->cinema->addScreening($screening);
			}
		}
		
		$this->parserService->getEntityManager()->persist($this->cinema);
		$this->parserService->getEntityManager()->flush();
	}
}
