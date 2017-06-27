<?php
namespace Zitkino\parsers;

/**
 * Turany parser.
 */
class Turany extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->getContent();
	}
	
	public function getContent() {
		$this->getContentFromDB(14);
	}
}
