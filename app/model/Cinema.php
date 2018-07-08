<?php
namespace Zitkino;
use \Lib\database\Doctrine as DB;

/**
 * Cinema.
 */
class Cinema {
	private $id, $data;
	/** @var \Zitkino\Movie[] */
	private $movies;

	public function __construct($id) {
		$db = new DB(__DIR__."/../config/database.ini");
		/** @var \Doctrine\DBAL\Connection $connection */
		$connection = $db->getConnection();
		
		if(is_numeric($id)) {
			$column = "id";
		} else {
			$column = "short_name";
		}
		$this->data = $connection->fetchAssoc("SELECT * FROM cinemas WHERE $column = ?", [$id]);
	}
	
	function getId() { return $this->id; }
	function getData() { return $this->data; }
	
	public function getMovies() {
		return $this->movies;
	}
	
	public function setMovies() {
		try {
			$parserClass = "\Zitkino\parsers\\".ucfirst($this->data["short_name"]);
			if(class_exists($parserClass)) {
				/** @var \Zitkino\parsers\Parser $parser */
				$parser = new $parserClass();
				
				$films = $parser->getMovies();
				if(isset($films)) {
					foreach($films as $film) {
						if($this->checkActualMovie($film)) {
							$this->movies[] = $film;
						}
					}
				}
			} else { $this->movies = null; }
		} catch(\Error $error) {
			\Tracy\Debugger::barDump($error);
			\Tracy\Debugger::log($error, \Tracy\Debugger::ERROR);
		} catch(\Exception $exception) {
			\Tracy\Debugger::barDump($exception);
			\Tracy\Debugger::log($exception, \Tracy\Debugger::EXCEPTION);
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
