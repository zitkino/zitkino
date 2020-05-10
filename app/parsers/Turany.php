<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Turany parser.
 */
class Turany extends Parser {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->getConnection();
	}
	
	public function parse(): void {
		$screenings = $this->getContentFromDB(14);
		$this->cinema->setScreenings($screenings);
	}
}
