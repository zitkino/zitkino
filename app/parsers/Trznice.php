<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Trznice parser.
 */
class Trznice extends Parser {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->getConnection();
	}
	
	public function parse(): void {
		$screenings = $this->getContentFromDB(16);
		$this->cinema->setScreenings($screenings);
	}
}
