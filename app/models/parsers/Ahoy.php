<?php
namespace Zitkino\Parsers;

/**
 * Ahoy parser.
 */
class Ahoy extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->getContent();
	}
	
	public function getContent() {
		$this->getContentFromDB(11);
	}
}
