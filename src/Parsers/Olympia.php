<?php
namespace App\Parsers;

use App\Models\Cinema\Cinema;
use App\Services\ParserService;

/**
 * Olympia parser.
 */
class Olympia extends CinemaCity {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema, "1034");
	}
}
