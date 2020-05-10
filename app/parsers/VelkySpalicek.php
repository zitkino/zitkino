<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Velky Spalicek parser.
 */
class VelkySpalicek extends CinemaCity {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema, "1035");
	}
}
