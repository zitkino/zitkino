<?php
namespace Zitkino\parsers;

/**
 * Olympia Parser.
 */
class OlympiaParser extends CinemaCityParser {
	public function __construct() {
		$this->setCinemaId("1010103");
		
		parent::__construct();
	}
}
