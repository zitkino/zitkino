<?php
namespace Zitkino\parsers;

/**
 * Certova rokle parser.
 */
class CertovaRokle extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->getContent();
	}
	
	public function getContent() {
		$this->getContentFromDB(10);
	}
}