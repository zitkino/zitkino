<?php
namespace Zitkino\Parsers;

/**
 * Velky Spalicek parser.
 */
class VelkySpalicek extends CinemaCity {
	public function __construct() {
		$this->setCinemaId("1010107");
		
		parent::__construct();
	}
}
