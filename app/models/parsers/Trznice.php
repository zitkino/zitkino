<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;
use Zitkino\Screenings\Screenings;

/**
 * Trznice parser.
 */
class Trznice extends Parser {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		$this->getConnection();
		$this->parse();
	}
	
	public function parse(): Screenings {
		$this->getContentFromDB(16);
	}
}
