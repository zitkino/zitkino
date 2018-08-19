<?php
namespace Zitkino\Parsers;

/**
 * Ahoy parser.
 */
class Ahoy extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->parse();
	}
	
	public function parse() {
		$this->getContentFromDB(11);
	}
}
