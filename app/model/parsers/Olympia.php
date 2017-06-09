<?php
namespace Zitkino\parsers;

/**
 * Olympia parser.
 */
class Olympia extends CinemaCity {
	public function __construct() {
		$this->setCinemaId("1010103");
		
		parent::__construct();
	}
}
