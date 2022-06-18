<?php

namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Zbrojovka parser.
 */
class Zbrojovka extends Art {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->setUrl("https://www.kinoart.cz/cs/program/zbrojovka");
	}
}
