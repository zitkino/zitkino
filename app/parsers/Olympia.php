<?php
namespace Zitkino\Parsers;

use Zitkino\Cinemas\Cinema;

/**
 * Olympia parser.
 */
class Olympia extends CinemaCity {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema, "1034");
	}
}
