<?php
namespace Zitkino\Parsers;

use Zitkino\Screenings\Screenings;

/**
 * Ahoy parser.
 */
class Ahoy extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->parse();
	}
	
	public function parse(): Screenings {
		$this->getContentFromDB(11);
	}
}
