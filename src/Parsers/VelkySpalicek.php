<?php
namespace App\Parsers;

use App\Entities\Cinema;
use App\Services\ParserService;

/**
 * Velky Spalicek parser.
 */
class VelkySpalicek extends CinemaCity {
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema, "1035");
	}
}
