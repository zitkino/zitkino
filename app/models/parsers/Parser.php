<?php
namespace Zitkino\Parsers;

use DOMDocument, DOMXPath;
use Zitkino\Exceptions\ParserException;
use Zitkino\Movies\Movie;

/**
 * Parser.
 */
abstract class Parser {
	private $url = "";
	/** @var \DOMDocument */
	private $document;
	
	protected $movies = [];
	/** @var \Doctrine\DBAL\Connection */
	protected $connection;
	
	public function getUrl() {
		return $this->url;
	}
	public function getMovies() {
		return $this->movies;
	}
	
	public function setUrl($url) {
		$this->url = $url;
	}
	public function setMovies($movies) {
		$this->movies = $movies;
	}
	
	/**
	 * Initiates DOM document.
	 */
	public function initiateDocument() {
		$this->document = new DOMDocument("1.0", "UTF-8");
		$this->document->formatOutput = true;
		$this->document->preserveWhiteSpace = true;
	}
	
	/**
	 * Downloads data from internet.
	 * @return DOMXPath XPath document for parsing.
	 * @throws \Exception
	 */
	public function downloadData() {
		$handle = curl_init($this->url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
//        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        
		$html = curl_exec($handle);
		if($html === false) {
			$e = new ParserException(curl_error($handle));
			$e->setUrl($this->getUrl());
			throw $e;
		}
		
		curl_close($handle);
		
		libxml_use_internal_errors(true); // Prevent HTML errors from displaying
		$this->document->loadHTML(mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8"));
		$xpath = new DOMXPath($this->document);
		
		return $xpath;
	}
	
	public function getConnection() {
		$db = new \Lib\database\Doctrine(__DIR__."/../../config/database.ini");
		$this->connection = $db->getConnection();
	}

	/**
	 * Gets movies and other data from the web page.
	 * @return Movie[] Array of movies.
	 */
	abstract public function parse();
	
	public function getContentFromDB($cinema) {
		$today = date("Y-m-d", strtotime("now"));
		$events = $this->connection->fetchAll("SELECT s.*, m.*, l.czech AS dubbing, ls.czech AS subtitles FROM screenings AS s JOIN movies AS m ON s.movie = m.id
			 LEFT JOIN languages AS l ON s.dubbing = l.id LEFT JOIN languages AS ls ON s.subtitles = ls.id WHERE s.cinema = ? AND date >= ?", [$cinema, $today]);
		
		foreach($events as $event) {
			$datetimes = [];
			$datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $event["date"]." ".$event["time"]);
			$datetimes[] = $datetime;
			
			$movie = new \Zitkino\Movies\Movie($event["name"], $datetimes);
			$movie->setLink($event["link"]);
			$movie->setType($event["type"]);
			$movie->setDubbing($event["dubbing"]);
			$movie->setSubtitles($event["subtitles"]);
			$movie->setLength($event["length"]);
			$movie->setPrice($event["price"]);
			$movie->setCsfd($event["csfd"]);
			$movie->setImdb($event["imdb"]);
			$this->movies[] = $movie;
		}
		
		$this->setMovies($this->movies);
	}
}
