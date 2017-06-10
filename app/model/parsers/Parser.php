<?php
namespace Zitkino\parsers;
use DOMDocument, DOMXPath;

/**
 * Parser.
 */
abstract class Parser {
	private $url = "", $document;
	protected $movies = [], $connection;
	
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
	 */
	public function downloadData() {
		$handle = curl_init($this->url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_ENCODING, "UTF-8");
		$html = curl_exec($handle);
		libxml_use_internal_errors(true); // Prevent HTML errors from displaying
		$this->document->loadHTML(mb_convert_encoding($html, "HTML-ENTITIES", "UTF-8"));
		$xpath = new DOMXPath($this->document);
		
		return $xpath;
	}
	
	public function getConnection() {
		$db = new \Lib\database\Doctrine(__DIR__."/../../database.ini");
		$this->connection = $db->getConnection();
	}

	/**
	 * Gets movies and other data from the web page.
	 */
	abstract public function getContent();
	
	public function getContentFromDB($cinema) {
		$today = date("Y-m-d", strtotime("now"));
		$events = $this->connection->fetchAll("SELECT s.*, m.*, l.czech AS language, ls.czech AS subtitles FROM screenings AS s JOIN movies AS m ON s.movie = m.id
			 LEFT JOIN languages AS l ON s.language = l.id LEFT JOIN languages AS ls ON s.subtitles = ls.id WHERE s.cinema = ? AND date >= ?", [$cinema, $today]);
		
		foreach($events as $event) {
			$datetimes = [];
			$datetime = \DateTime::createFromFormat("Y-m-d H:i:s", $event["date"]." ".$event["time"]);
			$datetimes[] = $datetime;
			
			$this->movies[] = new \Zitkino\Movie($event["name"], $datetimes);
			$this->movies[count($this->movies)-1]->setLink($event["link"]);
			$this->movies[count($this->movies)-1]->setCsfd($event["csfd"]);
			$this->movies[count($this->movies)-1]->setImdb($event["imdb"]);
			$this->movies[count($this->movies)-1]->setType($event["type"]);
			$this->movies[count($this->movies)-1]->setLanguage($event["language"]);
			$this->movies[count($this->movies)-1]->setSubtitles($event["subtitles"]);
		}
		
		$this->setMovies($this->movies);
	}
}
