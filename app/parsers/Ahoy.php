<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Ahoy parser.
 */
class Ahoy extends Parser {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->getConnection();
	}
	
	public function parse(): void {
		$screenings = $this->getContentFromDB(11);
		$this->cinema->setScreenings($screenings);
	}
}
