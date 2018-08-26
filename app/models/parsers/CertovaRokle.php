<?php
namespace Zitkino\Parsers;

use Zitkino\Screenings\Screenings;

/**
 * Certova rokle parser.
 */
class CertovaRokle extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->parse();
	}
	
	public function parse(): Screenings {
		$this->getContentFromDB(10);
	}
}
