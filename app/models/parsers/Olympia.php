<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Olympia parser.
 */
class Olympia extends CinemaCity {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		$this->setCinemaId("1010103");
		
		parent::__construct($cinema);
	}
}
