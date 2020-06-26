<?php
namespace Zitkino\Parsers;

use Doctrine\DBAL\DBALException;
use Zitkino\Cinemas\Cinema;

/**
 * Certova rokle parser.
 */
class CertovaRokle extends Parser {
	/**
	 * CertovaRokle constructor.
	 * @param ParserService $parserService
	 * @param Cinema $cinema
	 * @throws DBALException
	 */
	public function __construct(ParserService $parserService, Cinema $cinema) {
		parent::__construct($parserService, $cinema);
		$this->getConnection();
	}
	
	public function parse(): void {
		$screenings = $this->getContentFromDB(10);
		$this->cinema->setScreenings($screenings);
	}
}
