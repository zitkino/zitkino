<?php
namespace Zitkino;
use \Lib\database\Doctrine as DB;

/**
 * Cinema.
 */
class Cinema {
	private $id, $data, $movies;

	public function __construct($id) {
		$db = new DB(__DIR__."/../database.ini");
		$connection = $db->getConnection();
		
		if(is_numeric($id)) { $this->data = $connection->fetchAssoc("SELECT * FROM cinemas WHERE id = ?", array($id)); }
		else { $this->data = $connection->fetchAssoc("SELECT * FROM cinemas WHERE short_name = ?", array($id)); }
	}
	
	function getId() { return $this->id; }
	function getData() { return $this->data; }
	
	public function getMovies() {
		return $this->movies;
	}
	public function setMovies() {
		try {
			$parser = "\Zitkino\parsers\\".ucfirst($this->data["short_name"]);
			if(class_exists($parser)) {
				$pa = new $parser();
				
				$films = $pa->getMovies();
				foreach($films as $film) {
					if($this->checkActualMovie($film)) {
						$this->movies[] = $film;
					}
				}
			} else { $this->movies = null; }
		} catch(\Error $e) {
			\Tracy\Debugger::barDump($e);
			\Tracy\Debugger::log($e);
		}
	}
	
	public function hasMovies() {
		if(isset($this->movies) and !empty($this->movies)) { return true; }
		else { return false; }
	}
	
	public function getSoonestMovies() {
		$soonest = [];
		if(isset($this->movies)) {
			$currentDate = new \DateTime();
			
			foreach($this->movies as $movie) {
				$nextDate = new \DateTime();
				$nextDate->modify("+1 days");
				
				$datetimes = [];
				foreach($movie->getDatetimes() as $datetime) {
					// checks if movie is played from now to +1 day
					if($currentDate < $datetime and $datetime < $nextDate) {
						$datetimes[] = $datetime;
					}
				}
				
				if(!empty($datetimes)) {
					$movie->setDatetimes($datetimes);
					$soonest[] = $movie;
				}
			}
			
			if(count($soonest) < 5) {
				$soonest = [];
				for($i=0; $i<count($this->movies); $i++) {
					if(isset($this->movies[$i])) {
						foreach($this->movies[$i]->getDatetimes() as $datetime) {
							if($currentDate < $datetime) {
								$soonest[] = $this->movies[$i];
							}
						}
					}
					
					if(count($soonest) == 5) {
						break;
					}
				}
			}
		}
		
		if(empty($soonest)) {
			if(is_null($this->movies) or empty($this->movies)) { $soonest = null; }
			else {
				if($this->checkActualMovie($this->movies[0])) {
					$soonest = [$this->movies[0]];
				} else {
					$soonest = null;
				}
			}
		}
		
		return $soonest;
	}
	
	public function checkActualMovie(\Zitkino\Movie $movie) {
		$datetime = $movie->getDatetimes()[0];
		if($datetime > new \DateTime()) {
			return true;
		} else {
			return false;
		}
	}
}
