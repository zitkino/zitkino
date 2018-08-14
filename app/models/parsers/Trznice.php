<?php
namespace Zitkino\Parsers;

/**
 * Trznice parser.
 */
class Trznice extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->getContent();
	}
	
	public function getContent() {
		$this->getContentFromDB(16);
	}
}
