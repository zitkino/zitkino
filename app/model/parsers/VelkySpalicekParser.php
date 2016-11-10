<?php
namespace Zitkino\parsers;

/**
 * Velky Spalicek Parser.
 */
class VelkySpalicekParser extends CinemaCityParser {
	public function __construct() {
		$this->setCinemaId("1010107");
		
		parent::__construct();
	}
}
