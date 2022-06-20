<?php
namespace Zitkino\Parsers;

use Doctrine\ORM\{OptimisticLockException, ORMException};
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\Screening;

/**
 * Rubin parser.
 */
class Rubin extends Parser {
	/**
	 * @throws ParserException
	 * @throws ORMException
	 * @throws OptimisticLockException
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
				
				$linkString = $itemQuery->item(0)->attributes->getNamedItem("href")->nodeValue;
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
				$screening->setLink($link)
					->setShowtimes([$datetime]);
				
				$this->parserService->getScreeningFacade()->save($screening);
				$this->cinema->addScreening($screening);
			}
		}
		
		$this->cinema->setParsed(new \DateTime());
		$this->parserService->getCinemaFacade()->save($this->cinema);
	}
}
