<?php
namespace Zitkino;
use \Lib\database\Doctrine as DB;

/**
 * Cinema.
 */
class Cinema {
	private $id, $data;

	public function __construct($id) {
		$db = new DB(__DIR__."/../database.ini");
		$connection = $db->getConnection();
		
		if(is_numeric($id)) { $this->data = $connection->fetchAssoc("SELECT * FROM cinemas WHERE id = ?", array($id)); }
		else { $this->data = $connection->fetchAssoc("SELECT * FROM cinemas WHERE shortName = ?", array($id)); }
	}
	
	function getId() { return $this->id; }
	function getData() { return $this->data; }
	
	public function getMovies() {
		$parser = "\Zitkino\parsers\\".ucfirst($this->data["shortName"])."Parser";
		if(class_exists($parser)) {
			$pa = new $parser();
			return $pa->getMovies();
		} else { return null; }
	}
	
	public function getSoonestMovies() {
		$movies = $this->getMovies();
		$soonest = [];
		
		if(isset($movies)) {
			foreach($movies as $movie) {
				$currentDate = new \DateTime();
				
				$nextDate = new \DateTime();
				$nextDate->modify("+1 days");
				
				$datetimes = [];
				foreach($movie->getDatetimes() as $datetime) {
					// checks if movie is played from now to +1 day
					if($currentDate < $datetime and $datetime < $nextDate) {
						array_push($datetimes, $datetime);
					}
				}
				
				if(!empty($datetimes)) {
					$movie->setDatetimes($datetimes);
					array_push($soonest, $movie);
				}
			}
		}
		
		if(empty($soonest)) {
			if(is_null($movies) or empty($movies)) { $soonest = null; }
			else { $soonest = [$movies[0]]; }
		}
		
		return $soonest;
	}
}
