<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Certova rokle parser.
 */
class CertovaRokle extends Parser {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->getConnection();
	}
	
	public function parse(): void {
		$screenings = $this->getContentFromDB(10);
		$this->cinema->setScreenings($screenings);
	}
}
