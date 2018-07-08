<?php
namespace Zitkino;
use \Lib\database\Doctrine as DB;

/**
 * Cinemas.
 */
class Cinemas {
	private $all = [], $classic = [], $multiplex = [], $summer = [];
	
	function getAll() {
		return $this->all;
	}
	function getClassic() {
		return $this->classic;
	}
	function getMultiplex() {
		return $this->multiplex;
	}
	function getSummer() {
		return $this->summer;
	}
	
	function getWithMovies($type="all") {
		$cinemas = [];
		foreach($this->{$type} as $cinema) {
			$cinema->setMovies();
			if($cinema->hasMovies()) {
				$cinemas[] = $cinema;
			}
		}
		return $cinemas;
	}

	public function __construct() {
		$db = new DB(__DIR__."/../config/database.ini");
		$connection = $db->getConnection();
		
		$cinemas = $connection->fetchAll("SELECT id FROM cinemas WHERE active_until IS null ORDER BY short_name");
		foreach($cinemas as $c) {
			$cinema = new \Zitkino\Cinema($c["id"]);
			array_push($this->all, $cinema);
			
			$data = $cinema->getData();
			switch($data["type"]) {
				case "classic": array_push($this->classic, $cinema); break;
				case "multiplex": array_push($this->multiplex, $cinema); break;
				case "summer": array_push($this->summer, $cinema); break;
				default: break;
			}
		}
	}
}
