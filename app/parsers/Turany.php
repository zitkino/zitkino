<?php
namespace Zitkino\Parsers;

use Doctrine\DBAL\DBALException;
use Zitkino\Cinemas\Cinema;

/**
 * Turany parser.
 */
class Turany extends Parser {
	/**
	 * Turany constructor.
	 * @param ParserService $parserService
	 * @param Cinema $cinema
	 * @throws DBALException
	 */
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->getConnection();
	}
	
	public function parse(): void {
		$screenings = $this->getContentFromDB(14);
		$this->cinema->setScreenings($screenings);
	}
}
