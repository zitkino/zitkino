<?php
namespace Zitkino\parsers;

/**
 * Certova rokle parser.
 */
class CertovaRokle extends Parser {
	public function __construct() {
		$this->getConnection();
		$this->getContent();
	}
	
	public function getContent() {
		$today = date("Y-m-d", strtotime("now"));
		$events = $this->connection->fetchAll("SELECT s.*, m.*, l.czech AS language, ls.czech AS subtitles FROM screenings AS s JOIN movies AS m ON s.movie = m.id
			 LEFT JOIN languages AS l ON s.language = l.id LEFT JOIN languages AS ls ON s.subtitles = ls.id WHERE s.cinema = 10 AND date >= ?", [$today]);
		
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
