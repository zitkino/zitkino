<?php
namespace Zitkino\Parsers;

use Dobine\Connections\DBAL;
use Doctrine\DBAL\{Connection, DBALException};
use DOMDocument;
use DOMXPath;
use Nette\Utils\{Json, JsonException};
use Zitkino\Cinemas\Cinema;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;
use Zitkino\Screenings\{Screening, Screenings, ScreeningType};

/**
 * Parser.
 */
abstract class Parser {
	/** @var Cinema */
	protected $cinema;
	
	private $url = "";
	
	/** @var Connection */
	protected $connection;
	
	/** @var ParserService */
	protected $parserService;
	
	/**
	 * Parser constructor.
	 * @param ParserService $parserService
	 * @param Cinema $cinema
	 */
	public function __construct(ParserService $parserService, Cinema $cinema) {
		$this->parserService = $parserService;
		$this->cinema = $cinema;
	}
	
	public function getUrl() {
		return $this->url;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
	
	/**
	 * @param Screenings|array|null $screenings
	 * @return Parser
	 */
	public function setScreenings($screenings) {
		if(!isset($screenings)) {
			$screenings = [];
		}
		
		if(is_array($screenings)) {
			$this->screenings = new Screenings($screenings);
		} else {
			$this->screenings = $screenings;
		}
		
		return $this;
	}
	
	/**
	 * Downloads data from internet.
	 * @return DOMXPath XPath document for parsing.
	 * @throws ParserException
	 */
	private function downloadData() {
		$handle = curl_init($this->url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_ENCODING, "UTF-8");
		curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		
		$data = curl_exec($handle);
		if($data === false) {
			$e = new ParserException(curl_error($handle));
			$e->setUrl($this->getUrl());
			throw $e;
		}
		
		curl_close($handle);
		return $data;
	}
	
	/**
	 * @return DOMXPath
	 * @throws ParserException
	 */
	public function getXpath() {
		$data = $this->downloadData();
		libxml_use_internal_errors(true); // Prevent HTML errors from displaying
		
		$document = new DOMDocument("1.0", "UTF-8");
		$document->formatOutput = true;
		$document->preserveWhiteSpace = true;
		
		$html = mb_convert_encoding($data, "HTML-ENTITIES", "UTF-8");
		$document->loadHTML($html);
		
		$xpath = new DOMXPath($document);
		return $xpath;
	}
	
	/**
	 * @return array
	 * @throws ParserException
	 * @throws JsonException
	 */
	public function getJson() {
		$data = $this->downloadData();
		
		return Json::decode($data, Json::FORCE_ARRAY);
	}
	
	/**
	 * @throws DBALException
	 */
	public function getConnection() {
		$db = new DBAL();
		$this->connection = $db->connectFromFile(__DIR__."/../config/".$_ENV["APP_ENV"].".neon", "dbal.connection");
//		$this->connection = $db->getConnection();
	}
	
	/**
	 * Gets movies and other data from the web page.
	 */
	abstract public function parse(): void;
	
	public function getContentFromDB($cinema) {
		$today = date("Y-m-d", strtotime("now"));
		$events = $this->connection->fetchAll("
			SELECT s.*, m.*, l.czech AS dubbing, ls.czech AS subtitles, stype.name as type, st.datetime FROM screenings AS s
			LEFT JOIN movies AS m ON s.movie = m.id
			LEFT JOIN languages AS l ON s.dubbing = l.id
			LEFT JOIN languages AS ls ON s.subtitles = ls.id
			LEFT JOIN screenings_types AS stype ON stype.id = s.type
			LEFT JOIN showtimes AS st ON st.screening = s.id
			WHERE s.cinema = ? AND st.datetime >= ?",
			[$cinema, $today]
		);
		
		foreach($events as $event) {
			$datetimes = [];
			$datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $event["datetime"]);
			$datetimes[] = $datetime;
			
			$movie = new Movie($event["name"]);
			$movie->setLength($event["length"]);
			$movie->setCsfd($event["csfd"]);
			$movie->setImdb($event["imdb"]);
			
			$screening = new Screening($movie, $this->cinema);
			if(isset($event["type"])) {
				$screening->setType(new ScreeningType($event["type"]));
			}
			$screening->setLanguages($event["dubbing"], $event["subtitles"]);
			$screening->setPrice($event["price"]);
			$screening->setLink($event["link"]);
			$screening->setShowtimes($datetimes);
			
			$movie->addScreening($screening);
			$this->screenings[] = $screening;
		}
		
		$this->setScreenings($this->screenings);
		return $this->screenings;
	}
}
