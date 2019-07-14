<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Olympia parser.
 */
class Olympia extends CinemaCity {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		
		parent::__construct($cinema, "1034");
	}
}
