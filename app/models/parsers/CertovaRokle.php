<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;
use Zitkino\Screenings\Screenings;

/**
 * Certova rokle parser.
 */
class CertovaRokle extends Parser {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		$this->getConnection();
		$this->parse();
	}
	
	public function parse(): Screenings {
		return $this->getContentFromDB(10);
	}
}
