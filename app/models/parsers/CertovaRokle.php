<?php
namespace Zitkino\Parsers;

use Zitkino\Movies\Screenings;

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
