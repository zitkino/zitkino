<?php
namespace Zitkino;
use \Lib\database\Doctrine as DB;

/**
 * Cinemas.
 */
class Cinemas {
	private $all = [], $classic = [], $summer = [];
	
	function getAll() {
		return $this->all;
	}
	function getAllWithMovies() {
		$cinemas = [];
		foreach($this->all as $cinema) {
			$cinema->setMovies();
			if($cinema->hasMovies()) {
				$cinemas[] = $cinema;
			}
		}
		return $cinemas;
	}
	
	function getClassic() {
		return $this->classic;
	}
	function getSummer() {
		return $this->summer;
	}

	public function __construct() {
		$db = new DB(__DIR__."/../database.ini");
		$connection = $db->getConnection();
		
		$cinemas = $connection->fetchAll("SELECT id FROM cinemas WHERE active_until IS null ORDER BY short_name");
		foreach($cinemas as $c) {
			$cinema = new \Zitkino\Cinema($c["id"]);
			array_push($this->all, $cinema);
			
			$data = $cinema->getData();
			switch($data["type"]) {
				case "classic":
					array_push($this->classic, $cinema);
					break;
				case "summer":
					array_push($this->summer, $cinema);
					break;
				default:
					break;
			}
		}
	}
}
