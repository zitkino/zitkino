<?php
namespace _nette\app\parsers;

use _nette\app\parsers\CinemaCity;
use _nette\app\parsers\ParserService;
use _nette\app\Models\cinemas\Cinema;

/**
 * Olympia parser.
 */
class Olympia extends CinemaCity {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema, "1034");
	}
}
