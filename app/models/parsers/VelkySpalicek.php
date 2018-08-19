<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Velky Spalicek parser.
 */
class VelkySpalicek extends CinemaCity {
	public function __construct(Cinema $cinema) {
		$this->cinema = $cinema;
		$this->setCinemaId("1010107");
		
		parent::__construct($cinema);
	}
}
